import * as _ from 'lodash'
import {sync} from 'vuex-router-sync'
import Vue from 'vue'
import VueRouter from 'vue-router'

import Roles from './enums/Roles'

import store from './store'

Vue.use(VueRouter);

const components = {

    manage: {

        advice: {

            Edit: require('./components/manage/advice/Edit.vue'),
            List: require('./components/manage/advice/List.vue')
        },
        news: {

            Edit: require('./components/manage/news/Edit.vue'),
            List: require('./components/manage/news/List.vue')
        },
        settings: {

            Index: require('./components/manage/settings/Index.vue'),
        },
        students: {

            Import: require('./components/manage/students/Import.vue'),
            List: require('./components/manage/students/List.vue'),
            View: require('./components/manage/students/View.vue')
        }
    },
    pages: {

        Advice: require('./components/pages/Advice.vue'),
        Login: require('./components/pages/Login.vue'),
        Me: require('./components/pages/Me.vue'),
        News: require('./components/pages/News.vue'),
        NotFound: require('./components/pages/NotFound.vue'),
        Overview: require('./components/pages/Overview.vue')
    }
};

const router = new VueRouter({

    linkActiveClass: 'active',
    mode: 'history',
    routes: [

        {path: '/login', component: components.pages.Login, meta: {guestsOnly: true}},

        {
            path: '/logout',
            beforeEnter: (to, from, next) => {

                store.dispatch('auth/logout').then(() => {

                        next('/login');
                    })
                    .catch((ex) => {

                        next(false);
                    });
            }
        },

        {path: '/', component: components.pages.Overview, meta: {requiresAuth: true}},
        {path: '/advice', component: components.pages.Advice, meta: {requiresAuth: true}},
        {path: '/me', component: components.pages.Me, meta: {requiresAuth: true}},
        {path: '/news', component: components.pages.News, meta: {requiresAuth: true}},

        {path: '/manage/advice', component: components.manage.advice.List, meta: {requiresAuth: true, requiresRole: [Roles.StudyAdviser, Roles.Administrator]}},
        {path: '/manage/advice/new', component: components.manage.advice.Edit, meta: {requiresAuth: true, requiresRole: [Roles.StudyAdviser, Roles.Administrator]}},
        {path: '/manage/advice/:id/edit', component: components.manage.advice.Edit, meta: {requiresAuth: true, requiresRole: [Roles.StudyAdviser, Roles.Administrator]}},
        {path: '/manage/news', component: components.manage.news.List, meta: {requiresAuth: true, requiresRole: [Roles.StudyAdviser, Roles.Administrator]}},
        {path: '/manage/news/new', component: components.manage.news.Edit, meta: {requiresAuth: true, requiresRole: [Roles.StudyAdviser, Roles.Administrator]}},
        {path: '/manage/news/:id/edit', component: components.manage.news.Edit, meta: {requiresAuth: true, requiresRole: [Roles.StudyAdviser, Roles.Administrator]}},
        {path: '/manage/settings', component: components.manage.settings.Index, meta: {requiresAuth: true, requiresRole: [Roles.Administrator]}},
        {path: '/manage/students', component: components.manage.students.List, meta: {requiresAuth: true, requiresRole: [Roles.StudyAdviser, Roles.Administrator]}},
        {path: '/manage/students/import', component: components.manage.students.Import, meta: {requiresAuth: true, requiresRole: Roles.Administrator}},
        {path: '/manage/students/:id', component: components.manage.students.View, meta: {requiresAuth: true, requiresRole: [Roles.StudyAdviser, Roles.Administrator]}},

        {path: '*', component: components.pages.NotFound}
    ],
    scrollBehavior (to, from, savedPosition) {

        if (savedPosition)
            return savedPosition;
        else
            return {x: 0, y: 0};
    }
});

router.afterEach((to, from) => {

    const viewAs = store.state.auth.viewAs;

    if (viewAs && !_.has(to, 'query.viewAs')) {

        router.replace({

            query: _.extend({

                viewAs: viewAs.id

            }, to.query)
        });
    }
});

router.beforeEach((to, from, next) => {

    const viewAs = store.state.auth.viewAs;
    const viewAsId = _.get(to, 'query.viewAs');

    if (viewAsId && !viewAs) {

        store.commit('auth/SET_VIEW_AS', {

            fullname: 'ID ' + viewAsId,
            id: viewAsId,
            roles: [Roles.Student]
        });
    }

    if (to.matched.some(record => record.meta.requiresAuth)) {

        if (!store.getters['auth/isAuthenticated']) {

            next({

                path: '/login',
                query: {redirect: to.fullPath}
            });
        }
        else {

            const requiredPermissions = _.flattenDeep(_.without(_.map(to.matched, 'meta.requiresPermission'), undefined));
            const requiredRoles = _.flattenDeep(_.without(_.map(to.matched, 'meta.requiresRole'), undefined));

            const mismatchPermissions = _.without(requiredPermissions, ...store.getters['auth/permissions']);
            const mismatchRoles = _.without(requiredRoles, ...store.getters['auth/roles']);

            if (requiredPermissions.length > 0 && mismatchPermissions.length >= requiredPermissions.length) {

                console.log('User has insufficient permissions for this route.');
                next(false);
            }
            else if (requiredRoles.length > 0 && mismatchRoles.length >= requiredRoles.length) {

                console.log('User has insufficient roles for this route.');
                next(false);
            }
            else
                next();
        }
    }
    else if (to.matched.some(record => record.meta.guestsOnly)) {

        if (store.getters['auth/isAuthenticated']) {

            // Logout before continuing navigation.

            store.dispatch('auth/logout').then(() => {

                    next();
                })
                .catch((ex) => {

                    next(false);
                });
        }
        else
            next(); // Continue navigation - we're already logged out.
    }
    else
        next();
});

sync(store, router); // Sync the current route with the store.

export default router