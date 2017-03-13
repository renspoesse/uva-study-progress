<template>

    <div>

        <loading-overlay v-show="loading"></loading-overlay>

        <div class="dashhead">
            <div class="dashhead-titles">
                <h2 class="dashhead-subtitle">Manage students</h2>
                <h2 class="dashhead-title">Import from .csv</h2>
            </div>
        </div>

        <hr class="m-t">

        <alert type="success" v-bind:message="successMessage" v-bind:show="successMessage" v-on:close="successMessage = ''"></alert>
        <alert type="danger" v-bind:message="errorMessage" v-bind:show="errorMessage" v-on:close="errorMessage = ''"></alert>

        <form v-on:submit.prevent="submit" class="form-horizontal">

            <div class="form-group">
                <div class="col-md-3 control-label">Select a file</div>
                <div class="col-md-9">
                    <input type="file" accept="text/csv" ref="fileInput">
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-3"></div>
                <div class="col-sm-9" v-show="hasBackgroundProcesses">
                    <i class="fa fa-spinner fa-spin m-r"></i>Wait one second please while background processes are finishing.
                </div>
                <div class="col-sm-9" v-show="!hasBackgroundProcesses">
                    <div class="btn-group">
                        <a v-on:click.prevent="handleSubmit" class="btn btn-primary-outline">Import from file</a>
                    </div>
                </div>
            </div>

        </form>

    </div>

</template>

<script>

    import * as _ from 'lodash'
    import backgroundProcesses from '../../../mixins/background_processes'
    import errorAlerts from '../../../mixins/error_alerts'
    import loadingOverlay from '../../../mixins/loading_overlay'

    import * as students from '../../../services/students'

    export default {

        computed: {

            _() { return _; }
        },
        methods: {

            handleSubmit: function() {

                this.displayErrors(false);
                this.displaySuccess(false);

                this.backgroundProcesses++;

                students.importFromFile({}, this.$refs.fileInput.files[0])
                    .then((result) => {this.displaySuccess(true, 'The data was imported successfully.'); this.backgroundProcesses--;})
                    .catch((ex) => {this.displayErrors(true, ex.message, ex.errors); this.backgroundProcesses--;});
            }
        },
        mixins: [

            backgroundProcesses,
            errorAlerts,
            loadingOverlay
        ]
    }

</script>