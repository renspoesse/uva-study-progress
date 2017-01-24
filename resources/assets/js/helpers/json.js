const removeDataWrappers = function (obj) {

    if (_.isObject(obj) || _.isArray(obj)) {

        const length = _.isArray(obj) ? obj.length : (_.keys(obj)).length;

        if (obj['data']) {

            if ((length === 1 || obj['meta'] && length === 2)) {

                obj = obj['data'];
            }
        }

        _.each(obj, (value, key) => {

            obj[key] = removeDataWrappers(obj[key]);
        });
    }

    return obj;
};

export {

    removeDataWrappers
}