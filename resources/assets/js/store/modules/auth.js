const state = {

    user: null
};

const getters = {

    displayName: (state) => {

        if (!state.user) return '';

        return 'Student number?';
    },
    isAuthenticated: (state) => { return !!state.user; }
};

const mutations = {

    SET_USER (state, payload) {state.user = payload;},
    UNSET_USER (state) {state.user = null;}
};

const actions = {

    getSession ({commit}) {

        return new Promise((resolve, reject) => {

            resolve(); // TODO RENS: login.

            /*
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
                */
        });
    },
    logout ({commit}) {

        return new Promise((resolve, reject) => {

            resolve(); // TODO RENS: logout.

            /*
            Vue.http.post(Kinder.web + '/logout').then(() => {

                    commit('UNSET_USER');
                    resolve();
                })
                .catch((response) => {

                    console.log(response);
                    reject({});
                });
                */
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