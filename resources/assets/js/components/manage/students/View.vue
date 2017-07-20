<template>

    <div>

        <loading-overlay v-show="loading"></loading-overlay>

        <div class="dashhead">
            <div class="dashhead-titles">
                <h2 class="dashhead-subtitle">Manage</h2>
                <h2 class="dashhead-title">{{ student.display_name }}</h2>
            </div>
        </div>

        <hr class="m-t">

        <alert type="danger" v-bind:message="errorMessage" v-bind:show="errorMessage" v-on:close="errorMessage = ''"></alert>

        <div class="row m-b">
            <div class="col-md-12">
                <div class="btn-group pull-right">
                    <a class="btn btn-default-outline" v-on:click.prevent="handleViewAs"><i class="fa fa-eye"></i></a>
                    <template v-if="hasAnyRole(user, roles.Administrator)">
                        <a class="btn btn-primary-outline" v-on:click.prevent="handlePublish" v-if="!student.is_published"><i class="fa fa-eye m-r-s"></i>Publish</a>
                        <a class="btn btn-default-outline" v-on:click.prevent="handleUnpublish" v-else><i class="fa fa-eye-slash m-r-s"></i>Unpublish</a>
                    </template>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">

                <p>This page is only meant to give some insight in the raw student data. To edit the data, please use the import functionality if you're an Administrator.</p>

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
    import {mapState} from 'vuex'

    import Roles from '../../../enums/Roles'

    import errorAlerts from '../../../mixins/error_alerts'
    import loadingOverlay from '../../../mixins/loading_overlay'

    import * as images from '../../../helpers/images'
    import * as roles from '../../../helpers/roles'
    import * as students from '../../../services/students'

    export default {

        computed: {

            ...mapState({

                user: (state) => state.auth.user
            }),
            _() { return _; },
            roles() { return Roles; }
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
            },
            handlePublish: function() {

                this.showLoading(true);
                this.displayErrors(false);

                students.updateById(this.student.id, {is_published: true})
                    .then((result) => {

                        this.student = result.item;
                        this.showLoading(false);
                    })
                    .catch((ex) => {

                        this.displayErrors(true, ex.message);
                        this.showLoading(false);
                    });
            },
            handleUnpublish: function() {

                this.showLoading(true);
                this.displayErrors(false);

                students.updateById(this.student.id, {is_published: false})
                    .then((result) => {

                        this.student = result.item;
                        this.showLoading(false);
                    })
                    .catch((ex) => {

                        this.displayErrors(true, ex.message);
                        this.showLoading(false);
                    });
            },
            handleViewAs: function() {

                this.$store.commit('auth/SET_VIEW_AS', {

                    fullname: this.student.display_name,
                    id: this.student.id,
                    roles: [Roles.Student]
                });

                this.$router.push({path: '/', query: {viewAs: this.student.id}});
            },
            hasAnyRole: roles.hasAnyRole
        },
        mixins: [

            errorAlerts,
            loadingOverlay
        ],
        watch: {

            '$route' (to, from) {

                if (this.$route.params.id)
                    this.fetchData(this.$route.params.id);
            }
        }
    }

</script>