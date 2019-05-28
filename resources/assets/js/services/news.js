import axios from 'axios';
import * as _ from 'lodash';
import * as json from '../helpers/json';

const deleteByIds = function(ids) {

    if (_.isArray(ids))
        ids = ids.join(',');

    return axios.delete('news/' + ids);
};

const getById = function(id) {

    return axios.get('news/' + id)
        .then(response => response.data)
        .then(obj => ({item: json.removeDataWrappers(obj)}));
};

const getByParameters = function(params) {

    return axios.get('news', {params})
        .then(response => response.data)
        .then(obj => {

            const meta = _.clone(obj);
            delete meta.data;

            obj = json.removeDataWrappers(obj.data);

            return {

                items: obj,
                meta: {

                    pagination: {

                        currentPage: meta.current_page,
                        totalPages: meta.last_page,
                    },
                },
            };
        });
};

const save = function(payload) {

    payload = json.removeEmptyObjects(_.clone(payload));

    return axios.post('news', payload)
        .then(response => response.data)
        .then(obj => ({item: json.removeDataWrappers(obj)}))
        .catch(error => {

            if (error.response && error.response.status === 422) {

                throw {

                    message: 'It seems like you didn\'t provide a valid update item.',
                    errors: error.response.data.errors,
                };
            }
            else
                throw {message: 'Oops. Something went wrong.'};
        });
};

const saveOrUpdate = function(payload) {

    return payload.id ? updateById(payload.id, payload) : save(payload);
};

const updateById = function(id, payload) {

    payload = json.removeEmptyObjects(_.clone(payload));

    return axios.patch('news/' + id, payload)
        .then(response => response.data)
        .then(obj => ({item: json.removeDataWrappers(obj)}))
        .catch(error => {

            if (error.response && error.response.status === 422) {

                throw {

                    message: 'It seems like you didn\'t provide a valid update item.',
                    errors: error.response.data.errors,
                };
            }
            else
                throw {message: 'Oops. Something went wrong.'};
        });
};

export {

    deleteByIds,
    getById,
    getByParameters,
    save,
    saveOrUpdate,
    updateById,
};
