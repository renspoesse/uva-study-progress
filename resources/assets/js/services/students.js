import Vue from 'vue'
import * as _ from 'lodash'

import * as json from '../helpers/json'

const deleteByIds = function(ids) {

    if (_.isArray(ids))
        ids = ids.join(',');

    return new Promise((resolve, reject) => {

        Vue.http.delete('students/' + ids).then((response) => {

                resolve({});
            })
            .catch((response) => {

                console.log(response);
                reject({message: 'Oops. Something went wrong.'});
            });
    });
};

const deleteByParameters = function(params) {

    return new Promise((resolve, reject) => {

        Vue.http.delete('students', {

            params: params

        }).then((response) => {

                resolve({});
            })
            .catch((response) => {

                console.log(response);
                reject({message: 'Oops. Something went wrong.'});
            });
    });
};

const getByAuthenticated = function() {

    return new Promise((resolve, reject) => {

        Vue.http.get('me/student').then((response) => {

                response.json().then((obj) => {

                        obj = json.removeDataWrappers(obj);
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

const getById = function(id) {

    return new Promise((resolve, reject) => {

        Vue.http.get('students/' + id).then((response) => {

                response.json().then((obj) => {

                        obj = json.removeDataWrappers(obj);
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
                        reject({message: 'Failed to parse students.'});
                    });
            })
            .catch((response) => {

                console.log(response);
                reject({message: 'Oops. Something went wrong.'});
            });
    });
};

const getCreditsAverage = function() {

    return new Promise((resolve, reject) => {

        Vue.http.get('students/creditsaverage').then((response) => {

                response.json().then((obj) => {

                        obj = json.removeDataWrappers(obj);
                        resolve({item: obj});
                    })
                    .catch((parseError) => {

                        console.log(parseError);
                        reject({message: 'Failed to parse result.'});
                    });
            })
            .catch((response) => {

                console.log(response);
                reject({message: 'Oops. Something went wrong.'});
            });
    });
};

const getProgramSatisfactionAverage = function({cohort, program_code, year}) {

    return new Promise((resolve, reject) => {

        Vue.http.get(`students/programsatisfactionaverage?program_code=${program_code}&cohort=${cohort}&year=${year}`).then((response) => {

                resolve(Number(response.data) || null);
            })
            .catch((response) => {

                console.log(response);
                reject({message: 'Oops. Something went wrong.'});
            });
    });
};

const importFromFile = function(payload, file) {

    return new Promise((resolve, reject) => {

        payload = json.removeEmptyObjects(_.clone(payload));
        const data = json.getFormData(payload, file);

        Vue.http.post('import/students', data).then((response) => {

                resolve({});
            })
            .catch((response) => {

                console.log(response);

                if (response.status === 422) {

                    response.json().then((obj) => {

                            reject({

                                message: "It seems like you didn't provide a valid file.",
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

const updateByAuthenticated = function(payload) {

    return new Promise((resolve, reject) => {

        payload = json.removeEmptyObjects(_.clone(payload));

        Vue.http.patch('me/student', payload).then((response) => {

                response.json().then((obj) => {

                        obj = json.removeDataWrappers(obj);
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

const updateByIds = function(ids, payload) {

    if (_.isArray(ids))
        ids = ids.join(',');

    return new Promise((resolve, reject) => {

        payload = json.removeEmptyObjects(_.clone(payload));

        Vue.http.patch('students/' + ids, payload).then((response) => {

                response.json().then((obj) => {

                        obj = json.removeDataWrappers(obj);
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

const updateByParameters = function(params, payload) {

    return new Promise((resolve, reject) => {

        payload = json.removeEmptyObjects(_.clone(payload));

        Vue.http.patch('students', payload, {

            params: params

        }).then((response) => {

                response.json().then((obj) => {

                        obj = json.removeDataWrappers(obj);
                        resolve({items: obj});
                    })
                    .catch((parseError) => {

                        console.log(parseError);
                        reject({message: 'Failed to parse students.'});
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

    deleteByIds,
    deleteByParameters,
    getByAuthenticated,
    getById,
    getByParameters,
    getCreditsAverage,
    getProgramSatisfactionAverage,
    importFromFile,
    updateByAuthenticated,
    updateByIds,
    updateByParameters
}