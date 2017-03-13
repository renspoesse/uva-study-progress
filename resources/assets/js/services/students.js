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

export {

    deleteById,
    getById,
    getByParameters
}