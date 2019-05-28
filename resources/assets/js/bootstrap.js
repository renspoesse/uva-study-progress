import axios from 'axios';
import _ from 'lodash';
import moment from 'moment';
import Vue from 'vue';
import VueRouter from 'vue-router';
import Vuex from 'vuex';
import Roles from './enums/Roles';

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

axios.defaults.baseURL = '/api';
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {

    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}
else {

    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo'

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     encrypted: true
// });

moment.fn.toJSON = function() { return this.format('YYYY-MM-DD HH:mm:ss'); };

Vue.prototype._ = _;
Vue.prototype.moment = moment;
Vue.prototype.roles = Roles;

// Enable Vuex and routing.

Vue.use(Vuex);
Vue.use(VueRouter);
