import Vue from 'vue'
import Vuex from 'vuex'

import auth from './modules/auth'

Vue.use(Vuex);

const debug = true;

export default new Vuex.Store({

    modules: {

        auth
    },
    strict: debug
});