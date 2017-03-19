<template>

    <div>

        <div class="dashhead">
            <div class="dashhead-titles">
                <h6 class="dashhead-subtitle">Dashboards</h6>
                <h2 class="dashhead-title">Overview for {{ displayName }}</h2>
            </div>
        </div>

        <hr class="m-t">

        <div class="row text-center m-t-md">
            <div class="col-md-12 m-b-md">
                <div class="w-lg m-x-auto" style="height: 30rem;">
                    <canvas ref="chartCreditPrognosis"></canvas>
                </div>
            </div>
        </div>

        <div class="hr-divider m-t-md m-b">
            <h3 class="hr-divider-content hr-divider-heading">Study progress indicators</h3>
        </div>

        <div class="row">
            <div class="col-md-12">

                <div class="table-full">
                    <div class="table-responsive">
                        <table class="table" data-sort="table">
                            <thead>
                            <tr>
                                <th class="header">Indicator</th>
                                <th class="header">Value</th>
                            </tr>
                            </thead>
                            <tbody>

                            <tr>
                                <td>BSA</td>
                                <td>{{ getStudentBsaText(student) }}<i class="fa fa-check m-l-s" v-if="isStudentBsaPositive(student)"></i></td>
                            </tr>
                            <tr>
                                <td>Credits in 1st year</td>
                                <td>{{ student.bsa_credits }}</td>
                            </tr>
                            <tr>
                                <td>Credits in block 1, 2nd year</td>
                                <td>{{ student.second_year_b1_credits }}</td>
                            </tr>
                            <tr>
                                <td>Prognosis total credits 2nd year</td>
                                <td>{{ student.second_year_credits }}</td>
                            </tr>
                            <tr class="bg-info">
                                <td>Expected graduation semester</td>
                                <td>(Data not yet available)</td>
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
    import Chart from 'chart.js'
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
            this.fetchStudent();
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
            },
            fetchStudent: function() {

                this.displayErrors(false);

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
            },
            getStudentBsaText: function(student) {

                switch (student.bsa) {

                    case 'MX':
                        return 'Max';
                    case 'PS':
                        return 'Positive';
                    case 'NE':
                        return 'Negative';
                    default:
                        return student.bsa;
                }
            },
            getCreditPrognosis: function(bsaCredits, courses) {

                // TODO RENS: met Fred bespreken.

                if (bsaCredits === 42) {

                    if (courses === 0) return 30;
                    if (courses === 1) return 40;
                    if (courses === 2) return 52;
                }
                else if (bsaCredits === 48) {

                    if (courses === 0) return 30;
                    if (courses === 1) return 44;
                    if (courses === 2) return 52;
                }
                else if (bsaCredits === 54) {

                    if (courses === 0) return 36;
                    if (courses === 1) return 48;
                    if (courses === 2) return 56;
                }
                else if (bsaCredits === 60) {

                    if (courses === 0) return 32;
                    if (courses === 1) return 50;
                    if (courses === 2) return 60;
                }
            },
            isStudentBsaPositive: function(student) {

                return (student.bsa === 'MX' || student.bsa === 'PS');
            },
            renderCharts: function() {

                const primaryColor = '#1CA8DD';

                let dipCategory = this.student.dip_category.split('~');

                dipCategory = {

                    bsaCredits: parseInt(dipCategory[0]),
                    courses: parseInt(dipCategory[1])
                };

                const chart = new Chart(this.$refs.chartCreditPrognosis, {

                    type: 'line',
                    data: {

                        labels: [0, 1, 2],
                        datasets: [{

                            fill: false,
                            lineTension: 0,
                            //backgroundColor: "rgba(75,192,192,0.4)",
                            //borderColor: "rgba(75,192,192,1)",
                            //borderCapStyle: 'butt',
                            //borderDash: [],
                            //borderDashOffset: 0.0,
                            //borderJoinStyle: 'miter',
                            pointBorderColor: [

                                dipCategory.courses === 0 ? primaryColor : 'red',
                                dipCategory.courses === 1 ? primaryColor : 'yellow',
                                dipCategory.courses === 2 ? primaryColor : 'green'
                            ],
                            pointBackgroundColor: ['red', 'yellow', 'green'],
                            //pointBorderWidth: 1,
                            pointHoverRadius: [

                                dipCategory.courses === 0 ? 20 : 5,
                                dipCategory.courses === 1 ? 20 : 5,
                                dipCategory.courses === 2 ? 20 : 5
                            ],
                            //pointHoverBackgroundColor: "rgba(75,192,192,1)",
                            //pointHoverBorderColor: "rgba(220,220,220,1)",
                            //pointHoverBorderWidth: 1,
                            pointRadius: [

                                dipCategory.courses === 0 ? 10 : 2,
                                dipCategory.courses === 1 ? 10 : 2,
                                dipCategory.courses === 2 ? 10 : 2
                            ],
                            data: [

                                dipCategory.courses === 0 ? this.student.second_year_credits : this.getCreditPrognosis(dipCategory.bsaCredits, 0),
                                dipCategory.courses === 1 ? this.student.second_year_credits : this.getCreditPrognosis(dipCategory.bsaCredits, 1),
                                dipCategory.courses === 2 ? this.student.second_year_credits : this.getCreditPrognosis(dipCategory.bsaCredits, 2)
                            ],
                            //spanGaps: false,
                        }]
                    },
                    options: {

                        legend: {

                            display: false
                        },
                        maintainAspectRatio: false,
                        scales: {

                            xAxes: [{

                                scaleLabel: {

                                    display: true,
                                    labelString: 'Courses passed in 1st block'
                                }
                            }],
                            yAxes: [{

                                ticks: {

                                    max: 70,
                                    min: 0,
                                    stepSize: 10
                                }
                            }]
                        },
                        title: {

                            display: true,
                            text: 'Credit prognosis 2nd year - ' + this.student.bsa_credits + ' credit BSA group'
                        },
                        tooltips: {

                            callbacks: {

                                title: function(tooltipItem, data) {

                                    const passed = tooltipItem[0].xLabel;

                                    if (passed === 0)
                                        return passed + ' courses passed in block 1';
                                    else if (passed === 1)
                                        return passed + ' course passed in block 1';
                                    else
                                        return 'At least ' + passed + ' courses passed in block 1';
                                },
                                label: function(tooltipItem, data) { return 'Your prognosis for the 2nd year is ' + tooltipItem.yLabel + ' credits.'; }
                            },
                            displayColors: false
                        }
                    }
                });
            }
        },
        mixins: [

            errorAlerts,
            loadingOverlay
        ],
        updated () {

            images.fadeWhenLoaded($(this.$el).find('img'));
        },
        watch: {

            student: function(newValue) {

                if (newValue)
                    this.renderCharts();
            },
            viewAs: function(newValue) {

                this.fetchStudent();
            }
        }
    }

</script>