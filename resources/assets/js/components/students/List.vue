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
            <div class="flextable-item">
                <div class="btn-group">
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
                        <th v-bind:class="headerClass('student_number')" v-on:click.prevent="handleOrder('student_number')">Student number</th>
                        <th v-bind:class="headerClass('program_code')" v-on:click.prevent="handleOrder('program_code')">Program code</th>
                        <th v-bind:class="headerClass('program_name')" v-on:click.prevent="handleOrder('program_name')">Description</th>
                        <th v-bind:class="headerClass('updated_at')" v-on:click.prevent="handleOrder('updated_at')">Updated at</th>
                    </tr>
                    </thead>
                    <tbody>

                    <tr v-for="item in items" v-bind:key="item.id" v-bind:data-id="item.id">
                        <td><input type="checkbox" class="select-row"></td>
                        <td>{{ item.student_number }}</td>
                        <td>{{ item.program_code }}</td>
                        <td>{{ _.truncate(item.program_name, {length: 30}) }}</td>
                        <td>{{ moment.utc(item.updated_at).local().format('YYYY-MM-DD HH:mm:ss') }}</td>
                    </tr>

                    <tr v-if="items.length === 0">
                        <td colspan="5">No results</td>
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

    import errorAlerts from '../../mixins/error_alerts'

    import * as images from '../../helpers/images'
    import * as students from '../../services/students'

    export default {

        computed: {

            _() { return _; },
            moment() { return moment; }
        },
        created () {

            if (_.has(this.$route, 'query.order')) {

                const values = this.$route.query.order.split('|');
                this.order = {field: values[0], direction: values[1] || 'asc'};
            }

            if (_.has(this.$route, 'query.page'))
                this.pagination.currentPage = parseInt(this.$route.query.page);

            if (_.has(this.$route, 'query.query'))
                this.query = this.$route.query.query;

            this.fetchData(this.pagination.currentPage);
        },
        data: function() {

            return {

                items: [],
                loading: false,
                order: {field: 'updated_at', direction: 'desc'},
                pagination: {currentPage: 1, totalPages: 1},
                query: ''
            };
        },
        methods: {

            fetchData: function(page) {

                this.loading = true;
                this.displayErrors(false);

                // Change the route and call any hooks, but without re-rendering the component.

                this.$router.replace({

                    query: {

                        order: this.order.field + '|' + this.order.direction,
                        page: page,
                        query: this.query.trim()
                    }
                });

                students.getByParameters({

                        order: this.order.field + '|' + this.order.direction,
                        page: page,
                        query: this.query.trim()
                    })
                    .then((result) => {

                        this.items = result.items;
                        this.pagination = result.meta.pagination;

                        this.loading = false;
                    })
                    .catch((ex) => {

                        this.displayErrors(true, ex.message);
                        this.loading = false;
                    });
            },
            handleGoToPage: function(e) {

                this.fetchData(e.page);
            },
            handleOrder: function(field) {

                if (this.order.field === field)
                    this.order.direction = this.order.direction === 'asc' ? 'desc' : 'asc';
                else
                    this.order.field = field;

                this.fetchData(this.pagination.currentPage);
            },
            handleRemove: function() {

                this.displayErrors(false);

                // TODO RENS: dit kan meer reactive.

                const selected = $(this.$el).find('.select-row:checked').closest('[data-id]');

                // TODO RENS: confirmation.

                _.forEach(selected, (el) => {

                    const id = parseInt($(el).attr('data-id'));

                    students.deleteById(id)
                        .then(() => {this.fetchData(this.pagination.currentPage);})
                        .catch((ex) => {this.addError(ex.message);});
                });
            },
            handleSearch: function() {

                this.fetchData(1)
            },
            handleSelectAll: function() {

                const checked = $(this.$refs.selectAll).prop('checked');
                $(this.$el).find('.select-row').prop('checked', checked);
            },
            headerClass: function(field) {

                let result = 'header';

                if (this.order.field === field)
                    result += this.order.direction === 'asc' ? ' headerSortUp' : ' headerSortDown';

                return result;
            }
        },
        mixins: [

            errorAlerts
        ],
        updated () {

            images.fadeWhenLoaded($(this.$el).find('img'));
        }
    }

</script>