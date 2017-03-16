import Vue from 'vue'

import * as json from '../../helpers/json'

const state = {

    user: null,
    viewAs: null
};

const getters = {

    avatarUrl: (state) => {

        if (state.viewAs) {

            return _.get(state, 'viewAs.image');
        }

        return _.get(state, 'user.image');
    },
    displayName: (state) => {

        if (state.viewAs) {

            return _.get(state, 'viewAs.fullname');
        }

        return _.get(state, 'user.fullname');
    },
    isAuthenticated: (state) => {

        return !!state.user;
    },
    roles: (state) => {

        if (state.viewAs) {

            return _.get(state, 'viewAs.roles', []);
        }

        return _.get(state, 'user.roles', []);
    }
};

const mutations = {

    SET_USER (state, payload) {state.user = payload;},
    SET_VIEW_AS (state, payload) {state.viewAs = payload;},
    UNSET_USER (state) {state.user = null;},
    UNSET_VIEW_AS (state) {state.viewAs = null;}
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
                            commit('UNSET_VIEW_AS');
                            resolve();
                        });
                })
                .catch(() => {

                    commit('UNSET_USER');
                    commit('UNSET_VIEW_AS');
                    resolve();
                });
        });
    },
    logout ({commit}) {

        return new Promise((resolve, reject) => {

            Vue.http.post('logout').then(() => {

                    commit('UNSET_USER');
                    commit('UNSET_VIEW_AS');
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