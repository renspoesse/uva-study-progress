<template>

    <div>

        <loading-overlay v-show="loading"></loading-overlay>

        <div class="dashhead">
            <div class="dashhead-titles">
                <h6 class="dashhead-subtitle">Manage</h6>
                <h2 class="dashhead-title">Edit updates</h2>
            </div>
        </div>

        <hr class="m-t">

        <alert type="danger" v-bind:message="errorMessage" v-bind:show="errorMessage" v-on:close="errorMessage = ''"></alert>

        <div class="flextable table-actions">
            <div class="flextable-item flextable-primary">
                <div class="btn-toolbar-item input-with-icon">
                    <input type="text" class="form-control input-block" placeholder="Search updates" v-model="query" v-on:keyup.enter="handleSearch">
                    <i class="icon icon-magnifying-glass"></i>
                </div>
                <select class="m-l-s" v-model="limit" v-on:change="handleRefresh()">
                    <option v-bind:value="15">Show 15 items</option>
                    <option v-bind:value="25">Show 25 items</option>
                    <option v-bind:value="50">Show 50 items</option>
                </select>
            </div>
            <div class="flextable-item">
                <div class="btn-group">
                    <router-link to="/manage/news/new" class="btn btn-primary-outline"><i class="fa fa-plus"></i></router-link>
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
                        <th v-bind:class="headerClass('title')" v-on:click.prevent="handleOrder('title')">Title</th>
                        <th v-bind:class="headerClass('is_published')" v-on:click.prevent="handleOrder('is_published')">Published</th>
                        <th v-bind:class="headerClass('updated_at')" v-on:click.prevent="handleOrder('updated_at')">Updated at</th>
                    </tr>
                    </thead>
                    <tbody>

                    <tr v-for="item in items" v-bind:key="item.id" v-bind:data-id="item.id">
                        <td><input type="checkbox" class="select-row"></td>
                        <td v-bind:title="item.title"><router-link v-bind:to="'/manage/news/' + item.id + '/edit'">{{ _.truncate(item.title, {length: 30}) }}</router-link></td>
                        <td>
                            <i class="fa fa-check" v-if="item.is_published"></i>
                        </td>
                        <td>{{ moment.utc(item.updated_at).local().format('YYYY-MM-DD HH:mm:ss') }}</td>
                    </tr>

                    <tr v-if="items.length === 0">
                        <td colspan="4">No results</td>
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
    import errorAlerts from '../../../mixins/error_alerts';
    import listActions from '../../../mixins/list_actions';
    import loadingOverlay from '../../../mixins/loading_overlay';
    import * as news from '../../../services/news';

    export default {

        methods: {

            fetchData: function(page) {

                this.showLoading(true);
                this.displayErrors(false);

                this.updateRoute(page);

                news.getByParameters({

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
            handleRemove: function() {

                this.displayErrors(false);

                // TODO: confirmation.

                news.deleteByIds(_.map(this.getSelectedItems(), 'id'))
                    .then(() => {this.fetchData(this.pagination.currentPage);})
                    .catch(customError => {this.addError(customError.message);});
            },
        },
        mixins: [

            errorAlerts,
            listActions,
            loadingOverlay,
        ],
    };

</script>
