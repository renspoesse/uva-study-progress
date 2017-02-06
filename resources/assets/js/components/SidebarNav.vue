<template>

    <nav class="sidebar-nav">

        <div class="sidebar-header">
            <button class="nav-toggler nav-toggler-sm sidebar-toggler collapsed" type="button" data-toggle="collapse" data-target="#nav-toggleable-sm" aria-expanded="false">
                <span class="sr-only">Toggle nav</span>
            </button>
            <a class="sidebar-brand img-responsive">
                <!--<span class="icon icon-leaf sidebar-brand-icon"></span>-->
                <img src="images/logo.png">
            </a>
            <a class="sidebar-brand img-responsive m-l" v-if="avatarUrl">
                <img v-bind:src="avatarUrl" class="img-circle" ref="avatar">
            </a>
        </div>

        <div class="nav-toggleable-sm collapse" id="nav-toggleable-sm" aria-expanded="false">

            <ul class="nav nav-pills nav-stacked">

                <template v-if="isAuthenticated">

                    <li class="nav-header">Dashboards</li>

                    <router-link tag="li" to="/" exact>
                        <a><i class="fa fa-fw fa-home m-r" aria-hidden="true"></i>Overview</a>
                    </router-link>

                    <router-link tag="li" to="/courses">
                        <a><i class="fa fa-fw fa-graduation-cap m-r" aria-hidden="true"></i>Per course</a>
                    </router-link>

                    <router-link tag="li" to="/advice" exact>
                        <a><i class="fa fa-fw fa-info m-r" aria-hidden="true"></i>Tips and advice</a>
                    </router-link>

                    <template v-if="hasRole(roles.Administrator) || hasRole(roles.Mentor)">

                        <li class="nav-header">Manage</li>

                        <router-link tag="li" to="/students" v-if="hasRole(roles.Administrator)">
                            <a><i class="fa fa-fw fa-database m-r" aria-hidden="true"></i>Student data</a>
                        </router-link>

                        <router-link tag="li" to="/advice/edit">
                            <a><i class="fa fa-fw fa-info m-r" aria-hidden="true"></i>Tips and advice</a>
                        </router-link>

                        <router-link tag="li" to="/news/edit">
                            <a><i class="fa fa-fw fa-newspaper-o m-r" aria-hidden="true"></i>News</a>
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

    export default {

        computed: {

            ...mapGetters({

                avatarUrl: 'auth/avatarUrl',
                displayName: 'auth/displayName',
                isAuthenticated: 'auth/isAuthenticated',
                userRoles: 'auth/roles'
            }),
            ...mapState({

                user: (state) => state.auth.user
            }),
            roles() { return Roles; }
        },
        methods: {

            hasRole: function(role) {return _.indexOf(this.userRoles, role) > -1;}
        }
    }

</script>