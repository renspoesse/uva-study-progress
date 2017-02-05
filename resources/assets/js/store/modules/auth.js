import Vue from 'vue'

import * as json from '../../helpers/json'

const state = {

    user: null
};

const getters = {

    avatarUrl: (state) => {

        if (!state.user) return '';
        return state.user.image;
    },
    displayName: (state) => {

        if (!state.user) return '';
        return state.user.fullname;
    },
    isAuthenticated: (state) => { return !!state.user; },
    roles: (state) => {

        if (!state.user) return [];
        return state.user.roles;
    }
};

const mutations = {

    SET_USER (state, payload) {state.user = payload;},
    UNSET_USER (state) {state.user = null;}
};

const actions = {

    getSession ({commit}) {

        return new Promise((resolve, reject) => {

            Vue.http.get('me').then((response) => {

                    response.json().then((obj) => {

                            obj = json.removeDataWrappers(obj);

                            commit('SET_USER', obj);
                            resolve();
                        })
                        .catch((parseError) => {

                            console.log(parseError);

                            commit('UNSET_USER');
                            resolve();
                        });
                })
                .catch(() => {

                    commit('UNSET_USER');
                    resolve();
                });
        });
    },
    logout ({commit}) {

        return new Promise((resolve, reject) => {

            Vue.http.post('logout').then(() => {

                    commit('UNSET_USER');
                    resolve();
                })
                .catch((response) => {

                    console.log(response);
                    reject({});
                });
        });
    }
};

export default {

    namespaced: true,

    state,
    getters,
    mutations,
    actions
}