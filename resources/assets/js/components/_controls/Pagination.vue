<template>

    <ul class="pagination">
        <li v-bind:class="{ disabled: (pagination.currentPage <= 1) }">
            <a aria-label="First" v-on:click.prevent="raiseGoTo(1)"><span aria-hidden="true">««</span></a>
        </li>
        <li v-bind:class="{ disabled: (pagination.currentPage <= 1) }">
            <a aria-label="Previous" v-on:click.prevent="raisePrevious"><span aria-hidden="true">«</span></a>
        </li>
        <li v-for="n in visiblePages" v-bind:class="{ active: (pagination.currentPage === n) }" v-on:click.prevent="raiseGoTo(n)">
            <a>{{ n }}</a>
        </li>
        <li v-bind:class="{ disabled: (pagination.currentPage >= pagination.totalPages) }">
            <a aria-label="Next" v-on:click.prevent="raiseNext"><span aria-hidden="true">»</span></a>
        </li>
        <li v-bind:class="{ disabled: (pagination.currentPage >= pagination.totalPages) }">
            <a aria-label="Last" v-on:click.prevent="raiseGoTo(pagination.totalPages)"><span aria-hidden="true">»»</span></a>
        </li>
    </ul>

</template>

<script>

    import * as _ from 'lodash';

    export default {

        computed: {

            firstPage: function() {

                const group = Math.ceil(this.pagination.currentPage / this.pagesToShow);
                return (group - 1) * this.pagesToShow + 1;
            },
            visiblePages: function() {

                const first = this.firstPage;
                const last = Math.min(first + this.pagesToShow, Math.max(this.pagination.totalPages, 1) + 1);

                return _.range(first, last);
            },
        },
        data: function() {

            return {

                pagesToShow: 5,
            };
        },
        methods: {

            raiseGoTo: function(page) {this.$emit('goToPage', {page: page});},
            raiseNext: function() {this.$emit('goToPage', {page: this.pagination.currentPage + 1});},
            raisePrevious: function() {this.$emit('goToPage', {page: this.pagination.currentPage - 1});},
        },
        props: [

            'pagination',
        ],
    };

</script>
