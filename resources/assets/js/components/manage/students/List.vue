<template>

    <div>

        <loading-overlay v-show="loading"></loading-overlay>

        <div class="dashhead">
            <div class="dashhead-titles">
                <h6 class="dashhead-subtitle">Manage</h6>
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
                <select class="m-l-s" v-model="limit" v-on:change="handleRefresh()">
                    <option v-bind:value="15">Show 15 items</option>
                    <option v-bind:value="25">Show 25 items</option>
                    <option v-bind:value="50">Show 50 items</option>
                </select>
            </div>
            <div class="flextable-item" v-if="hasAnyRole(user, roles.Administrator)">
                <div class="btn-group">
                    <router-link to="/manage/students/import" class="btn btn-primary-outline"><i class="fa fa-plus m-r-s"></i>Import</router-link>
                    <a href="/api/export/students" class="btn btn-primary-outline"><i class="fa fa-download m-r-s"></i>Export</a>
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
                        <th v-bind:class="headerClass('student_number')" v-on:click.prevent="handleOrder('student_number')"></th>
                        <th v-bind:class="headerClass('display_name')" v-on:click.prevent="handleOrder('display_name')"></th>
                        <th v-bind:class="headerClass('program_name')" v-on:click.prevent="handleOrder('program_name')">Program</th>
                        <th class="header">Satisfaction</th>
                        <th v-bind:class="headerClass('first_year_mentor')" v-on:click.prevent="handleOrder('first_year_mentor')">Student Coach</th>
                        <th v-bind:class="headerClass('cohort')" v-on:click.prevent="handleOrder('cohort')">Cohort</th>
                        <th v-bind:class="headerClass('year')" v-on:click.prevent="handleOrder('year')">Year</th>
                        <th class="header">Goal</th>
                        <th v-bind:class="headerClass('second_year_credits_expected')" v-on:click.prevent="handleOrder('second_year_credits_expected')"></th>
                        <th v-bind:class="headerClass('is_published')" v-on:click.prevent="handleOrder('is_published')">Published</th>
                        <th class="header"></th>
                    </tr>
                    </thead>
                    <tbody>

                    <tr v-for="item in items" v-bind:key="item.id" v-bind:data-id="item.id">
                        <td><input type="checkbox" class="select-row"></td>
                        <td>
                            <router-link v-bind:to="'/manage/students/' + item.id">{{ item.student_number }}</router-link>
                        </td>
                        <td v-bind:title="item.display_name">{{ _.truncate(item.display_name, {length: 30}) }}</td>
                        <td v-bind:title="item.program_name">{{ _.truncate(item.program_name, {length: 30}) }}</td>
                        <td>{{ item.program_satisfaction }}</td>
                        <td>{{ item.first_year_mentor }}</td>
                        <td>{{ item.cohort }}</td>
                        <td>{{ item.year }}</td>
                        <td>{{ (item.year === 1) ? item.first_year_credits_goal : item.second_year_credits_goal }}</td>
                        <td>
                            <i class="fa fa-circle" style="color: green;" title=">= 54 EC expected" v-if="item.year === 2 && item.second_year_credits_expected >= 54"></i>
                            <i class="fa fa-circle" style="color: yellow;" title=">= 30 EC expected" v-else-if="item.year === 2 && item.second_year_credits_expected >= 30"></i>
                            <i class="fa fa-circle" style="color: red;" title="< 30 EC expected" v-else-if="item.year === 2"></i>
                        </td>
                        <td>
                            <i class="fa fa-check" v-if="item.is_published"></i>
                        </td>
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

    import * as _ from 'lodash';
    import {mapState} from 'vuex';
    import Roles from '../../../enums/Roles';
    import errorAlerts from '../../../mixins/error_alerts';
    import listActions from '../../../mixins/list_actions';
    import loadingOverlay from '../../../mixins/loading_overlay';
    import * as roles from '../../../helpers/roles';
    import * as students from '../../../services/students';

    export default {

        computed: {

            ...mapState({

                user: (state) => state.auth.user,
            }),
        },
        data: function() {

            return {

                actionMode: 1,
            };
        },
        methods: {

            fetchData: function(page) {

                this.showLoading(true);
                this.displayErrors(false);

                this.updateRoute(page);

                students.getByParameters({

                        limit: this.limit,
                        order: this.order.field + '|' + this.order.direction,
                        page: page,
                        query: this.query.trim(),
                    })
                    .then(result => {

                        this.items = result.items;
                        this.pagination = result.meta.pagination;

                        this.showLoading(false);
                    })
                    .catch(customError => {

                        this.displayErrors(true, customError.message);
                        this.showLoading(false);
                    });
            },
            handlePublish: function() {

                this.displayErrors(false);

                if (this.actionMode === 1) {

                    students.updateByIds(_.map(this.getSelectedItems(), 'id'), {is_published: true})
                        .then(() => {this.fetchData(this.pagination.currentPage);})
                        .catch((ex) => {this.addError(ex.message);});
                }
                else if (this.actionMode === 2) {

                    students.updateByParameters({query: this.query.trim()}, {is_published: true})
                        .then(() => {this.fetchData(this.pagination.currentPage);})
                        .catch((ex) => {this.addError(ex.message);});
                }
            },
            handleRemove: function() {

                this.displayErrors(false);

                // TODO: confirmation.

                if (this.actionMode === 1) {

                    students.deleteByIds(_.map(this.getSelectedItems(), 'id'))
                        .then(() => {this.fetchData(this.pagination.currentPage);})
                        .catch(customError => {this.addError(customError.message);});
                }
                else if (this.actionMode === 2) {

                    students.deleteByParameters({query: this.query.trim()})
                        .then(() => {this.fetchData(this.pagination.currentPage);})
                        .catch(customError => {this.addError(customError.message);});
                }
            },
            handleUnpublish: function() {

                this.displayErrors(false);

                if (this.actionMode === 1) {

                    students.updateByIds(_.map(this.getSelectedItems(), 'id'), {is_published: false})
                        .then(() => {this.fetchData(this.pagination.currentPage);})
                        .catch(customError => {this.addError(customError.message);});
                }
                else if (this.actionMode === 2) {

                    students.updateByParameters({query: this.query.trim()}, {is_published: false})
                        .then(() => {this.fetchData(this.pagination.currentPage);})
                        .catch(customError => {this.addError(customError.message);});
                }
            },
            handleViewAs: function(item) {

                this.$store.commit('auth/SET_VIEW_AS', {

                    fullname: item.display_name,
                    id: item.id,
                    roles: [Roles.Student],
                });

                this.$router.push({path: '/', query: {viewAs: item.id}});
            },
            hasAnyRole: roles.hasAnyRole,
        },
        mixins: [

            errorAlerts,
            listActions,
            loadingOverlay,
        ],
    };

</script>
