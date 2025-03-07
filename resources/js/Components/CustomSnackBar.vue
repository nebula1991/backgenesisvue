<template>
    <v-snackbar
        v-model="localShow"
        :color="color"
        :timeout="timeout"
    >
        {{ text }}
        <template v-slot:actions>
            <v-btn
                color="white"
                variant="text"
                @click="closeSnackbar"
            >
                Cerrar
            </v-btn>
        </template>
    </v-snackbar>
</template>

<script>
export default {
    props: {
        show: {
            type: Boolean,
            required: true
        },
        text: {
            type: String,
            default: ''
        },
        color: {
            type: String,
            default: 'success'
        },
        timeout: {
            type: Number,
            default: 3000
        }
    },
    computed: {
        localShow: {
            get() {
                return this.show;
            },
            set(value) {
                this.$emit('update:show', value);
            }
        }
    },
    methods: {
        closeSnackbar() {
            this.$emit('update:show', false);
        }
    }
}
</script>