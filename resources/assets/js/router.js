import {sync} from 'vuex-router-sync'
import Vue from 'vue'
import VueRouter from 'vue-router'

import store from './store'

Vue.use(VueRouter);

const components = {

    pages: {

        Login: require('./components/pages/Login.vue'),
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

                if (store.getters['auth/isAuthenticated']) {

                    store.dispatch('auth/logout').then(() => {

                            next('/login');
                        })
                        .catch((ex) => {

                            next(false);
                        });
                }
            }
        },

        {path: '/', component: components.pages.Overview, meta: {requiresAuth: true}},

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
        else
            next();
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