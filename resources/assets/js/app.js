import Vue from 'vue'

import App from './components/App.vue'
import router from './router'
import store from './store'

window.debug = false;

/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// Register app components.

Vue.component('alert', require('./components/_controls/Alert.vue'));
Vue.component('loading-overlay', require('./components/_controls/LoadingOverlay.vue'));
Vue.component('pagination', require('./components/_controls/Pagination.vue'));

Vue.component('sidebar-nav', require('./components/SidebarNav.vue'));

// Create a global event bus.

const eventBus = new Vue();

// Try using an existing session on the server. The getSession action guarantees a resolved promise,
// so the app is created whether there's a session or not.

let app;

store.dispatch('auth/getSession').then(() => {

    // Create the app.

    window.app = app = new Vue(Vue.util.extend({

        el: '#app',
        router: router,
        store: store

    }, App));
});

export {

    app,
    eventBus
}