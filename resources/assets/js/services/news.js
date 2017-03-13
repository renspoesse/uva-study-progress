import Vue from 'vue'

import * as json from '../helpers/json'

const deleteById = function(id) {

    return new Promise((resolve, reject) => {

        Vue.http.delete('news/' + id).then((response) => {

                resolve({});
            })
            .catch((response) => {

                console.log(response);
                reject({message: 'Oops. Something went wrong.'});
            });
    });
};

const getById = function(id) {

    return new Promise((resolve, reject) => {

        Vue.http.get('news/' + id).then((response) => {

                response.json().then((obj) => {

                        resolve({item: obj});
                    })
                    .catch((parseError) => {

                        console.log(parseError);
                        reject({message: 'Failed to parse news.'});
                    });
            })
            .catch((response) => {

                console.log(response);
                reject({message: 'Oops. Something went wrong.'});
            });
    });
};

const getByParameters = function(params) {

    return new Promise((resolve, reject) => {

        Vue.http.get('news', {

            params: params

        }).then((response) => {

                response.json().then((obj) => {

                        const meta = _.clone(obj);
                        delete meta.data;

                        resolve({
                            items: obj.data, meta: {

                                pagination: {

                                    currentPage: obj.current_page,
                                    totalPages: obj.last_page
                                }
                            }
                        });
                    })
                    .catch((parseError) => {

                        console.log(parseError);
                        reject({message: 'Failed to parse news.'});
                    });
            })
            .catch((response) => {

                console.log(response);
                reject({message: 'Oops. Something went wrong.'});
            });
    });
};

const save = function(payload) {

    return new Promise((resolve, reject) => {

        payload = json.removeEmptyObjects(_.clone(payload));

        Vue.http.post('news', payload).then((response) => {

                response.json().then((obj) => {

                        obj = json.removeDataWrappers(obj);
                        resolve({item: obj});
                    })
                    .catch((parseError) => {

                        console.log(parseError);
                        reject({message: 'Failed to parse news data.'});
                    });
            })
            .catch((response) => {

                console.log(response);

                if (response.status === 422) {

                    response.json().then((obj) => {

                            reject({

                                message: "It seems like you didn't provide a valid news item.",
                                errors: obj
                            });
                        })
                        .catch((parseError) => {

                            console.log(parseError);
                            reject({message: 'Failed to parse error data.'});
                        });
                }
                else
                    reject({message: 'Oops. Something went wrong.'});
            });
    });
};

const saveOrUpdate = function(payload) {

    return payload.id ? updateById(payload.id, payload) : save(payload);
};

const updateById = function(id, payload) {

    return new Promise((resolve, reject) => {

        payload = json.removeEmptyObjects(_.clone(payload));

        Vue.http.patch('news/' + id, payload).then((response) => {

                response.json().then((obj) => {

                        obj = json.removeDataWrappers(obj);
                        resolve({item: obj});
                    })
                    .catch((parseError) => {

                        console.log(parseError);
                        reject({message: 'Failed to parse news data.'});
                    });
            })
            .catch((response) => {

                console.log(response);

                if (response.status === 422) {

                    response.json().then((obj) => {

                            reject({

                                message: "It seems like you didn't provide a valid news item.",
                                errors: obj
                            });
                        })
                        .catch((parseError) => {

                            console.log(parseError);
                            reject({message: 'Failed to parse error data.'});
                        });
                }
                else
                    reject({message: 'Oops. Something went wrong.'});
            });
    });
};

export {

    deleteById,
    getById,
    getByParameters,
    save,
    saveOrUpdate,
    updateById
}