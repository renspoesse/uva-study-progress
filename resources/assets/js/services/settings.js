import axios from 'axios';
import * as _ from 'lodash';
import * as json from '../helpers/json';

const get = function() {

    return axios.get('settings')
        .then(response => response.data)
        .then(obj => ({item: json.removeDataWrappers(obj)}));
};

const update = function(payload) {

    payload = json.removeEmptyObjects(_.clone(payload));

    return axios.patch('settings', payload)
        .then(response => response.data)
        .then(obj => ({item: json.removeDataWrappers(obj)}))
        .catch(error => {

            if (error.response && error.response.status === 422) {

                throw {

                    message: 'It seems like you didn\'t provide a valid settings item.',
                    errors: error.response.data.errors,
                };
            }
            else
                throw {message: 'Oops. Something went wrong.'};
        });
};

export {

    get,
    update,
};
