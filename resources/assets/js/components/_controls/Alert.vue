<template>

    <transition name="fade" appear>

        <p v-bind:class="['alert', alertClass]" v-show="show">
            <a class="close" aria-label="Close" v-if="closeable" v-on:click.prevent="handleClose"><span aria-hidden="true">&times;</span></a>
            {{ message }}
        </p>

    </transition>

</template>

<script>

    export default {

        computed: {

            alertClass: function() {

                return 'alert-' + this.type;
            },
        },
        methods: {

            handleClose: function() {

                this.$emit('close');
            },
        },
        props: {

            closeable: {default: true},
            message: {},
            show: {},
            type: {},
        },
    };

</script>
