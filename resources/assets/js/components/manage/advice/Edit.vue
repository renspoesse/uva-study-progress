<template>

    <div>

        <loading-overlay v-show="loading"></loading-overlay>

        <div class="dashhead">
            <div class="dashhead-titles">
                <h2 class="dashhead-subtitle">Manage advice</h2>
                <h2 class="dashhead-title" v-if="isNew">New tip or advice</h2>
                <h2 class="dashhead-title" v-else>Edit {{ advice.title }}</h2>
            </div>
        </div>

        <hr class="m-t">

        <alert type="success" v-bind:message="successMessage" v-bind:show="successMessage" v-on:close="successMessage = ''"></alert>
        <alert type="danger" v-bind:message="errorMessage" v-bind:show="errorMessage" v-on:close="errorMessage = ''"></alert>

        <form v-on:submit.prevent="submit" class="form-horizontal">

            <div class="form-group">
                <div class="col-sm-3"></div>
                <div class="col-sm-9" v-show="hasBackgroundProcesses">
                    <i class="fa fa-spinner fa-spin m-r"></i>Wait one second please while background processes are finishing.
                </div>
                <div class="col-sm-9" v-show="!hasBackgroundProcesses">
                    <div class="btn-group">
                        <a v-on:click.prevent="handleSubmit" class="btn btn-primary-outline" v-if="isNew">Create</a>
                        <a v-on:click.prevent="handleSubmit" class="btn btn-primary-outline" v-if="!isNew">Save</a>
                    </div>
                    <a v-on:click.prevent="handleRemove" class="btn btn-primary-outline pull-right" v-if="!isNew"><i class="fa fa-trash"></i></a>
                </div>
            </div>

            <div v-bind:class="['form-group', hasError('title') ? 'has-feedback has-error' : '']">
                <label class="col-sm-3 control-label">Title</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" v-model="advice.title">
                </div>
            </div>

            <div v-bind:class="['form-group', hasError('text') ? 'has-feedback has-error' : '']">
                <label class="col-sm-3 control-label">Text</label>
                <div class="col-sm-9">
                     <div class="form-control editable" v-html="news.text" v-on:input="handleTextInput"></div>
                </div>
            </div>

            <div v-bind:class="['form-group', hasError('is_published') ? 'has-feedback has-error' : '']">
                <label class="col-sm-3 control-label"></label>
                <div class="col-sm-9">
                    <div class="checkbox">
                        <label><input type="checkbox" v-model="advice.is_published">Published</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-3"></div>
                <div class="col-sm-9" v-show="hasBackgroundProcesses">
                    <i class="fa fa-spinner fa-spin m-r"></i>Wait one second please while background processes are finishing.
                </div>
                <div class="col-sm-9" v-show="!hasBackgroundProcesses">
                    <div class="btn-group">
                        <a v-on:click.prevent="handleSubmit" class="btn btn-primary-outline" v-if="isNew">Create</a>
                        <a v-on:click.prevent="handleSubmit" class="btn btn-primary-outline" v-if="!isNew">Save</a>
                    </div>
                    <a v-on:click.prevent="handleRemove" class="btn btn-primary-outline pull-right" v-if="!isNew"><i class="fa fa-trash"></i></a>
                </div>
            </div>

        </form>

    </div>

</template>

<script>

    import * as _ from 'lodash'
    import MediumEditor from 'medium-editor'

    import backgroundProcesses from '../../../mixins/background_processes'
    import errorAlerts from '../../../mixins/error_alerts'
    import loadingOverlay from '../../../mixins/loading_overlay'

    import * as advice from '../../../services/advice'
    import * as images from '../../../helpers/images'

    export default {

        beforeDestroy() {

            if (this.editor) this.editor.destroy();
        },
        computed: {

            isNew: function() {return !this.advice.id;}
        },
        created: function() {

            if (this.$route.params.id)
                this.fetchData(this.$route.params.id);
        },
        data: function() {

            return {

                advice: {

                    is_published: true
                }
            }
        },
        methods: {

            fetchData: function(id) {

                this.showLoading(true);
                this.displayErrors(false);

                advice.getById(id)
                    .then((result) => {

                        this.advice = _.defaultsDeep(result.item, {

                            is_published: true
                        });

                        this.showLoading(false);
                    })
                    .catch((ex) => {

                        this.displayErrors(true, ex.message);
                        this.showLoading(false);
                    });
            },
            handleRemove: function() {

                advice.deleteById(this.advice.id)
                    .then(() => {this.$router.push({path: '/manage/advice'});})
                    .catch((ex) => {this.displayErrors(true, ex.message);});
            },
            handleSubmit: function() {

                this.saveOrUpdate(this.advice);
            },
            handleTextInput: function() {

                if (this.editor)
                    this.news.text = this.editor.getContent();
            },
            saveOrUpdate: function(payload) {

                this.displayErrors(false);
                this.displaySuccess(false);

                const wasNew = payload.id;

                const promise = advice.saveOrUpdate(payload);

                promise.then((result) => {

                        this.advice = _.defaultsDeep(result.item, {

                            is_published: true
                        });

                        if (wasNew)
                            this.$router.push({path: '/manage/advice/' + result.item.id + '/edit'});

                        this.displaySuccess(true);
                    })
                    .catch((ex) => {this.displayErrors(true, ex.message, ex.errors);});

                return promise;
            }
        },
        mixins: [

            backgroundProcesses,
            errorAlerts,
            loadingOverlay
        ],
        mounted() {

            this.editor = new MediumEditor('.editable', {

                linkValidation: true,
                placeholder: false,
                targetBlank: true
            });
        }
    }

</script>

<style>

    .editable {
        height: auto !important;
        min-height: 20rem;
    }

</style>