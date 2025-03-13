<template>

        <div class="text-center mb-6">  
            <h1 class="text-h4 text-grey-darken-3 text-uppercase" v-if="isEditing">
                Actualizar Subcategoría
                <div class="text-h5 mt-2 text-grey-darken-1">{{ form.name }}</div>
            </h1>
            <h1 class="text-h4 text-grey-darken-3 text-uppercase" v-else>Crear Subcategoría</h1>
        </div>

    <v-btn prepend-icon="mdi-arrow-left" variant="outlined" class="my-4" @click="$router.push({ name: 'subcategories' })">
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

                    <v-col cols="12">
                        <v-select
                            v-model="form.category_id"
                            :items="categories"
                            item-title="name"
                            item-value="id"
                            label="Categoría"
                            :error-messages="errors.category_id"
                            variant="outlined"
                            placeholder="Selecciona una categoría"
                        ></v-select>
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

export default{
    components: {
        CustomSnackbar
    },
 async mounted(){
     console.log('Id desde la ruta: ', this.$route.params.id);
     try{
        await Promise.all([this.getcategory()]);
         if(this.$route.params.id){
         await this.loadSubcategory();
         }
     }catch(error){
         console.error('Error cargando subcategoria:', error);
     }

   
 },
 data(){
     return{
         form:{
             name:'',
             description:'',
             category_id:'',
         },
         errors:{
             name: '',
             description: '',
             category_id:'',
         },
         isSubmitting: false,
         isEditing: false,
         categories: [],
         snackbar: {
                show: false,
                text: '',
                color: 'success'
            },
     };
 },
 methods:{
     async loadSubcategory() {
         const id = this.$route.params.id;
         console.log('Cargando subcategoria con ID:', id);
         try {
             const res = await this.$axios.get(`/api/subcategories/${id}`);
             console.log('Datos de la subcategoria:', res.data);
             this.form = { ...res.data };
             this.isEditing = true;
         } catch (error) {
             console.error('Error cargando subcategoria:', error);
             if (error.response?.status === 404) {
                 this.$router.push('/subcategories'); // Redirigir si no se encuentra
             }
         }
     },
     resetForm(){
         this.form = {
             name: '',
             description: '',
         };
         this.errors = {
             name: '',
             description: '',
         };
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
        async send(){
         if(this.isSubmitting) return;
         this.isSubmitting = true;

         try {
             const method = this.isEditing ? 'put' : 'post';
             const url = this.isEditing ? `/api/subcategories/${this.$route.params.id}`: '/api/subcategories';
             const res = await this.$axios[method](url, this.form);
               
             this.snackbar = {
                 show: true,
                 text: this.isEditing ? 'Subcategoría actualizada con éxito' 
                                        : 'Subcategoría creada con éxito',
                 color: 'success'
             };

             // Only navigate after delay
             setTimeout(() => {
                 this.resetForm();
                 this.$router.push('/subcategories');
             }, 2000);

         } catch (error){
             if (error.response?.status === 422 && error.response.data?.errors) {
                 const serverErrors = error.response.data.errors;
                 Object.keys(this.errors).forEach(key => {
                     this.errors[key] = serverErrors[key] ? serverErrors[key][0] : '';
                 });
             }
             
             this.snackbar = {
                 show: true,
                 text: 'Error al procesar la subcategoría',
                 color: 'error'
             };
         } finally {
             this.isSubmitting = false;
         }
     }
 }
}
</script>