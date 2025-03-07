<template>
    <div class="text-center mb-6"> 
        <h1 class="text-h4 text-grey-darken-3 text-uppercase" v-if="isEditing">Actualizar Categoría
            <div class="text-h5 mt-2 text-grey-darken-1">{{ form.name }}</div>
        </h1>
        <h1 class="text-h4 text-grey-darken-3 text-uppercase " v-else>Crear Categoría</h1>    
    </div>

    <v-btn prepend-icon="mdi-arrow-left" variant="outlined" class="my-4" @click="$router.push({ name: 'categories' })">
        Volver
    </v-btn>
  
    <div class="d-flex justify-center">

        <div class="pa-4" style="width: 100%; max-width: 600px;">

            <v-form  style="max-width: 600px; width:100%">
                <v-row>
                    <v-col cols="12">
                        <v-text-field v-model="form.name" label="Nombre" :error-messages="errors.name"
                            variant="outlined"></v-text-field>

                    </v-col>
                    <v-col cols="12">
                        <v-textarea v-model="form.description" label="Descripción" :error-messages="errors.description"
                            variant="outlined" rows="4"></v-textarea>

                    </v-col>

                    <v-col cols="12" class="text-center">
                        <v-btn color="primary" :loading="isSubmitting" @click="send" size="large" min-width="150">
                            {{ isEditing ? 'Actualizar' : 'Crear' }}
                        </v-btn>
                    </v-col>

                </v-row>


            </v-form>



  
        </div>
    </div>

    <custom-snackbar
        v-model:show="snackbar.show"
        :text="snackbar.text"
        :color="snackbar.color"
        :timeout="3000"
    />
</template>


<script>
import CustomSnackbar from './CustomSnackbar.vue';

export default {
    components: {
        CustomSnackbar
    },
    async mounted() {
        console.log('Id desde la ruta: ', this.$route.params.id);
        try {
            if (this.$route.params.id) {
                await this.loadCategory();
            }
        } catch (error) {
            console.error('Error cargando producto:', error);
        }


    },
    data() {
        return {
            form: {
                name: '',
                description: '',
            },
            errors: {
                name: '',
                description: '',
            },
            isSubmitting: false,
            isEditing: false,
            snackbar: {
                show: false,
                text: '',
                color: 'success'
            },
        }
    },
    methods: {
        async loadCategory() {
            const id = this.$route.params.id;
            console.log('Cargando categoria con ID:', id);
            try {
                const res = await this.$axios.get(`/api/categories/${id}`);
                console.log('Datos de la categoria:', res.data);
                this.form = { ...res.data };
                this.isEditing = true;
            } catch (error) {
                console.error('Error cargando categoria:', error);
                if (error.response?.status === 404) {
                    this.$router.push('/vue/categories'); // Redirigir si no se encuentra
                }
            }
        },
        resetForm() {
            this.form = {
                name: '',
                description: '',
            };
            this.errors = {
                name: '',
                description: '',
            };
        },
        async send() {
            if (this.isSubmitting) return;

            this.isSubmitting = true;
            const method = this.isEditing ? 'put' : 'post';
            const url = this.isEditing ? `/api/categories/${this.$route.params.id}` : '/api/categories';

            try {
                const res = await this.$axios[method](url, this.form);
                console.log(this.isEditing ? 'Categoria actualizada con éxito' : 'Categoria creada con éxito:', res.data);
                this.snackbar = {
                 show: true,
                 text: this.isEditing ? 'Subcategoría actualizada con éxito' 
                                        : 'Subcategoría creada con éxito',
                 color: 'success'
             };
             // Only navigate after delay
             setTimeout(() => {
                 this.resetForm();
                 this.$router.push('/vue/categories');
             }, 2000);
           
            } catch (error) {
                if (error.response) {
                    if (error.response.status === 422 && error.response.data?.errors) {
                        const serverErrors = error.response.data.errors;
                        Object.keys(this.errors).forEach(key => {
                            this.errors[key] = serverErrors[key] ? serverErrors[key][0] : '';
                        });
                    } else if (error.response.status === 500) {
                        console.error('Error 500 del servidor:', error.response.data);
                        alert('Error interno del servidor. Por favor, intenta de nuevo más tarde.');
                    } else {
                        console.error('Error inesperado:', error.response.data);
                        alert('Ocurrió un error al procesar el producto.');
                    }
                } else {
                    console.error('Error de conexión:', error);
                    alert('No se pudo conectar al servidor.');
                }
            }
            finally {
                this.isSubmitting = false;
            }

        }
    }
}
</script>