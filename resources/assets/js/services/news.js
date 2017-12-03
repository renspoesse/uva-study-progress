import Vue from 'vue'
import * as _ from 'lodash'

import * as json from '../helpers/json'

const deleteByIds = function(ids) {

    if (_.isArray(ids))
        ids = ids.join(',');

    return new Promise((resolve, reject) => {

        Vue.http.delete('news/' + ids).then((response) => {

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

                        obj = json.removeDataWrappers(obj);
                        resolve({item: obj});
                    })
                    .catch((parseError) => {

                        console.log(parseError);
                        reject({message: 'Failed to parse update.'});
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

                        obj = json.removeDataWrappers(obj.data);

                        resolve({

                            items: obj, meta: {

                                pagination: {

                                    currentPage: meta.current_page,
                                    totalPages: meta.last_page
                                }
                            }
                        });
                    })
                    .catch((parseError) => {

                        console.log(parseError);
                        reject({message: 'Failed to parse update.'});
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
                        reject({message: 'Failed to parse update data.'});
                    });
            })
            .catch((response) => {

                console.log(response);

                if (response.status === 422) {

                    response.json().then((obj) => {

                            reject({

                                message: "It seems like you didn't provide a valid update item.",
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
                        reject({message: 'Failed to parse update data.'});
                    });
            })
            .catch((response) => {

                console.log(response);

                if (response.status === 422) {

                    response.json().then((obj) => {

                            reject({

                                message: "It seems like you didn't provide a valid update item.",
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

    deleteByIds,
    getById,
    getByParameters,
    save,
    saveOrUpdate,
    updateById
}