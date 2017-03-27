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

const getByAuthenticated = function() {

    return new Promise((resolve, reject) => {

        Vue.http.get('me/student').then((response) => {

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
                        reject({message: 'Failed to parse students.'});
                    });
            })
            .catch((response) => {

                console.log(response);
                reject({message: 'Oops. Something went wrong.'});
            });
    });
};

const getCreditsExpected = function() {

    return new Promise((resolve, reject) => {

        Vue.http.get('students/creditsexpected').then((response) => {

                response.json().then((obj) => {

                        resolve({items: obj});
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

export {

    deleteById,
    getByAuthenticated,
    getById,
    getByParameters,
    getCreditsExpected,
    importFromFile
}