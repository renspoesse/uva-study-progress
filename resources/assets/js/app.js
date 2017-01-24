import App from './components/App'
import router from './router'
import store from './store'

/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the body of the page. From here, you may begin adding components to
 * the application, or feel free to tweak this setup for your needs.
 */

// Register app components.

Vue.component('alert', require('./components/Alert.vue'));
Vue.component('pagination', require('./components/Pagination.vue'));
Vue.component('sidebar-nav', require('./components/SidebarNav.vue'));

// Try using an existing session on the server. The getSession action guarantees a resolved promise,
// so the app is created whether there's a session or not.

store.dispatch('auth/getSession').then(() => {

    // Create the app.

    const app = new Vue(Vue.util.extend({

        el: '#app',
        router: router,
        store: store

    }, App));
});