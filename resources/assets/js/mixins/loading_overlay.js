export default {

    data: function() {

        return {

            loading: false
        }
    },
    methods: {

        showLoading: function(show) {

            this.loading = show;
        }
    }
}