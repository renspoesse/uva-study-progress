<template>

    <div>

        <loading-overlay v-show="loading"></loading-overlay>

        <div class="dashhead">
            <div class="dashhead-titles">
                <h2 class="dashhead-subtitle">{{ displayName }}</h2>
                <h2 class="dashhead-title">Personalize</h2>
            </div>
        </div>

        <hr class="m-t">

        <alert type="success" v-bind:message="successMessage" v-bind:show="successMessage" v-on:close="successMessage = ''"></alert>
        <alert type="danger" v-bind:message="errorMessage" v-bind:show="errorMessage" v-on:close="errorMessage = ''"></alert>

        <form v-on:submit.prevent="submit" class="form-horizontal">

            <div class="form-group" v-if="student.id">
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

            <div v-bind:class="['form-group']" v-if="hasAnyRole(user, [roles.StudyAdvisor, roles.Administrator])">
                <label class="col-sm-3 control-label">Roles (not visible to students)</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" v-bind:value="roles.join(', ')" disabled>
                </div>
            </div>

            <div v-bind:class="['form-group', hasError('second_year_credits_goal') ? 'has-feedback has-error' : '']" v-if="student.id">
                <label class="col-sm-3 control-label">2nd year credits goal</label>
                <div class="col-sm-9">
                    <input type="number" class="form-control" v-model="student.second_year_credits_goal">
                </div>
            </div>

            <div class="form-group" v-if="student.id">
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

    import * as _ from 'lodash'
    import {mapGetters, mapState} from 'vuex'

    import Roles from '../../enums/Roles'

    import backgroundProcesses from '../../mixins/background_processes'
    import errorAlerts from '../../mixins/error_alerts'
    import loadingOverlay from '../../mixins/loading_overlay'

    import * as images from '../../helpers/images'
    import * as roles from '../../helpers/roles'
    import * as students from '../../services/students'

    export default {

        computed: {

            ...mapGetters({

                displayName: 'auth/displayName',
                roles: 'auth/roles'
            }),
            ...mapState({

                viewAs: (state) => state.auth.viewAs
            }),
            roles() { return Roles; }
        },
        created() {

            this.fetchStudent();
        },
        data: function() {

            return {

                student: {}
            }
        },
        methods: {

            fetchStudent: function() {

                this.displayErrors(false);

                if (this.viewAs) {

                    students.getById(this.viewAs.id)
                        .then((result) => {

                            this.student = result.item;
                        });
                }
                else {

                    students.getByAuthenticated()
                        .then((result) => {

                            this.student = result.item;
                        })
                        .catch((ex) => {

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

                const promise = students.updateById(payload.id, payload);

                promise.then((result) => {

                        this.student = _.defaultsDeep(result.item, {});
                        this.displaySuccess(true);
                    })
                    .catch((ex) => {this.displayErrors(true, ex.message, ex.errors);});

                return promise;
            }
        },
        mixins: [

            backgroundProcesses,
            errorAlerts,
            loadingOverlay
        ],
        watch: {

            viewAs: function(newValue) {

                this.fetchStudent();
            }
        }
    }

</script>