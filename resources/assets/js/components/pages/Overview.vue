<template>

    <div v-if="!hasAnyRole(user, roles.Student) && !viewAs">

        <alert type="info" message="This page is only available to students. Try viewing it as a student instead." show="true" v-bind:closeable="false"></alert>
        <a class="btn btn-primary-outline" href="#/manage/students" v-if="hasAnyRole(user, [roles.StudyAdvisor, roles.Administrator])">Go to Students</a>

    </div>
    <div v-else>

        <div class="dashhead">
            <div class="dashhead-titles">
                <h6 class="dashhead-subtitle">Dashboards</h6>
                <h2 class="dashhead-title">Overview for {{ displayName }}</h2>
            </div>
        </div>

        <hr class="m-t">

        <template v-if="student.id">

            <div class="row text-center m-t-md">
                <div class="col-md-12 m-b-md">
                    <div class="w-lg m-x-auto" style="height: 30rem;">
                        <canvas ref="chartCredits"></canvas>
                    </div>
                </div>
            </div>

            <div class="row text-center m-t-md">
                <div class="col-md-12 m-b-md">
                    <div class="w-lg m-x-auto" style="height: 30rem;">
                        <canvas ref="chartCreditsOverTime"></canvas>
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

                                <tr class="bg-info" v-if="student.graduation_date_expected">
                                    <td>Expected graduation date</td>
                                    <td>{{ moment(student.graduation_date_expected).format('YYYY-MM-DD') }}</td>
                                </tr>
                                <tr>
                                    <td>BSA</td>
                                    <td>{{ getStudentBsaText(student) }}<i class="fa fa-check m-l-s" v-if="isStudentBsaPositive(student)"></i></td>
                                </tr>
                                <tr>
                                    <td>Credits in 1st year</td>
                                    <td>{{ student.bsa_credits }} ECTS</td>
                                </tr>
                                <tr>
                                    <td>Credits in 2nd year</td>
                                    <td>{{ student.second_year_credits }} ECTS</td>
                                </tr>
                                <tr>
                                    <td>Total credits</td>
                                    <td>{{ student.credits }} ECTS</td>
                                </tr>
                                <tr class="bg-info">
                                    <td>Prognosis total credits 2nd year</td>
                                    <td>{{ student.second_year_credits_expected }} ECTS</td>
                                </tr>
                                <tr>
                                    <td>Courses passed in block 1, 2nd year</td>
                                    <td>{{ student.second_year_b1_subjects }} course(s)</td>
                                </tr>
                                <tr>
                                    <td>Credits in block 1, 2nd year</td>
                                    <td>{{ student.second_year_b1_credits }} ECTS</td>
                                </tr>
                                <tr v-if="student.second_year_b2_credits !== null">
                                    <td>Credits in block 2, 2nd year</td>
                                    <td>{{ student.second_year_b2_credits }} ECTS</td>
                                </tr>
                                <tr v-if="student.second_year_b3_credits !== null">
                                    <td>Credits in block 3, 3nd year</td>
                                    <td>{{ student.second_year_b3_credits }} ECTS</td>
                                </tr>
                                <tr v-if="student.second_year_b4_credits !== null">
                                    <td>Credits in block 4, 4nd year</td>
                                    <td>{{ student.second_year_b4_credits }} ECTS</td>
                                </tr>
                                <tr v-if="student.second_year_b5_credits !== null">
                                    <td>Credits in block 5, 5nd year</td>
                                    <td>{{ student.second_year_b5_credits }} ECTS</td>
                                </tr>
                                <tr v-if="student.second_year_b6_credits !== null">
                                    <td>Credits in block 6, 6nd year</td>
                                    <td>{{ student.second_year_b6_credits }} ECTS</td>
                                </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>

                    <a class="btn btn-primary-outline" href="http://student.uva.nl/ecb/az/content/studieadviseurs/online-afsprakensysteem/afspraak-studieadviseur-economie-bedrijfskunde.html" target="_blank">Contact a study advisor</a>

                </div>
            </div>

            <div class="hr-divider m-t-md m-b">
                <h3 class="hr-divider-content hr-divider-heading">Latest news, tips and advice</h3>
            </div>

        </template>

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

    import Roles from '../../enums/Roles'

    import * as advice from '../../services/advice'
    import * as images from '../../helpers/images'
    import * as news from '../../services/news'
    import * as roles from '../../helpers/roles'
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
            moment() { return moment; },
            roles() { return Roles; }
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
                        return 'Maximal (60 ECTS)';
                    case 'PS':
                        return 'Positive (48 or more ECTS)';
                    case 'NE':
                        return 'Negative';
                    case 'Di':
                        return 'Dispensation';
                    case 'HC':
                        return 'Hardness clause';
                    default:
                        return student.bsa;
                }
            },
            isStudentBsaPositive: function(student) {

                return (student.bsa === 'MX' || student.bsa === 'PS');
            },
            hasAnyRole: roles.hasAnyRole,
            renderCharts: function() {

                students.getCreditsExpected().then((result) => {

//                    const creditsExpected0 = _.find(result.items, (value) => {return value.bsa_credits === this.student.bsa_credits && value.second_year_b1_subjects === 0});
//                    const creditsExpected1 = _.find(result.items, (value) => {return value.bsa_credits === this.student.bsa_credits && value.second_year_b1_subjects === 1});
//                    const creditsExpected2 = _.find(result.items, (value) => {return value.bsa_credits === this.student.bsa_credits && value.second_year_b1_subjects > 1});
//
//                    const primaryColor = '#1CA8DD';
//
//                    let dipCategory = this.student.dip_category.split('~');
//
//                    dipCategory = {
//
//                        bsaCredits: parseInt(dipCategory[0]),
//                        block1Courses: parseInt(dipCategory[1])
//                    };

                    const chart1 = new Chart(this.$refs.chartCredits, {

                        type: 'bar',
                        data: {

                            labels: [

                                'Prognosis (' + this.student.second_year_credits_expected + ')',
                                'Goal (' + this.student.second_year_credits_goal + ')',
                                'Credits this year (' + this.student.second_year_credits + ')'
                            ],
                            datasets: [{

                                backgroundColor: [

                                    'rgba(255, 206, 86, 0.2)', // Yellow
                                    'rgba(75, 192, 192, 0.2)', // Green
                                    'rgba(255, 99, 132, 0.2)' // Red
                                ],
                                borderColor: [

                                    'rgba(255, 206, 86, 1)', // Yellow
                                    'rgba(75, 192, 192, 1)', // Green
                                    'rgba(255,99,132,1)' // Red
                                ],
                                borderWidth: 1,
                                data: [

                                    this.student.second_year_credits_expected,
                                    this.student.second_year_credits_goal,
                                    this.student.second_year_credits
                                ]
                            }]
                        },
                        options: {

                            legend: {

                                display: false
                            },
                            maintainAspectRatio: false,
                            scales: {

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
                                text: 'Your current 2nd year credits'
                            },
                            tooltips: {

                                enabled: false
                            }
                        }
                    });

                    const chart2 = new Chart(this.$refs.chartCreditsOverTime, {

                        type: 'bar',
                        data: {

                            labels: [

                                'Block 1',
                                'Block 2',
                                'Block 3',
                                'Block 4',
                                'Block 5',
                                'Block 6'
                            ],
                            datasets: [

                                {
                                    type: 'bar',
                                    data: [

                                        12,
                                        12 + 12,
                                        12 + 12 + 6,
                                        12 + 12 + 6 + 12,
                                        12 + 12 + 6 + 12 + 12,
                                        12 + 12 + 6 + 12 + 12 + 6
                                    ]
                                },
                                {
                                    type: 'line',
                                    borderColor: [

                                        'rgba(255, 206, 86, 1)' // Yellow
                                    ],
                                    fill: false,
                                    pointRadius: 0,
                                    data: [

                                        this.student.second_year_credits_expected,
                                        this.student.second_year_credits_expected,
                                        this.student.second_year_credits_expected,
                                        this.student.second_year_credits_expected,
                                        this.student.second_year_credits_expected,
                                        this.student.second_year_credits_expected
                                    ]
                                },
                                {
                                    type: 'line',
                                    borderColor: [

                                        'rgba(75, 192, 192, 1)' // Green
                                    ],
                                    fill: false,
                                    pointRadius: 0,
                                    data: [

                                        this.student.second_year_credits_goal,
                                        this.student.second_year_credits_goal,
                                        this.student.second_year_credits_goal,
                                        this.student.second_year_credits_goal,
                                        this.student.second_year_credits_goal,
                                        this.student.second_year_credits_goal
                                    ]
                                },
                                {
                                    type: 'line',
                                    borderColor: [

                                        'rgba(255,99,132,1)' // Red
                                    ],
                                    fill: false,
                                    pointBackgroundColor: [

                                        'rgba(255, 99, 132, 0.2)' // Red
                                    ],
                                    data: [

                                        this.student.second_year_b1_credits,
                                        (this.student.second_year_b2_credits === null) ? null : this.student.second_year_b1_credits + this.student.second_year_b2_credits,
                                        (this.student.second_year_b3_credits === null) ? null : this.student.second_year_b1_credits + this.student.second_year_b2_credits + this.student.second_year_b3_credits,
                                        (this.student.second_year_b4_credits === null) ? null : this.student.second_year_b1_credits + this.student.second_year_b2_credits + this.student.second_year_b3_credits + this.student.second_year_b4_credits,
                                        (this.student.second_year_b5_credits === null) ? null : this.student.second_year_b1_credits + this.student.second_year_b2_credits + this.student.second_year_b3_credits + this.student.second_year_b4_credits + this.student.second_year_b5_credits,
                                        (this.student.second_year_b6_credits === null) ? null : this.student.second_year_b1_credits + this.student.second_year_b2_credits + this.student.second_year_b3_credits + this.student.second_year_b4_credits + this.student.second_year_b5_credits + this.student.second_year_b6_credits,
                                    ]
                                }
                            ]
                        },
                        options: {

                            legend: {

                                display: false
                            },
                            maintainAspectRatio: false,
                            scales: {

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
                                text: 'Your 2nd year credits over time'
                            },
                            tooltips: {

                                enabled: false
                            }
                        }
                    });

//                    const chart3 = new Chart(this.$refs.chartCreditsOld, {
//
//                        type: 'line',
//                        data: {
//
//                            labels: [0, 1, 2],
//                            datasets: [{
//
//                                fill: false,
//                                lineTension: 0,
//                                //backgroundColor: "rgba(75,192,192,0.4)",
//                                //borderColor: "rgba(75,192,192,1)",
//                                //borderCapStyle: 'butt',
//                                //borderDash: [],
//                                //borderDashOffset: 0.0,
//                                //borderJoinStyle: 'miter',
//                                pointBorderColor: [
//
//                                    dipCategory.block1Courses === 0 ? primaryColor : 'red',
//                                    dipCategory.block1Courses === 1 ? primaryColor : 'yellow',
//                                    dipCategory.block1Courses > 1 ? primaryColor : 'green'
//                                ],
//                                pointBackgroundColor: ['red', 'yellow', 'green'],
//                                //pointBorderWidth: 1,
//                                pointHoverRadius: [
//
//                                    dipCategory.block1Courses === 0 ? 20 : 5,
//                                    dipCategory.block1Courses === 1 ? 20 : 5,
//                                    dipCategory.block1Courses > 1 ? 20 : 5
//                                ],
//                                //pointHoverBackgroundColor: "rgba(75,192,192,1)",
//                                //pointHoverBorderColor: "rgba(220,220,220,1)",
//                                //pointHoverBorderWidth: 1,
//                                pointRadius: [
//
//                                    dipCategory.block1Courses === 0 ? 10 : 2,
//                                    dipCategory.block1Courses === 1 ? 10 : 2,
//                                    dipCategory.block1Courses > 1 ? 10 : 2
//                                ],
//                                data: [
//
//                                    dipCategory.block1Courses === 0 ? this.student.second_year_credits_expected : creditsExpected0.second_year_credits_expected || 0,
//                                    dipCategory.block1Courses === 1 ? this.student.second_year_credits_expected : creditsExpected1.second_year_credits_expected || 0,
//                                    dipCategory.block1Courses > 1 ? this.student.second_year_credits_expected : creditsExpected2.second_year_credits_expected || 0
//                                ],
//                                //spanGaps: false,
//                            }]
//                        },
//                        options: {
//
//                            legend: {
//
//                                display: false
//                            },
//                            maintainAspectRatio: false,
//                            scales: {
//
//                                xAxes: [{
//
//                                    scaleLabel: {
//
//                                        display: true,
//                                        labelString: 'Courses passed in 1st block'
//                                    }
//                                }],
//                                yAxes: [{
//
//                                    ticks: {
//
//                                        max: 70,
//                                        min: 0,
//                                        stepSize: 10
//                                    }
//                                }]
//                            },
//                            title: {
//
//                                display: true,
//                                text: 'Credit prognosis 2nd year - ' + this.student.bsa_credits + ' credit BSA group'
//                            },
//                            tooltips: {
//
//                                callbacks: {
//
//                                    title: function(tooltipItem, data) {
//
//                                        const passed = parseInt(tooltipItem[0].xLabel);
//
//                                        if (passed === 0)
//                                            return passed + ' courses passed in block 1';
//                                        else if (passed === 1)
//                                            return passed + ' course passed in block 1';
//                                        else if (passed > 1)
//                                            return 'At least ' + passed + ' courses passed in block 1';
//                                    },
//                                    label: function(tooltipItem, data) { return 'Your prognosis for the 2nd year is ' + tooltipItem.yLabel + ' ECTS.'; }
//                                },
//                                displayColors: false
//                            }
//                        }
//                    });
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