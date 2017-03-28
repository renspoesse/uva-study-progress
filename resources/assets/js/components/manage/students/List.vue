<template>

    <div>

        <loading-overlay v-show="loading"></loading-overlay>

        <div class="dashhead">
            <div class="dashhead-titles">
                <h6 class="dashhead-subtitle">Manage students</h6>
                <h2 class="dashhead-title">Students</h2>
            </div>
        </div>

        <hr class="m-t">

        <alert type="danger" v-bind:message="errorMessage" v-bind:show="errorMessage" v-on:close="errorMessage = ''"></alert>

        <div class="flextable table-actions">
            <div class="flextable-item flextable-primary">
                <div class="btn-toolbar-item input-with-icon">
                    <input type="text" class="form-control input-block" placeholder="Search students" v-model="query" v-on:keyup.enter="handleSearch">
                    <i class="icon icon-magnifying-glass"></i>
                </div>
            </div>
            <div class="flextable-item" v-if="hasAnyRole(user, roles.Administrator)">
                <div class="btn-group">
                    <a href="#/manage/students/import" class="btn btn-primary-outline"><i class="fa fa-plus m-r-s"></i>Import from .csv</a>
                </div>
            </div>
        </div>

        <div class="flextable table-actions">
            <div class="flextable-item flextable-primary">
            </div>
            <div class="flextable-item" v-if="hasAnyRole(user, roles.Administrator)">
                <select v-model="actionMode">
                    <option v-bind:value="1">With selected items only:</option>
                    <option v-bind:value="2">With all matching items:</option>
                </select>
                <div class="btn-group">
                    <a class="btn btn-primary-outline" v-on:click.prevent="handlePublish"><i class="fa fa-eye m-r-s"></i>Publish</a>
                    <a class="btn btn-primary-outline" v-on:click.prevent="handleUnpublish"><i class="fa fa-eye-slash m-r-s"></i>Unpublish</a>
                    <a class="btn btn-primary-outline" v-on:click.prevent="handleRemove"><i class="fa fa-trash"></i></a>
                </div>
            </div>
        </div>

        <div class="table-full">
            <div class="table-responsive">
                <table class="table" data-sort="table">
                    <thead>
                    <tr>
                        <th class="header"><input type="checkbox" ref="selectAll" v-on:change="handleSelectAll"></th>
                        <th v-bind:class="headerClass('student_number')" v-on:click.prevent="handleOrder('student_number')">#</th>
                        <th v-bind:class="headerClass('display_name')" v-on:click.prevent="handleOrder('display_name')">Name</th>
                        <th v-bind:class="headerClass('program_name')" v-on:click.prevent="handleOrder('program_name')">Program</th>
                        <th v-bind:class="headerClass('cohort')" v-on:click.prevent="handleOrder('cohort')">Cohort</th>
                        <th v-bind:class="headerClass('second_year_b1_subjects')" v-on:click.prevent="handleOrder('second_year_b1_subjects')" class="header">Prog.</th>
                        <th v-bind:class="headerClass('is_published')" v-on:click.prevent="handleOrder('is_published')">Published</th>
                        <th v-bind:class="headerClass('updated_at')" v-on:click.prevent="handleOrder('updated_at')">Updated at</th>
                        <th class="header">View as</th>
                    </tr>
                    </thead>
                    <tbody>

                    <tr v-for="item in items" v-bind:key="item.id" v-bind:data-id="item.id">
                        <td><input type="checkbox" class="select-row"></td>
                        <td><a v-bind:href="'#/manage/students/' + item.id">{{ item.student_number }}</a></td>
                        <td v-bind:title="item.display_name">{{ _.truncate(item.display_name, {length: 30}) }}</td>
                        <td v-bind:title="item.program_name">{{ _.truncate(item.program_name, {length: 30}) }}</td>
                        <td>{{ item.cohort }}</td>
                        <td>
                            <i class="fa fa-circle" style="color: red;" v-if="item.second_year_b1_subjects === 0"></i>
                            <i class="fa fa-circle" style="color: yellow;" v-else-if="item.second_year_b1_subjects === 1"></i>
                            <i class="fa fa-circle" style="color: green;" v-else-if="item.second_year_b1_subjects > 1"></i>
                        </td>
                        <td>
                            <i class="fa fa-check" v-if="item.is_published"></i>
                        </td>
                        <td>{{ moment.utc(item.updated_at).local().format('YYYY-MM-DD HH:mm:ss') }}</td>
                        <td><a class="btn btn-default-outline" v-on:click.prevent="handleViewAs(item)"><i class="fa fa-eye"></i></a></td>
                    </tr>

                    <tr v-if="items.length === 0">
                        <td colspan="8">No results</td>
                    </tr>

                    </tbody>
                </table>
            </div>
        </div>

        <pagination v-bind:pagination="pagination" v-on:goToPage="handleGoToPage"></pagination>

    </div>

