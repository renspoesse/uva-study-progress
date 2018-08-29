import Vue from 'vue'
import * as _ from 'lodash'

import * as json from '../helpers/json'

const get = function() {

    return new Promise((resolve, reject) => {

        Vue.http.get('settings').then((response) => {

                response.json().then((obj) => {

                        obj = json.removeDataWrappers(obj);
                        resolve({item: obj});
                    })
                    .catch((parseError) => {

                        console.log(parseError);
                        reject({message: 'Failed to parse info.'});
                    });
            })
            .catch((response) => {

                console.log(response);
                reject({message: 'Oops. Something went wrong.'});
            });
    });
};

const update = function(payload) {

    return new Promise((resolve, reject) => {

        payload = json.removeEmptyObjects(_.clone(payload));

        Vue.http.patch('settings', payload).then((response) => {

                response.json().then((obj) => {

                        obj = json.removeDataWrappers(obj);
                        resolve({item: obj});
                    })
                    .catch((parseError) => {

                        console.log(parseError);
                        reject({message: 'Failed to parse info data.'});
                    });
            })
            .catch((response) => {

                console.log(response);

                if (response.status === 422) {

                    response.json().then((obj) => {

                            reject({

                                message: "It seems like you didn't provide a valid settings item.",
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

    get,
    update
}