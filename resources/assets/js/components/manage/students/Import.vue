<template>

    <div>

        <loading-overlay v-show="loading"></loading-overlay>

        <div class="dashhead">
            <div class="dashhead-titles">
                <h2 class="dashhead-subtitle">Manage</h2>
                <h2 class="dashhead-title">Import students from .csv</h2>
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

        <div class="m-t">
            <p><strong>The .csv file should meet the following requirements:</strong></p>
            <ul>
                <li>It should ideally be UTF-8 encoded to prevent any data loss.</li>
                <li>The delimiter must be ; (default when exporting from Microsoft Excel).</li>
                <li>The first row must contain the column headers.</li>
            </ul>
            <p><strong>It should contain the following columns:</strong></p>
            <ol>
                <li>nummer (required)</li>
                <li>opl (required)</li>
                <li>omschrijving</li>
                <li>cohort (required)</li>
                <li>Jaar (required)</li>

                <li>1stY-Mentor</li>
                <li>1stY-B1</li>
                <li>1stY-B2</li>
                <li>1stY-B3</li>
                <li>1stY-B4</li>
                <li>1stY-B5</li>
                <li>1stY-B6</li>
                <li>1stY-crd</li>
                <li>My1stGoal</li>

                <li>WA1-crd</li>
                <li>WA1</li>
                <li>WA2-crd</li>
                <li>WA2</li>
                <li>WA3-crd</li>
                <li>WA3</li>

                <li>BSA-crd</li>
                <li>bsa</li>

                <li>2ndY</li>
                <li>2ndY-B1</li>
                <li>2ndY-B2</li>
                <li>2ndY-B3</li>
                <li>2ndY-B4</li>
                <li>2ndY-B5</li>
                <li>2ndY-B6</li>
                <li>Nvakken-B1</li>
                <li>2ndY-crd</li>
                <li>2ndY-crd Prognose</li>
                <li>My2ndGoal</li>

                <li>DipCategory</li>
                <li>RunningTotal (required)</li>
                <li>GPA actueel</li>
                <li>prognose afstudeer datum obv tempo (format: dd-mm-yyyy)</li>

                <li>VOORNAAM (required)</li>
                <li>ACHTERNAAM (required)</li>
                <li>TUSSENVOEGSEL</li>
                <li>INITIALEN</li>
                <li>GEBOORTEDATUM (required, format: dd-mm-yyyy)</li>
                <li>GEBOORTEPLAATS</li>
                <li>GEBOORTELAND</li>
                <li>GESLACHT (required, format: M/F)</li>
                <li>NATIONALITEIT</li>
                <li>EMAILADRES</li>
                <li>vooropleidng</li>
            </ol>
            <p>
                <strong>Recommendation:</strong> verify the .csv file before uploading by opening it in a plain text editor such as Notepad or TextMate.
                                                 Pay special attention to the delimiting character (;) and date formats (dd-mm-yyyy).
            </p>
        </div>

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
                    .then((result) => {
                        this.displaySuccess(true, 'The data was imported successfully.');
                        this.backgroundProcesses--;
                    })
                    .catch((ex) => {
                        this.displayErrors(true, ex.message, ex.errors);
                        this.backgroundProcesses--;
                    });
            }
        },
        mixins: [

            backgroundProcesses,
            errorAlerts,
            loadingOverlay
        ]
    }

</script>