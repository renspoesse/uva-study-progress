<template>

    <nav class="sidebar-nav">

        <div class="sidebar-header">
            <button class="nav-toggler nav-toggler-sm sidebar-toggler collapsed" type="button" data-toggle="collapse" data-target="#nav-toggleable-sm" aria-expanded="false">
                <span class="sr-only">Toggle nav</span>
            </button>
            <router-link to="/" class="img-wrapper img-size-small pull-left">
                <img src="/images/logo.png" class="img-responsive">
            </router-link>
            <a class="img-wrapper img-size-small pull-left m-l-s" v-if="avatarUrl">
                <img v-bind:src="avatarUrl" class="img-circle img-fit-cover" ref="avatar">
            </a>
        </div>

        <div class="nav-toggleable-sm collapse" id="nav-toggleable-sm" aria-expanded="false">

            <ul class="nav nav-pills nav-stacked">

                <template v-if="isAuthenticated">

                    <template v-if="viewAs !== user">

                        <li class="nav-header">View as</li>

                        <li>
                            <a class="btn btn-primary-outline" v-on:click.prevent="handleViewAsYourself">View as yourself again</a>
                        </li>

                    </template>

                    <li class="nav-header">Dashboard</li>

                    <router-link tag="li" to="/" exact>
                        <a><i class="fa fa-fw fa-home m-r" aria-hidden="true"></i>Dashboard</a>
                    </router-link>
                    <router-link tag="li" to="/news">
                        <a><i class="fa fa-fw fa-newspaper-o m-r" aria-hidden="true"></i>Updates</a>
                    </router-link>
                    <router-link tag="li" to="/advice">
                        <a><i class="fa fa-fw fa-info m-r" aria-hidden="true"></i>Info</a>
                    </router-link>
                    <li>
                        <a href="mailto:mydashboard-eb@uva.nl" target="_blank"><i class="fa fa-fw fa-envelope m-r" aria-hidden="true"></i>Contact</a>
                    </li>

                    <template v-if="hasAnyRole(viewAs, [roles.StudyAdviser, roles.Administrator])">

                        <li class="nav-header">Manage</li>

                        <router-link tag="li" to="/manage/students">
                            <a><i class="fa fa-fw fa-database m-r" aria-hidden="true"></i>Students</a>
                        </router-link>
                        <router-link tag="li" to="/manage/news">
                            <a><i class="fa fa-fw fa-newspaper-o m-r" aria-hidden="true"></i>Edit updates</a>
                        </router-link>
                        <router-link tag="li" to="/manage/advice">
                            <a><i class="fa fa-fw fa-info m-r" aria-hidden="true"></i>Edit info</a>
                        </router-link>
                        <router-link tag="li" to="/manage/settings" v-if="hasAnyRole(viewAs, [roles.Administrator])">
                            <a><i class="fa fa-fw fa-cog m-r" aria-hidden="true"></i>Settings</a>
                        </router-link>

                    </template>

                    <li class="nav-header">{{ displayName }}</li>

                    <router-link tag="li" to="/me">
                        <a><i class="fa fa-fw fa-cog m-r" aria-hidden="true"></i>Personalise</a>
                    </router-link>
                    <router-link tag="li" to="/logout">
                        <a><i class="fa fa-fw fa-sign-out m-r" aria-hidden="true"></i>Log out</a>
                    </router-link>

                </template>
                <template v-else>

                    <router-link tag="li" to="/login">
                        <a><i class="fa fa-fw fa-sign-in m-r" aria-hidden="true"></i>Not logged in</a>
                    </router-link>

                </template>

            </ul>

            <hr class="visible-xs m-t">

        </div>

    </nav>

</template>

<script>

    import {mapGetters, mapState} from 'vuex';
    import * as roles from '../helpers/roles';

    export default {

        computed: {

            ...mapGetters({

                avatarUrl: 'auth/avatarUrl',
                displayName: 'auth/displayName',
                isAuthenticated: 'auth/isAuthenticated',
            }),
            ...mapState({

                user: (state) => state.auth.user,
                viewAs: (state) => { return state.auth.viewAs ? state.auth.viewAs : state.auth.user; },
            }),
        },
        methods: {

            handleViewAsYourself: function() {

                this.$store.commit('auth/UNSET_VIEW_AS');
                this.$router.push({path: '/manage/students'});
            },
            hasAnyRole: roles.hasAnyRole,
        },
    };

</script>
