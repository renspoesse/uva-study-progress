<template>

    <div>

        <loading-overlay v-show="loading"></loading-overlay>

        <div class="dashhead">
            <div class="dashhead-titles">
                <h2 class="dashhead-subtitle">{{ displayName }}</h2>
                <h2 class="dashhead-title">Personalise</h2>
            </div>
        </div>

        <hr class="m-t">

        <alert type="success" v-bind:message="successMessage" v-bind:show="successMessage" v-on:close="successMessage = ''"></alert>
        <alert type="danger" v-bind:message="errorMessage" v-bind:show="errorMessage" v-on:close="errorMessage = ''"></alert>

        <form v-on:submit.prevent="submit" class="form-horizontal">

            <div v-bind:class="['form-group']" v-if="hasAnyRole(user, [roles.StudyAdviser, roles.Administrator])">
                <label class="col-sm-3 control-label">Roles (not visible to student)</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" v-bind:value="userRoles.join(', ')" disabled>
                </div>
            </div>

            <div v-bind:class="['form-group', hasError('first_year_credits_goal') ? 'has-feedback has-error' : '']" v-if="student.id && student.year === 1">
                <label class="col-sm-3 control-label">1st year credits goal</label>
                <div class="col-sm-9">
                    <input type="number" class="form-control" min="0" max="100" v-model="student.first_year_credits_goal" v-bind:disabled="!canEdit">
                    <span class="help-block" v-show="getValidationErrors('first_year_credits_goal')">{{ getValidationErrors('first_year_credits_goal') }}</span>
                </div>
            </div>

            <div v-bind:class="['form-group', hasError('second_year_credits_goal') ? 'has-feedback has-error' : '']" v-if="student.id && student.year === 2">
                <label class="col-sm-3 control-label">2nd year credits goal</label>
                <div class="col-sm-9">
                    <input type="number" class="form-control" min="0" max="100" v-model="student.second_year_credits_goal" v-bind:disabled="!canEdit">
                    <span class="help-block" v-show="getValidationErrors('second_year_credits_goal')">{{ getValidationErrors('second_year_credits_goal') }}</span>
                </div>
            </div>

            <div v-bind:class="['form-group', hasError('program_satisfaction') ? 'has-feedback has-error' : '']" v-if="student.id && student.year === 1">
                <label class="col-sm-3 control-label">How satisfied are you with your choice of program? (1-10)</label>
                <div class="col-sm-9">
                    <input type="number" class="form-control" min="1" max="10" v-model="student.program_satisfaction" v-bind:disabled="!canEdit">
                    <span class="help-block" v-show="getValidationErrors('program_satisfaction')">{{ getValidationErrors('program_satisfaction') }}</span>
                </div>
            </div>

            <div class="form-group" v-if="student.id && canEdit">
                <div class="col-sm-3"></div>
                <div class="col-sm-9" v-show="hasBackgroundProcesses">
                    <i class="fa fa-spinner fa-spin m-r"></i>Wait one second please while background processes are finishing.
                </div>
                <div class="col-sm-9" v-show="!hasBackgroundProcesses">
                    <div class="btn-group">
                        <a v-on:click.prevent="handleSubmit" class="btn btn-primary-outline">Save</a>
                    </div>
                </div>
            </div>

        </form>

    </div>

</template>

<script>

    import * as _ from 'lodash';
    import {mapGetters, mapState} from 'vuex';
    import backgroundProcesses from '../../mixins/background_processes';
    import errorAlerts from '../../mixins/error_alerts';
    import loadingOverlay from '../../mixins/loading_overlay';
    import * as roles from '../../helpers/roles';
    import * as students from '../../services/students';

    export default {

        computed: {

            ...mapGetters({

                displayName: 'auth/displayName',
                userRoles: 'auth/roles',
            }),
            ...mapState({

                user: (state) => state.auth.user,
                viewAs: (state) => state.auth.viewAs,
            }),
            canEdit: function() {

                return (!this.viewAs || this.hasAnyRole(this.user, [this.roles.Administrator]));
            },
        },
        created() {

            this.fetchStudent();
        },
        data: function() {

            return {

                student: {},
            };
        },
        methods: {

            fetchStudent: function() {

                this.displayErrors(false);

                if (this.viewAs) {

                    students.getById(this.viewAs.id).then(result => this.student = result.item);
                }
                else {

                    students.getByAuthenticated()
                        .then(result => {

                            this.student = result.item;
                        })
                        .catch(() => {

                            // When changing from view-as-student to view-as-yourself, there might be no student data available anymore.
                            // We should reflect this in the view instead of keeping the old data.

                            this.student = {};
                        });
                }
            },
            handleSubmit: function() {

                this.update(this.student);
            },
            hasAnyRole: roles.hasAnyRole,
            update: function(payload) {

                this.displayErrors(false);
                this.displaySuccess(false);

                let promise;

                if (this.viewAs) {

                    promise = students.updateByIds(payload.id, payload);
                }
                else
                    promise = students.updateByAuthenticated(payload, payload);

                promise.then(result => {

                    this.student = _.defaultsDeep(result.item, {});
                    this.displaySuccess(true);

                    // By request: redirect to the dashboard page. This should probably be removed once more settings can be edited on this page:

                    this.$router.push({path: '/'});
                })
                    .catch(customError => {this.displayErrorsSpecific(true, customError.errors);});

                return promise;
            },
        },
        mixins: [

            backgroundProcesses,
            errorAlerts,
            loadingOverlay,
        ],
        watch: {

            viewAs: function() {

                this.fetchStudent();
            },
        },
    };

</script>
