import * as _ from 'lodash'
import Modernizr from 'modernizr'
import moment from 'moment'
import numeral from 'numeral'
import Vue from 'vue'
import VueResource from 'vue-resource'

window._ = _; // Makes debugging in the console a bit easier.

window.Project = {

    api: '/api',
    csrfToken: $('meta[name="csrf-token"]').attr('content')
};

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

//window.$ = window.jQuery = require('jquery');
//require('bootstrap-sass');

/**
 * Vue is a modern JavaScript library for building interactive web interfaces
 * using reactive data binding and reusable components. Vue's API is clean
 * and simple, leaving you to focus on building your next great project.
 */

window.Vue = Vue;

/**
 * Register VueResource with Vue.
 * This allows us to access the $http service on every component using this.$http*
 */

Vue.use(VueResource);

/**
 * We'll register a HTTP interceptor to attach the "CSRF" header to each of
 * the outgoing requests issued by this application. The CSRF middleware
 * included with Laravel will automatically verify the header's value.
 */

Vue.http.options.root = Project.api;
//Vue.http.headers.common['Content-Type'] = 'application/json';

Vue.http.interceptors.push(function(request, next) {

    request.headers.set('X-CSRF-TOKEN', Project.csrfToken);
    next();
});

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo'

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: 'your-pusher-key'
// });

/**
 * Other stuff.
 */

moment.fn.toJSON = function() { return this.format('YYYY-MM-DD HH:mm:ss'); };

window.trans = (string, args) => {

    let value = _.get(window.i18n, string);

    _.eachRight(args, (paramVal, paramKey) => {

        value = _.replace(value, `:${paramKey}`, paramVal);
    });

    return value;
};

Vue.prototype.window = window;
Vue.prototype._ = _;
Vue.prototype.$ = $;
Vue.prototype.Modernizr = Modernizr;
Vue.prototype.moment = moment;
Vue.prototype.numeral = numeral;
Vue.prototype.i18n = window.i18n;
Vue.prototype.trans = window.trans;