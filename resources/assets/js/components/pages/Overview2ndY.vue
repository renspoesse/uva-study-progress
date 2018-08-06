<template>

    <div>

        <div class="dashhead">
            <div class="dashhead-titles">
                <h2 class="dashhead-title">Dashboard for {{ displayName }}</h2>
            </div>
        </div>

        <hr class="m-t">

        <div class="row text-center" v-if="student.graduation_date_expected">
            <div class="col-md-12 m-b-md">

                <h4>Expected graduation date: {{ moment(student.graduation_date_expected).format('MMMM Do YYYY') }}</h4>
                <p>Your expected graduation date is based on the average number of credits earned per month.</p>

            </div>
        </div>

        <div class="row text-center m-t-md">
            <div class="col-md-12 m-b-md">
                <div class="w-lg m-x-auto" style="height: 30rem;">
                    <canvas ref="chartCredits"></canvas>
                </div>
                <p class="m-t">
                    * Please note that our prognosis has a margin of 6 credits.<br>
                    The prognosis is based on your results in year 1 and period 1 of year 2.<br>
                    Your personal goal is based on your results in year 1; you can adjust it on the <router-link to="/me">Personalise</router-link> page.
                </p>
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
                                <th class="header" colspan="2">Overall indicators</th>
                            </tr>
                            </thead>
                            <tbody>

                            <tr v-if="student.bsa !== null">
                                <td>BSA</td>
                                <td>{{ getStudentBsaText(student.bsa) }}<i class="fa fa-check m-l-s" v-if="isStudentBsaPositive(student.bsa)"></i></td>
                            </tr>
                            <tr v-if="student.bsa_credits !== null">
                                <td>BSA credits</td>
                                <td>{{ student.bsa_credits }} ECTS</td>
                            </tr>
                            <tr v-if="student.first_year_credits !== null">
                                <td>Credits in 1st year</td>
                                <td>{{ student.first_year_credits }} ECTS</td>
                            </tr>
                            <tr v-if="student.second_year_credits !== null">
                                <td>Credits in 2nd year</td>
                                <td>{{ student.second_year_credits }} ECTS</td>
                            </tr>
                            <tr>
                                <td>Total credits</td>
                                <td>{{ student.credits }} ECTS</td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="table-full">
                    <div class="table-responsive">
                        <table class="table" data-sort="table">
                            <thead>
                            <tr>
                                <th class="header" colspan="2">2nd year indicators</th>
                            </tr>
                            </thead>
                            <tbody>

                            <tr v-if="student.second_year_b1_subjects !== null">
                                <td>Courses passed in block 1</td>
                                <td>{{ student.second_year_b1_subjects }} course(s)</td>
                            </tr>
                            <tr v-if="student.second_year_b1_credits !== null">
                                <td>Credits in block 1</td>
                                <td>{{ student.second_year_b1_credits }} ECTS</td>
                            </tr>
                            <tr v-if="student.second_year_b2_credits !== null">
                                <td>Credits in block 2</td>
                                <td>{{ student.second_year_b2_credits }} ECTS</td>
                            </tr>
                            <tr v-if="student.second_year_b3_credits !== null">
                                <td>Credits in block 3</td>
                                <td>{{ student.second_year_b3_credits }} ECTS</td>
                            </tr>
                            <tr v-if="student.second_year_b4_credits !== null">
                                <td>Credits in block 4</td>
                                <td>{{ student.second_year_b4_credits }} ECTS</td>
                            </tr>
                            <tr v-if="student.second_year_b5_credits !== null">
                                <td>Credits in block 5</td>
                                <td>{{ student.second_year_b5_credits }} ECTS</td>
                            </tr>
                            <tr v-if="student.second_year_b6_credits !== null">
                                <td>Credits in block 6</td>
                                <td>{{ student.second_year_b6_credits }} ECTS</td>
                            </tr>
                            <tr class="bg-info" v-if="student.second_year_credits_expected !== null">
                                <td>Prognosis total credits 2nd year</td>
                                <td>{{ student.second_year_credits_expected }} ECTS</td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                </div>

                <a class="btn btn-primary-outline" href="http://student.uva.nl/eco/content/az/study-advisers/study-advisers.html" target="_blank">Contact a study adviser</a>

            </div>
        </div>

        <!--<div class="hr-divider m-t-md m-b">-->
            <!--<h3 class="hr-divider-content hr-divider-heading">Latest updates and info</h3>-->
        <!--</div>-->

        <!--<div class="row">-->
            <!--<div class="col-md-6">-->

                <!--<div class="panel panel-default" v-for="item in news">-->
                    <!--<div class="panel-heading">-->
                        <!--<h3 class="panel-title">{{ item.title }}</h3>-->
                    <!--</div>-->
                    <!--<div class="panel-body" v-html="item.text"></div>-->
                    <!--<div class="panel-footer">-->
                        <!--updated {{ moment.utc(item.updated_at).local().fromNow() }}-->
                    <!--</div>-->
                <!--</div>-->

            <!--</div>-->
            <!--<div class="col-md-6">-->

                <!--<div class="panel panel-default" v-for="item in advice">-->
                    <!--<div class="panel-heading">-->
                        <!--<h3 class="panel-title">{{ item.title }}</h3>-->
                    <!--</div>-->
                    <!--<div class="panel-body" v-html="item.text"></div>-->
                    <!--<div class="panel-footer">-->
                        <!--updated {{ moment.utc(item.updated_at).local().fromNow() }}-->
                    <!--</div>-->
                <!--</div>-->

            <!--</div>-->
        <!--</div>-->

    </div>

