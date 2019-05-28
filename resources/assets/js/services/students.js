import axios from 'axios';
import * as _ from 'lodash';
import * as json from '../helpers/json';

const deleteByIds = function(ids) {

    if (_.isArray(ids))
        ids = ids.join(',');

    return axios.delete('students/' + ids);
};

const deleteByParameters = function(params) {

    return axios.delete('students', {params});
};

const getByAuthenticated = function() {

    return axios.get('me/student')
        .then(response => response.data)
        .then(obj => ({item: json.removeDataWrappers(obj)}));
};

const getById = function(id) {

    return axios.get('students/' + id)
        .then(response => response.data)
        .then(obj => ({item: json.removeDataWrappers(obj)}));
};

const getByParameters = function(params) {

    return axios.get('students', {params})
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

const getCreditsAverage = function({cohort, program_code, year}) {

    return axios.get(`students/creditsaverage?program_code=${program_code}&cohort=${cohort}&year=${year}`)
        .then(response => response.data)
        .then(obj => ({item: json.removeDataWrappers(obj)}));
};

const getProgramSatisfactionAverage = function({cohort, program_code, year}) {

    return axios.get(`students/programsatisfactionaverage?program_code=${program_code}&cohort=${cohort}&year=${year}`)
        .then(response => response.data)
        .then(obj => ({item: json.removeDataWrappers(obj)}));
};

const importFromFile = function(payload, file) {

    payload = json.removeEmptyObjects(_.clone(payload));
    const data = json.getFormData(payload, file);

    return axios.post('import/students', data)
        .catch(error => {

            if (error.response && error.response.status === 422) {

                throw {

                    message: 'It seems like you didn\'t provide a valid file.',
                    errors: error.response.data,
                };
            }
            else
                throw {message: 'Oops. Something went wrong.'};
        });
};

const updateByAuthenticated = function(payload) {

    payload = json.removeEmptyObjects(_.clone(payload));

    return axios.patch('me/student', payload)
        .then(response => response.data)
        .then(obj => ({item: json.removeDataWrappers(obj)}))
        .catch(error => {

            if (error.response && error.response.status === 422) {

                throw {

                    message: 'It seems like you didn\'t provide a valid student.',
                    errors: error.response.data.errors,
                };
            }
            else
                throw {message: 'Oops. Something went wrong.'};
        });
};

const updateByIds = function(ids, payload) {

    if (_.isArray(ids))
        ids = ids.join(',');

    payload = json.removeEmptyObjects(_.clone(payload));

    return axios.patch('students/' + ids, payload)
        .then(response => response.data)
        .then(obj => ({item: json.removeDataWrappers(obj)}))
        .catch(error => {

            if (error.response && error.response.status === 422) {

                throw {

                    message: 'It seems like you didn\'t provide a valid student.',
                    errors: error.response.data.errors,
                };
            }
            else
                throw {message: 'Oops. Something went wrong.'};
        });
};

const updateByParameters = function(params, payload) {

    payload = json.removeEmptyObjects(_.clone(payload));

    return axios.patch('students', payload, {params})
        .then(response => response.data)
        .then(obj => ({items: json.removeDataWrappers(obj)}))
        .catch(error => {

            if (error.response && error.response.status === 422) {

                throw {

                    message: 'It seems like you didn\'t provide a valid student.',
                    errors: error.response.data.errors,
                };
            }
            else
                throw {message: 'Oops. Something went wrong.'};
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
    updateByParameters,
};
