<template>

    <div>

        <loading-overlay v-show="loading"></loading-overlay>

        <div class="dashhead">
            <div class="dashhead-titles">
                <h2 class="dashhead-subtitle">Manage</h2>
                <h2 class="dashhead-title">Settings</h2>
            </div>
        </div>

        <hr class="m-t">

        <alert type="success" v-bind:message="successMessage" v-bind:show="successMessage" v-on:close="successMessage = ''"></alert>
        <alert type="danger" v-bind:message="errorMessage" v-bind:show="errorMessage" v-on:close="errorMessage = ''"></alert>

        <form v-on:submit.prevent="submit" class="form-horizontal">

            <div v-bind:class="['form-group', hasError('active_block') ? 'has-feedback has-error' : '']">
                <label class="col-sm-3 control-label">Active block</label>
                <div class="col-sm-9">
                    <select class="form-control" v-model="settings.active_block">
                        <option v-bind:value="1">Block 1</option>
                        <option v-bind:value="2">Block 2</option>
                        <option v-bind:value="3">Block 3</option>
                        <option v-bind:value="4">Block 4</option>
                        <option v-bind:value="5">Block 5</option>
                        <option v-bind:value="6">Block 6</option>
                    </select>
                    <span class="help-block" v-show="getValidationErrors('active_block')">{{ getValidationErrors('active_block') }}</span>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-3"></div>
                <div class="col-sm-9" v-show="hasBackgroundProcesses">
                    <i class="fa fa-spinner fa-spin m-r"></i>Wait one second please while background processes are finishing.
                </div>
                <div class="col-sm-9" v-show="!hasBackgroundProcesses">
                    <div class="btn-group">
                        <a v-on:click.prevent="handleSubmit" class="btn btn-primary-outline">Save</a>
                    </div>
                </div>
            </div>

        </form>

    </div>

</template>

<script>

    import backgroundProcesses from '../../../mixins/background_processes'
    import errorAlerts from '../../../mixins/error_alerts'
    import loadingOverlay from '../../../mixins/loading_overlay'

    import * as settings from '../../../services/settings'

    export default {

        created() {

            this.fetchSettings();
        },
        data: function() {

            return {

                settings: {}
            };
        },
        methods: {

            fetchSettings: function() {

                this.displayErrors(false);

                settings.get().then((result) => {

                    this.settings = result.item;
                });
            },
            handleSubmit: function() {

                this.update(this.settings);
            },
            update: function(payload) {

                this.displayErrors(false);
                this.displaySuccess(false);

                return settings.update(payload).then((result) => {

                        this.settings = _.defaultsDeep(result.item, {});
                        this.displaySuccess(true);
                    })
                    .catch((ex) => {this.displayErrorsSpecific(true, ex.errors);});
            }
        },
        mixins: [

            backgroundProcesses,
            errorAlerts,
            loadingOverlay
        ]
    }

</script>