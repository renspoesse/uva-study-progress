<template>

    <div>

        <div class="dashhead">
            <div class="dashhead-titles">
                <h2 class="dashhead-title">Dashboard for {{ displayName }}</h2>
            </div>
        </div>

        <hr class="m-t">

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

        <div class="row text-center m-t-md">
            <div class="col-md-12 m-b-md">
                <div class="w-lg m-x-auto" style="height: 30rem;">
                    <canvas ref="chartProgramSatisfaction"></canvas>
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

                            <tr v-if="student.first_year_mentor !== null">
                                <td>Mentor</td>
                                <td>{{ student.first_year_mentor }}</td>
                            </tr>
                            <tr v-if="student.wa1 !== null">
                                <td>1st intermediate advice (WA1)</td>
                                <td>{{ getStudentBsaText(student.wa1) }}<i class="fa fa-check m-l-s" v-if="isStudentBsaPositive(student.wa1)"></i></td>
                            </tr>
                            <tr v-if="student.wa1_credits !== null">
                                <td>WA1 credits</td>
                                <td>{{ student.wa1_credits }} ECTS</td>
                            </tr>
                            <tr v-if="student.wa2 !== null">
                                <td>2nd intermediate advice (WA2)</td>
                                <td>{{ getStudentBsaText(student.wa2) }}<i class="fa fa-check m-l-s" v-if="isStudentBsaPositive(student.wa2)"></i></td>
                            </tr>
                            <tr v-if="student.wa2_credits !== null">
                                <td>WA2 credits</td>
                                <td>{{ student.wa2_credits }} ECTS</td>
                            </tr>
                            <tr v-if="student.wa3 !== null">
                                <td>3rd intermediate advice (WA3)</td>
                                <td>{{ getStudentBsaText(student.wa3) }}<i class="fa fa-check m-l-s" v-if="isStudentBsaPositive(student.wa3)"></i></td>
                            </tr>
                            <tr v-if="student.wa3_credits !== null">
                                <td>WA3 credits</td>
                                <td>{{ student.wa3_credits }} ECTS</td>
                            </tr>
                            <tr v-if="student.bsa !== null">
                                <td>BSA</td>
                                <td>{{ getStudentBsaText(student.bsa) }}<i class="fa fa-check m-l-s" v-if="isStudentBsaPositive(student.bsa)"></i></td>
                            </tr>
                            <tr v-if="student.bsa_credits !== null">
                                <td>BSA credits</td>
                                <td>{{ student.bsa_credits }} ECTS</td>
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
                                <th class="header" colspan="2">1st year indicators</th>
                            </tr>
                            </thead>
                            <tbody>

                            <tr v-if="student.first_year_b1_credits !== null">
                                <td>Credits in block 1</td>
                                <td>{{ student.first_year_b1_credits }} ECTS</td>
                            </tr>
                            <tr v-if="student.first_year_b2_credits !== null">
                                <td>Credits in block 2</td>
                                <td>{{ student.first_year_b2_credits }} ECTS</td>
                            </tr>
                            <tr v-if="student.first_year_b3_credits !== null">
                                <td>Credits in block 3</td>
                                <td>{{ student.first_year_b3_credits }} ECTS</td>
                            </tr>
                            <tr v-if="student.first_year_b4_credits !== null">
                                <td>Credits in block 4</td>
                                <td>{{ student.first_year_b4_credits }} ECTS</td>
                            </tr>
                            <tr v-if="student.first_year_b5_credits !== null">
                                <td>Credits in block 5</td>
                                <td>{{ student.first_year_b5_credits }} ECTS</td>
                            </tr>
                            <tr v-if="student.first_year_b6_credits !== null">
                                <td>Credits in block 6</td>
                                <td>{{ student.first_year_b6_credits }} ECTS</td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                </div>

                <a class="btn btn-primary-outline" href="http://student.uva.nl/eco/content/az/study-advisers/study-advisers.html" target="_blank">Contact a study adviser</a>

            </div>
        </div>

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

                const color1 = 'rgba(135, 192, 181, .7)';
                const color3 = 'rgba(135, 192, 181, .4)';

                const color1Border = color1;
                const color3Border = color3;

                const chart1 = new Chart(this.$refs.chartCredits, {

                    type: 'bar',
                    data: {

                        labels: [

                            'Credits this year (' + (this.student.first_year_credits || 'none') + ')',
                            'Goal (' + (this.student.first_year_credits_goal || 'none') + ')',
                        ],
                        datasets: [{

                            backgroundColor: [

                                color1,
                                color3
                            ],
                            borderColor: [

                                color1Border,
                                color3Border
                            ],
                            borderWidth: 1,
                            data: [

                                this.student.first_year_credits,
                                this.student.first_year_credits_goal,
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

                                    max: Math.max(...[70, Math.ceil(this.student.first_year_credits / 10) * 10, Math.ceil(this.student.first_year_credits_goal / 10) * 10]),
                                    min: 0,
                                    stepSize: 10
                                }
                            }]
                        },
                        title: {

                            display: true,
                            text: 'Your 1st year'
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

                                        (average.first_year_b1_credits_average === null) ? null : average.first_year_b1_credits_average,
                                        (average.first_year_b2_credits_average === null) ? null : average.first_year_b1_credits_average + average.first_year_b2_credits_average,
                                        (average.first_year_b3_credits_average === null) ? null : average.first_year_b1_credits_average + average.first_year_b2_credits_average + average.first_year_b3_credits_average,
                                        (average.first_year_b4_credits_average === null) ? null : average.first_year_b1_credits_average + average.first_year_b2_credits_average + average.first_year_b3_credits_average + average.first_year_b4_credits_average,
                                        (average.first_year_b5_credits_average === null) ? null : average.first_year_b1_credits_average + average.first_year_b2_credits_average + average.first_year_b3_credits_average + average.first_year_b4_credits_average + average.first_year_b5_credits_average,
                                        (average.first_year_b6_credits_average === null) ? null : average.first_year_b1_credits_average + average.first_year_b2_credits_average + average.first_year_b3_credits_average + average.first_year_b4_credits_average + average.first_year_b5_credits_average + average.first_year_b6_credits_average,
                                    ]
                                },
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

                                        (this.student.first_year_b1_credits === null) ? null : this.student.first_year_b1_credits,
                                        (this.student.first_year_b2_credits === null) ? null : this.student.first_year_b1_credits + this.student.first_year_b2_credits,
                                        (this.student.first_year_b3_credits === null) ? null : this.student.first_year_b1_credits + this.student.first_year_b2_credits + this.student.first_year_b3_credits,
                                        (this.student.first_year_b4_credits === null) ? null : this.student.first_year_b1_credits + this.student.first_year_b2_credits + this.student.first_year_b3_credits + this.student.first_year_b4_credits,
                                        (this.student.first_year_b5_credits === null) ? null : this.student.first_year_b1_credits + this.student.first_year_b2_credits + this.student.first_year_b3_credits + this.student.first_year_b4_credits + this.student.first_year_b5_credits,
                                        (this.student.first_year_b6_credits === null) ? null : this.student.first_year_b1_credits + this.student.first_year_b2_credits + this.student.first_year_b3_credits + this.student.first_year_b4_credits + this.student.first_year_b5_credits + this.student.first_year_b6_credits,
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

                                        max: Math.max(...[70, Math.ceil(this.student.first_year_credits / 10) * 10]),
                                        min: 0,
                                        stepSize: 10
                                    }
                                }]
                            },
                            title: {

                                display: true,
                                text: 'Your 1st year over time'
                            },
                            tooltips: {

                                enabled: false
                            }
                        }
                    });
                });

                students.getProgramSatisfactionAverage(this.student).then((average) => {

                    const chart3 = new Chart(this.$refs.chartProgramSatisfaction, {

                        type: 'bar',
                        data: {

                            labels: [

                                'Me (' + (this.student.program_satisfaction || 'N/A') + ')',
                                'Peer group (' + (average || 'N/A') + ')'
                            ],
                            datasets: [{

                                backgroundColor: [

                                    color1,
                                    color3
                                ],
                                borderColor: [

                                    color1Border,
                                    color3Border
                                ],
                                borderWidth: 1,
                                data: [

                                    this.student.program_satisfaction,
                                    average,
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

                                        max: 10,
                                        min: 0,
                                        stepSize: 1
                                    }
                                }]
                            },
                            title: {

                                display: true,
                                text: 'Satisfaction'
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