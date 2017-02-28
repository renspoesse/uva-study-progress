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
        displaySuccess: function(show, message) {

            if (show && _.isEmpty(message)) message = 'You have successfully saved your changes!';

            this.successMessage = show ? message : '';
        },
        hasError: function(field) {

            return _.find(this.errors, function(value, key) {

                return (key === field || key.startsWith(field + '.'));
            });
        }
    }
}