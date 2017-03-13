<template>

    <div>

        <loading-overlay v-show="loading"></loading-overlay>

        <div class="dashhead">
            <div class="dashhead-titles">
                <h2 class="dashhead-subtitle">Manage students</h2>
                <h2 class="dashhead-title">{{ student.display_name }}</h2>
            </div>
        </div>

        <hr class="m-t">

        <alert type="danger" v-bind:message="errorMessage" v-bind:show="errorMessage" v-on:close="errorMessage = ''"></alert>

        <div class="row">
            <div class="col-md-12">

                <p>This page is only meant to give some insight in the raw student data. To edit the data, please use the import functionality.</p>

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

    </div>

</template>

<script>

    import * as _ from 'lodash'
    import errorAlerts from '../../mixins/error_alerts'
    import loadingOverlay from '../../mixins/loading_overlay'

    import * as images from '../../helpers/images'
    import * as students from '../../services/students'

    export default {

        computed: {

            _() { return _; }
        },
        created: function() {

            if (this.$route.params.id)
                this.fetchData(this.$route.params.id);
        },
        data: function() {

            return {

                student: {}
            }
        },
        methods: {

            fetchData: function(id) {

                this.showLoading(true);
                this.displayErrors(false);

                students.getById(id)
                    .then((result) => {

                        this.student = result.item;
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
        ],
        updated () {

            images.fadeWhenLoaded($(this.$el).find('img'));
        }
    }

</script>