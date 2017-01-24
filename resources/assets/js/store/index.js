import Vue from 'vue'
import Vuex from 'vuex'

import auth from './modules/auth'
import enums from './modules/enums'

Vue.use(Vuex);

const debug = true;

export default new Vuex.Store({

    modules: {

        auth,
        enums
    },
    strict: debug
});