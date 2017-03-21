import * as _ from 'lodash'

const getDefaultParameters = function(params) {

    return _.defaults(params, {

        limit: 15
    });
};

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

            if (length === 1) {

                obj = obj['data'];
            }
            else if (length === 2 && obj['meta']) {

                obj = zipMetaData(obj['data'], obj['meta']); // Zip any meta properties into the data values, if applicable.
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

            // We need to set the key to null to allow removing a value from a model by patching / updating.
            // We need to delete the key because of ... ?

            obj[key] = null; // Was: delete obj[key];
        }
    });

    return obj;
};

const zipMetaData = function(data, metaArray) {

    if (_.isArray(data)) {

        _.forEach(metaArray, (meta) => {

            _.forEach(meta, (value) => {

                if (value.id) {

                    const match = _.find(data, {id: value.id});

                    if (match)
                        match.meta = _.extend(match.meta, value);
                }
            });
        });
    }
    else {

        data.meta = metaArray.object;
    }

    return data;
};

export {

    getDefaultParameters,
    getFormData,
    removeDataWrappers,
    removeEmptyObjects,
    zipMetaData
}