import _ from 'lodash'

const getFormData = function(payload, file) {

    const formData = new FormData();

    _.forEach(payload, (value, key) => {

        if (!_.isObject(value) && !_.isArray(value) && !_.isNull(value) && !_.isUndefined(value))
            formData.append(key, value);
    });

    formData.append('data', file);

    return formData;
};

const removeDataWrappers = function(obj) {

    if (_.isObject(obj) || _.isArray(obj)) {

        const length = _.isArray(obj) ? obj.length : (_.keys(obj)).length;

        if (obj['data']) {

            if ((length === 1 || obj['meta'] && length === 2)) {

                obj = obj['data'];
            }
        }

        _.forEach(obj, (value, key) => {

            obj[key] = removeDataWrappers(obj[key]);
        });
    }

    return obj;
};

const removeEmptyObjects = function(obj) {

    _.forEach(obj, (value, key) => {

        if (_.isArray(value)) {

            obj[key] = _.filter(value, (child) => {return !child._isEmpty;});
        }
        else if (_.isObject(value) && value._isEmpty) {

            delete obj[key];
        }
    });

    return obj;
};

export {

    getFormData,
    removeDataWrappers,
    removeEmptyObjects
}