</template>

<script>

    import * as _ from 'lodash'
    import Chart from 'chart.js'
    import moment from 'moment'
    import {mapGetters} from 'vuex'

    import errorAlerts from '../../mixins/error_alerts'

    import * as advice from '../../services/advice'
    import * as images from '../../helpers/images'
    import * as news from '../../services/news'
    import * as students from '../../services/students'

    export default {

        computed: {

            ...mapGetters({

                displayName: 'auth/displayName'
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
                news: []
            };
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
            getStudentBsaText: function(bsa) {

                switch (bsa) {

                    case 'MX':
                        return 'Maximal';
                    case 'PS':
                        return 'Positive';
                    case 'NE':
                        return 'Negative';
                    case 'Di':
                        return 'Dispensation';
                    case 'HC':
                        return 'Hardness clause';
                    default:
                        return bsa;
                }
            },
            isStudentBsaPositive: function(bsa) {

                return (bsa === 'MX' || bsa === 'PS');
            },
            renderCharts: function() {

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

                // Behaalde credits: color1
                // Prognose: color2
                // Goal: color3

                //                    const color1 = 'rgba(255, 99, 132, 0.2)'; // Red
                //                    const color2 = 'rgba(255, 206, 86, 0.2)'; // Yellow
                //                    const color3 = 'rgba(75, 192, 192, 0.2)'; // Green
                //
                //                    const color1Border = 'rgba(255, 99, 132, 1)'; // Red
                //                    const color2Border = 'rgba(255, 206, 86, 1)'; // Yellow
                //                    const color3Border = 'rgba(75, 192, 192, 1)'; // Green

                const color1 = 'rgba(135, 192, 181, .7)';
                const color2 = 'rgba(229, 217, 79, .4)';
                const color3 = 'rgba(135, 192, 181, .4)';

                const color1Border = color1;
                const color2Border = color2;
                const color3Border = color3;

                const chart1 = new Chart(this.$refs.chartCredits, {

                    type: 'bar',
                    data: {

                        labels: [

                            'Credits this year (' + (this.student.second_year_credits || 'none') + ')',
                            'Prognosis* (' + (this.student.second_year_credits_expected || 'none') + ')',
                            'Goal (' + (this.student.second_year_credits_goal || 'none') + ')',
                        ],
                        datasets: [{

                            backgroundColor: [

                                color1,
                                color2,
                                color3
                            ],
                            borderColor: [

                                color1Border,
                                color2Border,
                                color3Border
                            ],
                            borderWidth: 1,
                            data: [

                                this.student.second_year_credits,
                                this.student.second_year_credits_expected,
                                this.student.second_year_credits_goal,
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

                                    max: Math.max(...[70, Math.ceil(this.student.second_year_credits / 10) * 10, Math.ceil(this.student.second_year_credits_expected / 10) * 10, Math.ceil(this.student.second_year_credits_goal / 10) * 10]),
                                    min: 0,
                                    stepSize: 10
                                }
                            }]
                        },
                        title: {

                            display: true,
                            text: 'Your 2nd year'
                        },
                        tooltips: {

                            enabled: false
                        }
                    }
                });

                students.getCreditsAverage().then((result) => result.item).then((average) => {

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
                                    label: 'Standard number of credits',
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

                                        '#000000'
                                    ],
                                    borderWidth: 1,
                                    fill: false,
                                    label: 'Peer group average',
                                    lineTension: 0,
                                    spanGaps: true,
                                    data: [

                                        (average.second_year_b1_credits_average === null) ? null : average.second_year_b1_credits_average,
                                        (average.second_year_b2_credits_average === null) ? null : average.second_year_b1_credits_average + average.second_year_b2_credits_average,
                                        (average.second_year_b3_credits_average === null) ? null : average.second_year_b1_credits_average + average.second_year_b2_credits_average + average.second_year_b3_credits_average,
                                        (average.second_year_b4_credits_average === null) ? null : average.second_year_b1_credits_average + average.second_year_b2_credits_average + average.second_year_b3_credits_average + average.second_year_b4_credits_average,
                                        (average.second_year_b5_credits_average === null) ? null : average.second_year_b1_credits_average + average.second_year_b2_credits_average + average.second_year_b3_credits_average + average.second_year_b4_credits_average + average.second_year_b5_credits_average,
                                        (average.second_year_b6_credits_average === null) ? null : average.second_year_b1_credits_average + average.second_year_b2_credits_average + average.second_year_b3_credits_average + average.second_year_b4_credits_average + average.second_year_b5_credits_average + average.second_year_b6_credits_average,
                                    ]
                                },
                                {
                                    type: 'line',
                                    borderColor: [

                                        color2Border
                                    ],
                                    fill: false,
                                    label: 'Prognosis',
                                    pointRadius: 0,
                                    spanGaps: true,
                                    data: [

                                        6,
                                        null,
                                        null,
                                        null,
                                        null,
                                        this.student.second_year_credits_expected
                                    ]
                                },
//                                {
//                                    type: 'line',
//                                    borderColor: [
//
//                                        color3Border
//                                    ],
//                                    fill: false,
//                                    label: 'Goal',
//                                    pointRadius: 0,
//                                    spanGaps: true,
//                                    data: [
//
//                                        12,
//                                        null,
//                                        null,
//                                        null,
//                                        null,
//                                        this.student.second_year_credits_goal
//                                    ]
//                                },
                                {
                                    type: 'line',
                                    borderColor: [

                                        color1Border
                                    ],
                                    fill: false,
                                    label: 'Credits this year',
                                    lineTension: 0,
                                    pointBackgroundColor: [

                                        color1
                                    ],
                                    spanGaps: true,
                                    data: [

                                        (this.student.second_year_b1_credits === null) ? null : this.student.second_year_b1_credits,
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

                                display: true,
                                position: 'top'
                            },
                            maintainAspectRatio: false,
                            scales: {

                                yAxes: [{

                                    ticks: {

                                        max: Math.max(...[70, Math.ceil(this.student.second_year_credits / 10) * 10]),
                                        min: 0,
                                        stepSize: 10
                                    }
                                }]
                            },
                            title: {

                                display: true,
                                text: 'Your 2nd year over time'
                            },
                            tooltips: {

                                enabled: false
                            }
                        }
                    });
                });
            }
        },
        mixins: [

            errorAlerts
        ],
        mounted() {

            this.renderCharts();
        },
        props: [

            'student'
        ],
        updated() {

            images.fadeWhenLoaded($(this.$el).find('img'));
        },
        watch: {

            student: function(newValue) {

                this.renderCharts();
            }
        }
    }

</script>