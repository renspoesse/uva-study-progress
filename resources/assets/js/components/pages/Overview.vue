<template>

    <div v-if="!hasAnyRole(user, roles.Student) && !viewAs">

        <alert type="info" message="This page is only available to students. Try viewing it as a student instead." show="true" v-bind:closeable="false"></alert>
        <router-link class="btn btn-primary-outline" to="/manage/students" v-if="hasAnyRole(user, [roles.StudyAdviser, roles.Administrator])">Go to Students</router-link>

    </div>
    <div v-else>

        <template v-if="student.id">

            <overview-1sty v-bind:student="student" v-if="student.year === 1"/>
            <overview-2ndy v-bind:student="student" v-else-if="student.year === 2"/>

        </template>
        <template v-else>

            <alert type="info" message="No student information is available for you (at least not at the moment)." show="true" v-bind:closeable="false"></alert>

        </template>

    </div>

</template>

<script>

    import {mapState} from 'vuex'

    import errorAlerts from '../../mixins/error_alerts'

    import Roles from '../../enums/Roles'

    import * as roles from '../../helpers/roles'
    import * as students from '../../services/students'

    export default {

        computed: {

            ...mapState({

                user: (state) => state.auth.user,
                viewAs: (state) => state.auth.viewAs
            }),
            roles() { return Roles; }
        },
        created() {

            this.fetchStudent();
        },
        data: function() {

            return {

                student: {}
            };
        },
        methods: {

            fetchStudent: function() {

                this.displayErrors(false);

                if (this.viewAs) {

                    students.getById(this.viewAs.id)
                        .then((result) => {

                            this.student = result.item;
                        })
                        .catch((ex) => {

                            this.displayErrors(true, ex.message);
                        });
                }
                else {

                    students.getByAuthenticated()
                        .then((result) => {

                            this.student = result.item;
                        })
                        .catch((ex) => {

                            // When changing from view-as-student to view-as-yourself, there might be no student data available anymore.
                            // We should reflect this in the view instead of keeping the old data.

                            this.student = {};
                            this.displayErrors(true, ex.message);
                        });
                }
            },
            hasAnyRole: roles.hasAnyRole
        },
        mixins: [

            errorAlerts
        ],
        watch: {

            viewAs: function(newValue) {

                this.fetchStudent();
            }
        }
    }

</script>