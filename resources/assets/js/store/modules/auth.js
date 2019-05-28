import axios from 'axios';
import * as json from '../../helpers/json';

const state = {

    user: null,
    viewAs: null,
};

const getters = {

    avatarUrl: state => {

        if (state.viewAs) {

            return _.get(state, 'viewAs.image');
        }

        return _.get(state, 'user.image');
    },
    displayName: state => {

        if (state.viewAs) {

            return _.get(state, 'viewAs.fullname');
        }

        return _.get(state, 'user.fullname');
    },
    isAuthenticated: state => {

        return !!state.user;
    },
    permissions: () => {

        return [];
    },
    roles: state => {

        if (state.viewAs) {

            return _.get(state, 'viewAs.roles', []);
        }

        return _.get(state, 'user.roles', []);
    },
};

const mutations = {

    SET_USER(state, payload) {state.user = payload;},
    SET_VIEW_AS(state, payload) {state.viewAs = payload;},
    UNSET_USER(state) {state.user = null;},
    UNSET_VIEW_AS(state) {state.viewAs = null;},
};

const actions = {

    getSession({commit}) {

        return axios.get('me')
            .then(response => {

                commit('SET_USER', json.removeDataWrappers(response.data));
            })
            .catch(() => {

                commit('UNSET_USER');
                commit('UNSET_VIEW_AS');
            });
    },
    logout({commit}) {

        return axios.post('logout').then(() => {

            commit('UNSET_USER');
            commit('UNSET_VIEW_AS');
        });
    }
};

export default {

    actions,
    getters,
    mutations,
    namespaced: true,
    state,
};
