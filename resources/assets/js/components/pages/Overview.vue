<template>

    <div>

        <div class="dashhead">
            <div class="dashhead-titles">
                <h6 class="dashhead-subtitle">Dashboards</h6>
                <h2 class="dashhead-title">Overview for {{ displayName }}</h2>
            </div>
        </div>

        <hr class="m-t">

        <div class="row text-center m-t-lg">
            <div class="col-sm-4 m-b-md">
                <div class="w-lg m-x-auto">
                    <canvas class="ex-graph" width="159" height="159" data-chart="doughnut" data-value="[{ value: 230, color: '#1ca8dd', label: 'Returning' }, { value: 130, color: '#1bc98e', label: 'New' }]" data-segment-stroke-color="#252830" style="width: 159px; height: 159px;">
                    </canvas>
                </div>
                <strong class="text-muted">Traffic</strong>
                <h3>New vs Returning</h3>
            </div>
            <div class="col-sm-4 m-b-md">
                <div class="w-lg m-x-auto">
                    <canvas class="ex-graph" width="159" height="159" data-chart="doughnut" data-value="[{ value: 330, color: '#1ca8dd', label: 'Recurring' }, { value: 30, color: '#1bc98e', label: 'New' }]" data-segment-stroke-color="#252830" style="width: 159px; height: 159px;">
                    </canvas>
                </div>
                <strong class="text-muted">Revenue</strong>
                <h3>New vs Recurring</h3>
            </div>
            <div class="col-sm-4 m-b-md">
                <div class="w-lg m-x-auto">
                    <canvas class="ex-graph" width="159" height="159" data-chart="doughnut" data-value="[{ value: 100, color: '#1ca8dd', label: 'Referrals' }, { value: 260, color: '#1bc98e', label: 'Direct' }]" data-segment-stroke-color="#252830" style="width: 159px; height: 159px;">
                    </canvas>
                </div>
                <strong class="text-muted">Traffic</strong>
                <h3>Direct vs Referrals</h3>
            </div>
        </div>

        <hr class="m-t">

        <div class="row">
            <div class="col-md-12">

                <div class="table-full">
                    <div class="table-responsive">
                        <table class="table" data-sort="table">
                            <thead>
                            <tr>
                                <th class="header">Field</th>
                                <th class="header">Value</th>
                            </tr>
                            </thead>
                            <tbody>

                            <tr v-for="field in _.keys(student)">
                                <td>{{ field }}</td>
                                <td>{{ student[field] }}</td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

        <div class="hr-divider m-t-md m-b">
            <h3 class="hr-divider-content hr-divider-heading">Latest news, tips and advice</h3>
        </div>

        <div class="row">
            <div class="col-md-6">

                <div class="panel panel-default" v-for="item in news">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{ item.title }}</h3>
                    </div>
                    <div class="panel-body" v-html="item.text"></div>
                    <div class="panel-footer">
                        updated {{ moment.utc(item.updated_at).local().fromNow() }}
                    </div>
                </div>

            </div>
            <div class="col-md-6">

                <div class="panel panel-default" v-for="item in advice">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{ item.title }}</h3>
                    </div>
                    <div class="panel-body" v-html="item.text"></div>
                    <div class="panel-footer">
                        updated {{ moment.utc(item.updated_at).local().fromNow() }}
                    </div>
                </div>

            </div>
        </div>

    </div>

</template>

<script>

    import * as _ from 'lodash'
    import moment from 'moment'
    import {mapGetters, mapState} from 'vuex'

    import errorAlerts from '../../mixins/error_alerts'
    import loadingOverlay from '../../mixins/loading_overlay'

    import * as advice from '../../services/advice'
    import * as images from '../../helpers/images'
    import * as news from '../../services/news'
    import * as students from '../../services/students'

    export default {

        computed: {

            ...mapGetters({

                displayName: 'auth/displayName'
            }),
            ...mapState({

                user: (state) => state.auth.user,
                viewAs: (state) => state.auth.viewAs
            }),
            _() { return _; },
            moment() { return moment; }
        },
        created() {

            this.fetchData();
        },
        data: function() {

            return {

                advice: [],
                news: [],
                student: {}
            }
        },
        methods: {

            fetchData: function() {

                this.displayErrors(false);

                advice.getByParameters({

                        order: 'updated_at|desc',
                        limit: 2,
                        publishedOnly: true
                    })
                    .then((result) => {

                        this.advice = result.items;
                    })
                    .catch((ex) => {

                        this.displayErrors(true, ex.message);
                    });

                news.getByParameters({

                        order: 'updated_at|desc',
                        limit: 2,
                        publishedOnly: true
                    })
                    .then((result) => {

                        this.news = result.items;
                    })
                    .catch((ex) => {

                        this.displayErrors(true, ex.message);
                    });

                if (this.viewAs) {

                    students.getById(this.viewAs.id)
                        .then((result) => {

                            this.student = result.item;
                        })
                        .catch((ex) => {

                            this.displayErrors(true, ex.message);
                        });
                }
                else {

                    students.getByAuthenticated()
                        .then((result) => {

                            this.student = result.item;
                        })
                        .catch((ex) => {

                            this.displayErrors(true, ex.message);
                        });
                }
            }
        },
        mixins: [

            errorAlerts,
            loadingOverlay
        ],
        mounted: function() {

            $(document).trigger('redraw.bs.charts');
        },
        updated () {

            images.fadeWhenLoaded($(this.$el).find('img'));
        }
    }

</script>