</template>

<script>

    import * as _ from 'lodash'
    import moment from 'moment'
    import {mapState} from 'vuex'

    import Roles from '../../../enums/Roles'

    import errorAlerts from '../../../mixins/error_alerts'
    import listActions from '../../../mixins/list_actions'
    import loadingOverlay from '../../../mixins/loading_overlay'

    import * as images from '../../../helpers/images'
    import * as roles from '../../../helpers/roles'
    import * as students from '../../../services/students'

    export default {

        computed: {

            ...mapState({

                user: (state) => state.auth.user
            }),
            _() { return _; },
            moment() { return moment; },
            roles() { return Roles; }
        },
        data: function() {

            return {

                actionMode: 1
            };
        },
        methods: {

            fetchData: function(page) {

                this.showLoading(true);
                this.displayErrors(false);

                this.updateRoute(page);

                students.getByParameters({

                        order: this.order.field + '|' + this.order.direction,
                        page: page,
                        query: this.query.trim()
                    })
                    .then((result) => {

                        this.items = result.items;
                        this.pagination = result.meta.pagination;

                        this.showLoading(false);
                    })
                    .catch((ex) => {

                        this.displayErrors(true, ex.message);
                        this.showLoading(false);
                    });
            },
            handlePublish: function() {

                this.displayErrors(false);

                if (this.actionMode === 1) {

                    _.forEach(this.getSelectedItems(), (el) => {

                        const id = parseInt($(el).attr('data-id'));

                        students.updateById(id, {is_published: true})
                            .then(() => {this.fetchData(this.pagination.currentPage);})
                            .catch((ex) => {this.addError(ex.message);});
                    });
                }
                else if (this.actionMode === 2) {

                    // TODO RENS
                }
            },
            handleRemove: function() {

                this.displayErrors(false);

                // TODO RENS: confirmation.

                if (this.actionMode === 1) {

                    _.forEach(this.getSelectedItems(), (el) => {

                        const id = parseInt($(el).attr('data-id'));

                        students.deleteById(id)
                            .then(() => {this.fetchData(this.pagination.currentPage);})
                            .catch((ex) => {this.addError(ex.message);});
                    });
                }
                else if (this.actionMode === 2) {

                    // TODO RENS
                }
            },
            handleUnpublish: function() {

                this.displayErrors(false);

                if (this.actionMode === 1) {

                    _.forEach(this.getSelectedItems(), (el) => {

                        const id = parseInt($(el).attr('data-id'));

                        students.updateById(id, {is_published: false})
                            .then(() => {this.fetchData(this.pagination.currentPage);})
                            .catch((ex) => {this.addError(ex.message);});
                    });
                }
                else if (this.actionMode === 2) {

                    // TODO RENS
                }
            },
            handleViewAs: function(item) {

                this.$store.commit('auth/SET_VIEW_AS', {

                    fullname: item.display_name,
                    id: item.id,
                    roles: [Roles.Student],
                    ltiUserId: item.student_number
                });

                this.$router.push('/');
            },
            hasAnyRole: roles.hasAnyRole
        },
        mixins: [

            errorAlerts,
            listActions,
            loadingOverlay
        ]
    }

</script>