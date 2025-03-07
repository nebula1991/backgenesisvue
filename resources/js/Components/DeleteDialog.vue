<template>
    <v-dialog 
        :model-value="show" 
        @update:model-value="$emit('update:show', $event)" 
        persistent max-width="400">
        <v-card>
            <v-card-title class="text-h5 bg-error text-white">
                <v-icon icon="mdi-alert" class="mr-2"></v-icon>
                {{ title }}
            </v-card-title>
            
            <v-card-text class="pt-4">
                {{ message }}
            </v-card-text>
            
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                    color="grey-darken-1"
                    variant="text"
                    @click="onCancel"
                >
                    Cancelar
                </v-btn>
                <v-btn
                    color="error"
                    variant="elevated"
                    @click="onConfirm"
                    :loading="loading"
                >
                    Eliminar
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    props: {
        show: Boolean,
        title: {
            type: String,
            default: 'Eliminar'
        },
        message: {
            type: String,
            default: '¿Estás seguro de que quieres eliminar este elemento? Esta acción no se puede deshacer.'
        },
        loading: {
            type: Boolean,
            default: false
        }
    },
    methods: {
        onCancel() {
            this.$emit('update:show', false);
            this.$emit('cancel');
        },
        onConfirm() {
            this.$emit('confirm');
        }
    }
}
</script>