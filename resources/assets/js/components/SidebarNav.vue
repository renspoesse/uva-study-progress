<template>

    <nav class="sidebar-nav">

        <div class="sidebar-header">
            <button class="nav-toggler nav-toggler-sm sidebar-toggler collapsed" type="button" data-toggle="collapse" data-target="#nav-toggleable-sm" aria-expanded="false">
                <span class="sr-only">Toggle nav</span>
            </button>
            <a href="/#/" class="img-wrapper img-size-small pull-left">
                <img src="images/logo.png" class="img-responsive">
            </a>
            <a class="img-wrapper img-size-small pull-left m-l-s" v-if="avatarUrl">
                <img v-bind:src="avatarUrl" class="img-circle img-fit-cover" ref="avatar">
            </a>
        </div>

        <div class="nav-toggleable-sm collapse" id="nav-toggleable-sm" aria-expanded="false">

            <ul class="nav nav-pills nav-stacked">

                <template v-if="isAuthenticated">

                    <template v-if="hasAnyRole(user, roles.Administrator)">

                        <li class="nav-header">View as</li>

                        <li>
                            <select class="form-control" v-model="viewAsId">
                                <option></option>
                                <option v-for="item in viewAsStudents">{{ item.student_number }}</option>
                            </select>
                        </li>

                    </template>

                    <li class="nav-header">Dashboards</li>

                    <router-link tag="li" to="/" exact>
                        <a><i class="fa fa-fw fa-home m-r" aria-hidden="true"></i>Overview</a>
                    </router-link>

                    <!--router-link tag="li" to="/courses">
                        <a><i class="fa fa-fw fa-graduation-cap m-r" aria-hidden="true"></i>Per course</a>
                    </router-link-->

                    <router-link tag="li" to="/news">
                        <a><i class="fa fa-fw fa-newspaper-o m-r" aria-hidden="true"></i>News</a>
                    </router-link>

                    <router-link tag="li" to="/advice" exact>
                        <a><i class="fa fa-fw fa-info m-r" aria-hidden="true"></i>Tips and advice</a>
                    </router-link>

                    <template v-if="hasAnyRole(viewAs, [roles.Instructor, roles.Administrator])">

                        <li class="nav-header">Manage</li>

                        <router-link tag="li" to="/manage/students" v-if="hasAnyRole(viewAs, roles.Administrator)">
                            <a><i class="fa fa-fw fa-database m-r" aria-hidden="true"></i>Students</a>
                        </router-link>

                        <router-link tag="li" to="/manage/news">
                            <a><i class="fa fa-fw fa-newspaper-o m-r" aria-hidden="true"></i>News</a>
                        </router-link>

                        <router-link tag="li" to="/manage/advice">
                            <a><i class="fa fa-fw fa-info m-r" aria-hidden="true"></i>Tips and advice</a>
                        </router-link>

                    </template>

                    <li class="nav-header">{{ displayName }}</li>

                    <router-link tag="li" to="/logout">
                        <a><i class="fa fa-fw fa-sign-out m-r" aria-hidden="true"></i>Log out</a>
                    </router-link>

                </template>
                <template v-else>

                    <router-link tag="li" to="/login">
                        <a><i class="fa fa-fw fa-sign-in m-r" aria-hidden="true"></i>Log in</a>
                    </router-link>

                </template>

            </ul>

            <hr class="visible-xs m-t">

        </div>

    </nav>

</template>

<script>

    import * as _ from 'lodash'
    import {mapGetters, mapState} from 'vuex'

    import Roles from '../enums/Roles'

    import * as roles from '../helpers/roles'
    import * as students from '../services/students'

    export default {

        computed: {

            ...mapGetters({

                avatarUrl: 'auth/avatarUrl',
                displayName: 'auth/displayName',
                isAuthenticated: 'auth/isAuthenticated'
            }),
            ...mapState({

                user: (state) => state.auth.user,
                viewAs: (state) => { return state.auth.viewAs ? state.auth.viewAs : state.auth.user; }
            }),
            roles() { return Roles; }
        },
        created() {

            this.fetchData();
        },
        data: function() {

            return {

                viewAsId: '',
                viewAsStudents: []
            }
        },
        methods: {

            fetchData: function() {

                students.getByParameters({

                        limit: 5,
                        publishedOnly: true
                    })
                    .then((result) => {

                        this.viewAsStudents = result.items;
                    });
            },
            hasAnyRole: roles.hasAnyRole
        },
        watch: {

            viewAsId: function(newValue) {

                if (!_.isEmpty(newValue)) {

                    const student = _.find(this.viewAsStudents, (obj) => {

                        return obj.student_number === parseInt(newValue);
                    });

                    if (student)
                        this.$store.commit('auth/SET_VIEW_AS', {

                            fullname: student.display_name,
                            id: student.id,
                            roles: [Roles.Learner],
                            userId: student.student_number
                        });
                }
                else
                    this.$store.commit('auth/UNSET_VIEW_AS');
            }
        }
    }

</script>