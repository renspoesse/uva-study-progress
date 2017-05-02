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

const removeDataWrappers = function(obj, metaContainer, metaPath) {

    let data, meta;

    // Only if we're dealing with an object or array, we can check for data wrappers and metadata.
    // NOTE: arrays are also considered objects!

    if (_.isObject(obj) || _.isArray(obj)) {

        // First, try removing the data wrapper and pulling any metadata. We need to make sure that we're dealing with an actual data wrapper and not a field that is just named 'data'!

        if (!_.isArray(obj)) {

            const keys = _.keys(obj);

            if ((keys.length === 1 && obj.data) || (keys.length === 2 && obj.data && obj.meta))
                data = obj.data;
        }

        meta = obj.meta;

        // If there was no data wrapper, consider the whole object as data - but without any metadata.

        if (!data)
            data = !_.isArray(obj) ? _.omit(obj, ['meta']) : obj;

        // Handle any metadata.

        if (meta) {

            // If the data is an array, we first try zipping any matching metadata into the array values.

            if (_.isArray(data)) {

                const unmatched = zipMetaData(data, meta);

                // There might be unmatched metadata left. This can be the case when the data contains less values than the metadata, or when other non-matching metadata is available.

                if (metaContainer) {

                    if (metaPath) {

                        _.set(metaContainer, metaPath, _.extend(_.get(metaContainer, metaPath), unmatched));
                    }
                    else
                        _.extend(metaContainer, unmatched);
                }
            }
            else {

                // If the data is an object instead, we add any metadata to it's meta field.
                // This may include previously zipped metadata and special object metadata, which the API includes in the 'object' field.

                data.meta = _.extend({}, meta, meta.object);
            }
        }

        // Do the above process recursively for all data keys.

        if (_.isArray(data)) {

            _.forEach(data, (value, key) => {

                data[key] = removeDataWrappers(data[key]);
            });
        }
        else if (_.isObject(data)) {

            _.forEach(data, (value, key) => {

                data[key] = removeDataWrappers(data[key], data, 'meta.' + key);
            });
        }
    }
    else
        data = obj;

    return data;
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

const zipMetaData = function(data, meta) {

    const unmatched = {};

    _.forEach(meta, (metaValue, metaKey) => {

        // Check if the specific metadata is zippable.

        if (_.isArray(metaValue)) {

            _.forEach(metaValue, (value) => {

                let matched = false;

                if (value.id) {

                    const match = _.find(data, {id: value.id});

                    if (match) {

                        match.meta = _.extend(match.meta, value);
                        matched = true;
                    }
                }

                if (!matched) {

                    unmatched[metaKey] = unmatched[metaKey] || [];
                    unmatched[metaKey].push(value);
                }
            });
        }
        else
            unmatched[metaKey] = metaValue;
    });

    return unmatched;
};

export {

    getDefaultParameters,
    getFormData,
    removeDataWrappers,
    removeEmptyObjects,
    zipMetaData
}