<template>
<div class="container mx-auto">


    <div class="text-center mb-6"> 
        <h1 class="text-h4 text-grey-darken-3 text-uppercase" v-if="isEditing">Actualizar Proveedor
            <div class="text-h5 mt-2 text-grey-darken-1">{{ form.name }}</div>
        </h1>
        <h1 class="text-h4 text-grey-darken-3 text-uppercase " v-else>Crear Proveedor</h1>    
    </div>


    <v-btn prepend-icon="mdi-arrow-left" variant="outlined" class="my-4" @click="$router.push({ name: 'suppliers' })">
        Volver
    </v-btn>

    <div class="d-flex justify-center align-center" style="min-height: 100vh;">
        <div class="pa-4" style="width: 100%; max-width: 600px;">
            <v-form class="mt-4">
                <v-row>
                    <v-col cols="12">
                        <v-text-field
                            v-model="form.name"
                            label="Nombre"
                            :error-messages="errors.name"
                            variant="outlined"
                        ></v-text-field>
                    </v-col>

                    <v-col cols="12">
                        <v-text-field
                            v-model="form.email"
                            label="Email"
                            type="email"
                            :error-messages="errors.email"
                            variant="outlined"
                        ></v-text-field>
                    </v-col>

                    <v-col cols="12" md="6">
                        <v-text-field
                            v-model="form.address"
                            label="Dirección"
                            type="text"
                            :error-messages="errors.address"
                            variant="outlined"
                        ></v-text-field>
                    </v-col>

                    <v-col cols="12" md="6">
                        <v-text-field
                            v-model="form.phone"
                            label="Telefono"
                            type="phone"
                            :error-messages="errors.phone"
                            variant="outlined"
                        ></v-text-field>
                    </v-col>


                    <v-col cols="12" class="text-center">
                        <v-btn
                            color="primary"
                            :loading="isSubmitting"
                            @click="send"
                            size="large"
                            min-width="150"
                        >
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
</div>
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
                await this.loadSupplier();
            }
        } catch (error) {
            console.error('Error cargando proveedor:', error);
        }
    },
    data() {
        return {
            form: {
                name: '',
                email: '',
                address: '',
                phone: '',
            },
            errors: {
                name: '',
                email: '',
                address: '',
                phone: '',

            },
            fileError:'',
            file: null,
            filesDaD: [],
            isSubmitting: false,
            isEditing: false,
            snackbar: {
                show: false,
                text: '',
                color: 'success'
            },
     
        };
    },
    methods: {
        async loadSupplier() {
            const id = this.$route.params.id;
            console.log('Cargando proveedor con ID:', id);
            try {
                const res = await this.$axios.get(`/api/suppliers/${id}`);
                console.log('Datos del proveedor:', res.data);
                this.form = { ...res.data };
                this.isEditing = true;
            } catch (error) {
                console.error('Error cargando proveedor:', error);
                if (error.response?.status === 404) {
                    this.$router.push('/suppliers'); // Redirigir si no se encuentra
                }
            }
        },

            resetForm() {
                this.form = {
                    name: '',
                    email: '',
                    address: '',
                    phone: '',
                };
                this.errors = {
                    name: '',
                    email: '',
                    address: '',
                    phone: '',
                };
           
        },
        async send() {
            if (this.isSubmitting) return;

            this.isSubmitting = true;

            try {
                const method = this.isEditing ? 'put' : 'post';
                const url = this.isEditing ? `/api/suppliers/${this.$route.params.id}` : '/api/suppliers';
                const res = await this.$axios[method](url, this.form);
                console.log(this.isEditing ? 'Proveedor actualizado:' : 'Proveedor creado:', res.data);
                this.snackbar = {
                 show: true,
                 text: this.isEditing ? 'Proveedor actualizado con éxito' 
                                        : 'Proveedor creado con éxito',
                 color: 'success'
             };

             // Only navigate after delay
             setTimeout(() => {
                 this.resetForm();
                 this.$router.push('/suppliers');
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
        },
    
        }
    } 
</script>