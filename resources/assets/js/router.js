import * as _ from 'lodash'
import {sync} from 'vuex-router-sync'
import Vue from 'vue'
import VueRouter from 'vue-router'

import Roles from './enums/Roles'

import store from './store'

Vue.use(VueRouter);

const components = {

    advice: {

        Edit: require('./components/advice/Edit.vue'),
        List: require('./components/advice/List.vue')
    },
    news: {

        Edit: require('./components/news/Edit.vue'),
        List: require('./components/news/List.vue')
    },
    pages: {

        Login: require('./components/pages/Login.vue'),
        NotFound: require('./components/pages/NotFound.vue'),
        Overview: require('./components/pages/Overview.vue')
    },
    students: {

        Import: require('./components/students/Import.vue'),
        List: require('./components/students/List.vue'),
        View: require('./components/students/View.vue')
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
        {path: '/advice', component: components.advice.List, meta: {requiresAuth: true, requiresRole: Roles.Instructor}},
        {path: '/advice/new', component: components.advice.Edit, meta: {requiresAuth: true, requiresRole: Roles.Instructor}},
        {path: '/advice/:id/edit', component: components.advice.Edit, meta: {requiresAuth: true, requiresRole: Roles.Instructor}},
        {path: '/news', component: components.news.List, meta: {requiresAuth: true, requiresRole: Roles.Instructor}},
        {path: '/news/new', component: components.news.Edit, meta: {requiresAuth: true, requiresRole: Roles.Instructor}},
        {path: '/news/:id/edit', component: components.news.Edit, meta: {requiresAuth: true, requiresRole: Roles.Instructor}},
        {path: '/students', component: components.students.List, meta: {requiresAuth: true, requiresRole: Roles.Administrator}},
        {path: '/students/import', component: components.students.Import, meta: {requiresAuth: true, requiresRole: Roles.Administrator}},
        {path: '/students/:id', component: components.students.View, meta: {requiresAuth: true, requiresRole: Roles.Administrator}},

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