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
        students: {

            Import: require('./components/manage/students/Import.vue'),
            List: require('./components/manage/students/List.vue'),
            View: require('./components/manage/students/View.vue')
        }
    },
    pages: {

        Advice: require('./components/pages/Advice.vue'),
        Login: require('./components/pages/Login.vue'),
        News: require('./components/pages/News.vue'),
        NotFound: require('./components/pages/NotFound.vue'),
        Overview: require('./components/pages/Overview.vue')
    }
};

const router = new VueRouter({

    linkActiveClass: 'active',
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
        {path: '/news', component: components.pages.News, meta: {requiresAuth: true}},

        {path: '/manage/advice', component: components.manage.advice.List, meta: {requiresAuth: true, requiresRole: Roles.Instructor}},
        {path: '/manage/advice/new', component: components.manage.advice.Edit, meta: {requiresAuth: true, requiresRole: Roles.Instructor}},
        {path: '/manage/advice/:id/edit', component: components.manage.advice.Edit, meta: {requiresAuth: true, requiresRole: Roles.Instructor}},
        {path: '/manage/news', component: components.manage.news.List, meta: {requiresAuth: true, requiresRole: Roles.Instructor}},
        {path: '/manage/news/new', component: components.manage.news.Edit, meta: {requiresAuth: true, requiresRole: Roles.Instructor}},
        {path: '/manage/news/:id/edit', component: components.manage.news.Edit, meta: {requiresAuth: true, requiresRole: Roles.Instructor}},
        {path: '/manage/students', component: components.manage.students.List, meta: {requiresAuth: true, requiresRole: Roles.Administrator}},
        {path: '/manage/students/import', component: components.manage.students.Import, meta: {requiresAuth: true, requiresRole: Roles.Administrator}},
        {path: '/manage/students/:id', component: components.manage.students.View, meta: {requiresAuth: true, requiresRole: Roles.Administrator}},

        {path: '*', component: components.pages.NotFound}
    ]
});

router.beforeEach((to, from, next) => {

    if (to.matched.some(record => record.meta.requiresAuth)) {

        if (!store.getters['auth/isAuthenticated']) {

            next({

                path: '/login',
                query: {redirect: to.fullPath}
            });
        }
        else {

            const requiredRoles = _.without(_.map(to.matched, 'meta.requiresRole'), undefined);
            const mismatch = _.without(requiredRoles, ...store.getters['auth/roles']);

            if (mismatch.length > 0) {

                console.log('User has insufficient permissions for this route.');
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