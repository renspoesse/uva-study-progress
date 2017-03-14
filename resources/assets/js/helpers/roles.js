import * as _ from 'lodash'

const hasAnyRole = function(user, roles) {

    if (!_.has(user, 'roles')) return false;

    if (_.isArray(roles)) {

        for(let i = 0; i < roles.length; i++) {

            if (_.indexOf(user.roles, roles[i]) > -1)
                return true;
        }

        return false;
    }
    else
        return _.indexOf(user.roles, roles) > -1;
};

export {

    hasAnyRole
}