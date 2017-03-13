<template>

    <div>

        <div class="dashhead">
            <div class="dashhead-titles">
                <h6 class="dashhead-subtitle">Dashboards</h6>
                <h2 class="dashhead-title">News</h2>
            </div>
        </div>

        <hr class="m-t">

        <div class="panel panel-default" v-for="item in items">
            <div class="panel-heading">
                <h3 class="panel-title">{{ item.title }}</h3>
            </div>
            <div class="panel-body" v-html="item.text"></div>
            <div class="panel-footer">
                updated {{ moment.utc(item.updated_at).local().fromNow() }}
            </div>
        </div>

    </div>

</template>

<script>

    import * as _ from 'lodash'
    import moment from 'moment'

    import errorAlerts from '../../mixins/error_alerts'
    import loadingOverlay from '../../mixins/loading_overlay'

    import * as news from '../../services/news'

    export default {

        computed: {

            _() { return _; },
            moment() { return moment; }
        },
        created() {

            this.fetchData();
        },
        data: function() {

            return {

                items: []
            }
        },
        methods: {

            fetchData: function() {

                this.showLoading(true);
                this.displayErrors(false);

                news.getByParameters({

                        order: 'updated_at|desc',
                        publishedOnly: true
                    })
                    .then((result) => {

                        this.items = result.items;
                        this.showLoading(false);
                    })
                    .catch((ex) => {

                        this.displayErrors(true, ex.message);
                        this.showLoading(false);
                    });
            }
        },
        mixins: [

            errorAlerts,
            loadingOverlay
        ]
    }

</script>