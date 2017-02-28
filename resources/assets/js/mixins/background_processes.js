export default {

    computed: {

        hasBackgroundProcesses: function() {

            return this.backgroundProcesses > 0;
        },
    },
    data: function() {

        return {

            backgroundProcesses: 0
        }
    }
}