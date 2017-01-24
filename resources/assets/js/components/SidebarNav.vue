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
        </div>

        <div class="nav-toggleable-sm collapse" id="nav-toggleable-sm" aria-expanded="false">

            <form class="sidebar-form">
                <input class="form-control" type="text" placeholder="Search...">
                <a class="btn btn-link"><i class="fa fa-search"></i></a>
            </form>

            <ul class="nav nav-pills nav-stacked">

                <template v-if="isAuthenticated">

                    <li class="nav-header">Dashboards</li>

                    <router-link tag="li" to="/" exact>
                        <a><i class="fa fa-fw fa-home m-r" aria-hidden="true"></i>Overview</a>
                    </router-link>
                    <router-link tag="li" to="/donations">
                        <a><i class="fa fa-fw fa-usd m-r" aria-hidden="true"></i>Donations</a>
                    </router-link>
                    <router-link tag="li" to="/leaderboard">
                        <a><i class="fa fa-fw fa-list-ol m-r" aria-hidden="true"></i>Leaderboard</a>
                    </router-link>

                    <li class="nav-header">Manage</li>

                    <router-link tag="li" to="/causes" exact>
                        <a><i class="fa fa-fw fa-heart m-r" aria-hidden="true"></i>Causes</a>
                    </router-link>
                    <router-link tag="li" to="/users">
                        <a><i class="fa fa-fw fa-user m-r" aria-hidden="true"></i>Users</a>
                    </router-link>
                    <router-link tag="li" to="/curators">
                        <a><i class="fa fa-fw fa-check m-r" aria-hidden="true"></i>Curators</a>
                    </router-link>

                    <li class="nav-header">More</li>

                    <router-link tag="li" to="/taxreports">
                        <a><i class="fa fa-fw fa-file-text m-r" aria-hidden="true"></i>Tax reports</a>
                    </router-link>

                    <li class="nav-header">{{ displayName }}</li>

                    <router-link tag="li" to="/account">
                        <a><i class="fa fa-fw fa-cog m-r" aria-hidden="true"></i>Account</a>
                    </router-link>
                    <router-link tag="li" to="/logout">
                        <a><i class="fa fa-fw fa-sign-out m-r" aria-hidden="true"></i>Log out</a>
                    </router-link>

                </template>
                <template v-else>

                    <router-link tag="li" to="/login">
                        <a><i class="fa fa-fw fa-sign-in m-r" aria-hidden="true"></i>Log in</a>
                    </router-link>
                    <router-link tag="li" to="/register">
                        <a><i class="fa fa-fw fa-user-plus m-r" aria-hidden="true"></i>Register</a>
                    </router-link>

                </template>

            </ul>

            <hr class="visible-xs m-t">

        </div>

    </nav>

</template>

<script>

    import {mapGetters, mapState} from 'vuex'

    export default {

        computed: {

            ...mapGetters({

                displayName: 'auth/displayName',
                isAuthenticated: 'auth/isAuthenticated'
            }),
            ...mapState({

                user: (state) => state.auth.user
            })
        }
    }

</script>