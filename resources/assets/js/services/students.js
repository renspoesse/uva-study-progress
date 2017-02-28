import Vue from 'vue'

import * as json from '../helpers/json'

const deleteById = function(id) {

    return new Promise((resolve, reject) => {

        Vue.http.delete('students/' + id).then((response) => {

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

        Vue.http.get('students/' + id).then((response) => {

                response.json().then((obj) => {

                        resolve({item: obj});
                    })
                    .catch((parseError) => {

                        console.log(parseError);
                        reject({message: 'Failed to parse student.'});
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

        Vue.http.get('students', {

            params: params

        }).then((response) => {

                response.json().then((obj) => {

                        const meta = _.clone(obj);
                        delete meta.data;

                        resolve({items: obj.data, meta: {

                            pagination: {

                                currentPage: obj.current_page,
                                totalPages: obj.last_page
                            }
                        }});
                    })
                    .catch((parseError) => {

                        console.log(parseError);
                        reject({message: 'Failed to parse students.'});
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

        Vue.http.post('students', payload).then((response) => {

                response.json().then((obj) => {

                        resolve({item: obj});
                    })
                    .catch((parseError) => {

                        console.log(parseError);
                        reject({message: 'Failed to parse student data.'});
                    });
            })
            .catch((response) => {

                console.log(response);

                if (response.status === 422) {

                    response.json().then((obj) => {

                            reject({

                                message: "It seems like you didn't provide a valid student.",
                                errors: obj.errors
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

    return payload.id ? update(payload.id, payload) : save(payload);
};

const update = function(id, payload) {

    return new Promise((resolve, reject) => {

        payload = json.removeEmptyObjects(_.clone(payload));

        Vue.http.patch('students/' + id, payload).then((response) => {

                response.json().then((obj) => {

                        resolve({item: obj});
                    })
                    .catch((parseError) => {

                        console.log(parseError);
                        reject({message: 'Failed to parse student data.'});
                    });
            })
            .catch((response) => {

                console.log(response);

                if (response.status === 422) {

                    response.json().then((obj) => {

                            reject({

                                message: "It seems like you didn't provide a valid student.",
                                errors: obj.errors
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
    update
}