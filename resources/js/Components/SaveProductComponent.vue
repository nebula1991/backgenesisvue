<template>
<div class="container mx-auto">


    <div class="text-center mb-6"> 
        <h1 class="text-h4 text-grey-darken-3 text-uppercase" v-if="isEditing">Actualizar Producto
            <div class="text-h5 mt-2 text-grey-darken-1">{{ form.name }}</div>
        </h1>
        <h1 class="text-h4 text-grey-darken-3 text-uppercase " v-else>Crear Producto</h1>    
    </div>


    <v-btn prepend-icon="mdi-arrow-left" variant="outlined" class="my-4" @click="$router.push({ name: 'products' })">
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
                        <v-textarea
                            v-model="form.description"
                            label="Descripción"
                            :error-messages="errors.description"
                            variant="outlined"
                            rows="4"
                        ></v-textarea>
                    </v-col>

                    <v-col cols="12" md="6">
                        <v-text-field
                            v-model="form.price"
                            label="Precio"
                            type="number"
                            :error-messages="errors.price"
                            variant="outlined"
                
                            @input="formatPrice"
                             placeholder="0.00"
                        ></v-text-field>
                    </v-col>

                    <v-col cols="12" md="6">
                        <v-text-field
                            v-model="form.stock"
                            label="Stock"
                            type="number"
                            :error-messages="errors.stock"
                            variant="outlined"
                            min="0"
                        ></v-text-field>
                    </v-col>

                    <v-col cols="12" md="6">
                        <v-select
                            v-model="form.category_id"
                            :items="categories"
                            item-title="name"
                            item-value="id"
                            label="Categoría"
                            :error-messages="errors.category_id"
                            variant="outlined"
                            @update:model-value="onCategoryChange"
                        ></v-select>
                    </v-col>

                    <v-col cols="12" md="6">
                        <v-select
                            v-model="form.subcategory_id"
                            :items="filteredSubcategories"
                            item-title="name"
                            item-value="id"
                            label="Subcategoría"
                            :error-messages="errors.subcategory_id"
                            variant="outlined"
                            :disabled="!form.category_id || filteredSubcategories.length === 0"
                            @update:modelValue="onSubcategoryChange"
                        ></v-select>
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

            <v-card v-if="isEditing" class="mt-4">
                <v-card-title>Imágenes del Producto</v-card-title>
                <v-card-text>
                    <v-file-input
                        v-model="file"
                        :error-messages="fileError"
                        accept="image/*"
                        label="Seleccionar imagen"
                        prepend-icon="mdi-camera"
                    ></v-file-input>
                    <v-btn
                        color="primary"
                        @click="upload"
                        :disabled="!file"
                        prepend-icon="mdi-cloud-upload"
                    >
                        Subir Imagen
                    </v-btn>
                </v-card-text>
            </v-card>
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
            console.log('Cargando categorías y subcategorías...');
            await Promise.all([this.getcategory(), this.getsubcategory()]);
            console.log('Categorías y subcategorías cargadas con éxito');

            if (this.$route.params.id) {
                console.log('Llamando a loadProduct con ID:', this.$route.params.id);
                await this.loadProduct();
            }
            else {
                console.log('No hay ID en la ruta, no se llama a loadProduct');
        }
        } catch (error) {
            console.error('Error en mounted:', error);
        }
    },
    data() {
        return {
            form: {
                name: '',
                description: '',
                price: '',
                stock: '',
                category_id: '',
                subcategory_id: '',
            },
            errors: {
                name: '',
                description: '',
                price: '',
                stock: '',
                category_id: '',
                subcategory_id: '',
            },
            fileError:'',
            file: null,
            filesDaD: [],
            categories: [],
            subcategories: [],
            isSubmitting: false,
            isEditing: false,
            filteredSubcategories: [],
            snackbar: {
                show: false,
                text: '',
                color: 'success'
            },
     
        };
    },
    methods: {
        onSubcategoryChange(value) {
            console.log('Subcategory changed to:', value);
        },
        formatPrice(event) {    
            let value = event.target.value;

            // Reemplazar comas por puntos para estandarizar decimales
            value = value.replace(',', '.');

            // Permitir solo números y un único punto decimal
            value = value.replace(/[^0-9.]/g, '');

            // Evitar múltiples puntos decimales
            const parts = value.split('.');
            if (parts.length > 2) {
                value = parts[0] + '.' + parts.slice(1).join('');
            }

            // Si el usuario solo ha escrito un punto (".") al inicio, evitar que lo borre
            if (value === '.') {
                value = '0.';
            }

            // Si termina con un punto, no lo eliminamos (para que el usuario pueda seguir escribiendo)
            if (value.match(/\.$/)) {
                this.form.price = value;
                return;
            }

            // Convertir a número flotante y formatear a 2 decimales cuando el usuario deja de escribir
            const number = parseFloat(value);
            if (!isNaN(number)) {
                this.form.price = number.toString(); // Mantenemos el formato sin redondear aún
            } else {
                this.form.price = ''; // Si no es un número válido, limpiar el campo
            }
        },
    

   
        
        async loadProduct() {
            const id = this.$route.params.id;
            console.log('Cargando producto con ID:', id);

            this.isLoading = true;  // Start loading indicator

            try {
                const res = await this.$axios.get(`/api/products/${id}`);
                console.log('Datos del producto:', res.data);

                  // Activar modo edición
                  this.isEditing = true;

              // Asignar los valores iniciales
                this.form = {
                name: res.data.name || '',
                description: res.data.description || '',
                price: res.data.price ? Number(res.data.price).toFixed(2) : '',
                stock: res.data.stock?.toString() || '',
                category_id: Number(res.data.category_id) || '', // Asegurar número
                subcategory_id: Number(res.data.subcategory_id) || '' // Asegurar número
                };


             
            // Filtrar subcategorías si hay category_id
                if (this.form.category_id) {
                    await this.onCategoryChange(this.form.category_id); // Actualiza las subcategorías
                    await this.$nextTick(); // Esperar a que Vue actualice el DOM
                }


                //Depuración
                console.log('--- Depuración después de cargar ---');
                console.log('form.category_id:', this.form.category_id);
                console.log('form.subcategory_id:', this.form.subcategory_id);
                console.log('filteredSubcategories:', this.filteredSubcategories.map(sub => ({ id: sub.id, name: sub.name })));
                }
            
             catch (error) {
                console.error('Error cargando producto:', error);
                if (error.response?.status === 404) {
                    this.$router.push('/products'); // Redirigir si no se encuentra
                }
            
            
            }
        },
 
        async onCategoryChange(categoryId) {
          
          this.filteredSubcategories = []; //Limpia las subcategorias filtradas

    
            // Si se ha seleccionado una categoría y existen subcategorías
                if (categoryId && this.subcategories.length > 0) {
                  this.filteredSubcategories = this.subcategories.filter(
                  sub => sub.category_id === Number(categoryId)
                  );
                  
                  console.log('Filtradas para categoría:', categoryId, this.filteredSubcategories.map(sub => ({ id: sub.id, name: sub.name })));
              }
          },

        upload() {
            this.fileError = ''
            const formData = new FormData();
            console.log(this.file);
            formData.append('image', this.file);
            try {
                const res = this.$axios.post(`/api/products/upload/${this.$route.params.id}`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });
                console.log('Imagen subida:', res.data);

            }
            catch (error) {
                console.error('Error al subir imagen:', error);
                this.fileError = error.response.data.message
            }
        },

        async getcategory() {
            try {
                const res = await this.$axios.get('/api/categories/all');
                this.categories = Array.isArray(res.data) ? res.data : [];
            } catch (error) {
                console.error('Error fetching categories:', error);
                this.categories = [];
            }
        },

        async getsubcategory() {
            try {
                const res = await this.$axios.get('/api/subcategories/all');
                this.subcategories = Array.isArray(res.data) ? res.data : res.data.data || [];
                console.log('Subcategorías cargadas:', this.subcategories);
            } catch (error) {
                console.error('Error fetching subcategories:', error);
                this.subcategories = [];
            }
        },
            resetForm() {
                this.form = {
                    name: '',
                    description: '',
                    price: '',
                    stock: '',
                    category_id: '',
                    subcategory_id: '',
                };
                this.errors = {
                    name: '',
                    description: '',
                    price: '',
                    stock: '',
                    category_id: '',
                    subcategory_id: '',
                };
           
           
        },
        async send() {
            if (this.isSubmitting) return;

            this.isSubmitting = true;
             // Format price before sending
                if (this.form.price) {
                    this.form.price = Number(this.form.price).toFixed(2);
                }
            

            try {
                const method = this.isEditing ? 'put' : 'post';
                const url = this.isEditing ? `/api/products/${this.$route.params.id}` : '/api/products';
                const res = await this.$axios[method](url, this.form);
                console.log(this.isEditing ? 'Producto actualizado:' : 'Producto creado:', res.data);
                this.snackbar = {
                 show: true,
                 text: this.isEditing ? 'Producto actualizado con éxito' 
                                        : 'Producto creado con éxito',
                 color: 'success'
             };

             // Only navigate after delay
             setTimeout(() => {
                 this.resetForm();
                 this.$router.push('/products');
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
    
    watch: {
        filesDaD: {
            handler(val) {
                // return console.log(val[val.length-1]);
                const formData = new FormData();
                console.log(this.file);
                formData.append('image', val[val.length-1]);
                try {
                    const res = this.$axios.post(`/api/products/upload/${this.$route.params.id}`, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    });
                    console.log('Imagen subida:', res.data);

                }
                catch (error) {
                    console.error('Error al subir imagen:', error);

                }
            },
            deep: true
        },
        filteredSubcategories(newVal) {
    console.log('Watcher: filteredSubcategories actualizado', newVal.map(sub => ({ id: sub.id, name: sub.name })));
    if (newVal.length > 0 && this.form.subcategory_id && this.form.category_id) {
      this.$nextTick(() => {
        console.log('Forzando actualización de v-select con subcategory_id:', this.form.subcategory_id);
        this.$forceUpdate(); // Forzar re-renderizado
      });
    }
}
    }
        }
    } 
</script>