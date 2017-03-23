import * as _ from 'lodash'

export default {

    created() {

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
            order: {field: '', direction: ''},
            pagination: {currentPage: 1, totalPages: 1},
            query: ''
        };
    },
    methods: {

        getSelectedItems: function() {

            // TODO RENS: dit kan meer reactive.

            return $(this.$el).find('.select-row:checked').closest('[data-id]');
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
        },
        updateRoute: function(page) {

            // Change the route and call any hooks, but without re-rendering the component.

            this.$router.replace({

                query: _.omitBy({

                    order: this.order.field ? this.order.field + '|' + this.order.direction : '',
                    page: page > 1 ? page : '',
                    query: this.query.trim()

                }, (value) => {return !value})
            });
        }
    }
}