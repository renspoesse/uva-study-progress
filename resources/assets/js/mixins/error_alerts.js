import * as _ from 'lodash'

export default {

    data: function() {

        return {

            errorMessage: '',
            errors: [],
            successMessage: ''
        }
    },
    methods: {

        addError: function(message) {

            this.errorMessage = message; // TODO RENS: allow multiple errors.
        },
        displayErrors: function(show, message, errors) {

            if (show && _.isEmpty(message)) message = 'Error';

            this.errorMessage = show ? message : '';
            this.errors = show && errors ? errors : [];
        },
        displayErrorsSpecific: function(show, errors) {

            this.errorMessage = show ? _.values(errors).join('\n') : '';
            this.errors = show ? errors : [];
        },
        displaySuccess: function(show, message) {

            if (show && _.isEmpty(message)) message = 'You have successfully saved your changes!';

            this.successMessage = show ? message : '';
        },
        getAttributeFromTranslations: function(name) {

            return _.replace(_.defaultTo(trans('validation.attributes.' + name), name), /_/g, ' ');
        },
        getValidationErrors: function(field) {

            let result = [];

            if (_.isArray(field)) {

                _.each(field, (value) => {

                    result = result.concat(_.get(this.errors, value, []));
                })
            }
            else
                result = result.concat(_.get(this.errors, field, []));

            return result.join(' ');
        },
        hasArrayItemWithError: function(arrayName, index, field) {

            return _.find(this.errors, function(value, key) {

                return (key === (arrayName + '.' + index + '.' + field));
            });
        },
        hasError: function(field) {

            return _.find(this.errors, function(value, key) {

                return (key === field || key.startsWith(field + '.'));
            });
        }
    }
}