<template>

    <div>

        <loading-overlay v-show="loading"></loading-overlay>

        <div class="dashhead">
            <div class="dashhead-titles">
                <h2 class="dashhead-subtitle">Manage</h2>
                <h2 class="dashhead-title" v-if="isNew">New update</h2>
                <h2 class="dashhead-title" v-else>Edit {{ news.title }}</h2>
            </div>
        </div>

        <hr class="m-t">

        <alert type="success" v-bind:message="successMessage" v-bind:show="successMessage" v-on:close="successMessage = ''"></alert>
        <alert type="danger" v-bind:message="errorMessage" v-bind:show="errorMessage" v-on:close="errorMessage = ''"></alert>

        <form v-on:submit.prevent="submit" class="form-horizontal">

            <div v-bind:class="['form-group', hasError('title') ? 'has-feedback has-error' : '']">
                <label class="col-sm-3 control-label">Title</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" v-model="news.title">
                </div>
            </div>

            <div v-bind:class="['form-group', hasError('text') ? 'has-feedback has-error' : '']">
                <label class="col-sm-3 control-label">Text</label>
                <div class="col-sm-9">
                    <div class="form-control editable" v-html="news.text"></div>
                </div>
            </div>

            <div v-bind:class="['form-group', hasError('is_published') ? 'has-feedback has-error' : '']">
                <label class="col-sm-3 control-label"></label>
                <div class="col-sm-9">
                    <div class="checkbox">
                        <label><input type="checkbox" v-model="news.is_published">Published</label>
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

    import * as _ from 'lodash';
    import MediumEditor from 'medium-editor';
    import backgroundProcesses from '../../../mixins/background_processes';
    import errorAlerts from '../../../mixins/error_alerts';
    import loadingOverlay from '../../../mixins/loading_overlay';
    import * as news from '../../../services/news';

    export default {

        beforeDestroy() {

            if (this.editor) this.editor.destroy();
        },
        computed: {

            isNew: function() {return !this.news.id;}
        },
        created: function() {

            if (this.$route.params.id)
                this.fetchData(this.$route.params.id);
        },
        data: function() {

            return {

                news: {

                    is_published: true,
                },
            };
        },
        methods: {

            fetchData: function(id) {

                this.showLoading(true);
                this.displayErrors(false);

                news.getById(id)
                    .then(result => {

                        this.news = _.defaultsDeep(result.item, {

                            is_published: true,
                        });

                        this.showLoading(false);
                    })
                    .catch(customError => {

                        this.displayErrors(true, customError.message);
                        this.showLoading(false);
                    });
            },
            handleRemove: function() {

                news.deleteByIds(this.news.id)
                    .then(() => {this.$router.push({path: '/manage/news'});})
                    .catch(customError => {this.displayErrors(true, customError.message);});
            },
            handleSubmit: function() {

                this.news.text = this.editor.getContent();
                this.saveOrUpdate(this.news);
            },
            saveOrUpdate: function(payload) {

                this.displayErrors(false);
                this.displaySuccess(false);

                const wasNew = payload.id;

                news.saveOrUpdate(payload)
                    .then(result => {

                        this.news = _.defaultsDeep(result.item, {

                            is_published: true
                        });

                        if (wasNew)
                            this.$router.push({path: '/manage/news/' + result.item.id + '/edit'});

                        this.displaySuccess(true);
                    })
                    .catch(customError => {

                        this.displayErrors(true, customError.message, customError.errors);
                    });
            }
        },
        mixins: [

            backgroundProcesses,
            errorAlerts,
            loadingOverlay,
        ],
        mounted() {

            this.editor = new MediumEditor('.editable', {

                linkValidation: true,
                placeholder: false,
                targetBlank: true,
            });
        },
    };

</script>

<style>

    .editable {
        height: auto !important;
        min-height: 20rem;
    }

</style>
