<template>
    <div class="container mx-auto">

        <div class="text-center mb-6">
            <h1 class="text-h4 text-grey-darken-3 text-uppercase">Categorias</h1>
        </div>
      

        <v-btn color="primary" rounded="lg" prepend-icon="mdi-plus" @click="$router.push({ name: 'saveCategory' })"> Crear
        </v-btn>
   
        <div class="mb-5"></div>

 

        <v-data-table :items="categories.data" :headers="headers" :loading="isLoading" class="elevation-1">
            <template v-slot:item.index="{ index }">
                {{ (currentPage - 1) * categories.per_page + index + 1 }}
            </template>
            <template v-slot:item.actions="{ item }">
                <div class="d-flex gap-2">
                    <v-btn icon color="warning" size="small"
                        @click="$router.push({ name: 'saveCategory', params: { 'id': item.id } })">
                        <v-icon>mdi-pencil</v-icon>
                    </v-btn>
                    <v-btn icon color="error" size="small" @click="deleteCategory(item.id)">
                        <v-icon>mdi-delete</v-icon>
                    </v-btn>
                </div>
            </template>
        </v-data-table>

        <div class="mt-4">
            <v-pagination v-if="categories.data && categories.data.length > 0" v-model="currentPage"
                :length="Math.ceil(categories.total / categories.per_page)" @update:modelValue="updatePage"
                rounded>
            </v-pagination>
        </div>

        <delete-dialog
            v-model:show="showDeleteDialog"
            title="Eliminar Categoría"
            message="¿Estás seguro de que quieres eliminar esta categoría? Esta acción no se puede deshacer."
            :loading="isDeleting"
            @confirm="confirmDelete"
        />

        <custom-snackbar
            v-model:show="snackbar.show"
            :text="snackbar.text"
            :color="snackbar.color"
        />


  
    </div>
</template>

<script>
import CustomSnackbar from '../Components/CustomSnackBar.vue';
import DeleteDialog from '../Components/DeleteDialog.vue';

export default {
    components: {
                    CustomSnackbar,
                    DeleteDialog
                },
    data() {
        return {
            headers: [
                { title: '#', key: 'index', align: 'start', sortable: false },
                { title: 'NOMBRE', key: 'name' },
                { title: 'DESCRIPCIÓN', key: 'description' },
                { title: 'ACCIONES', key: 'actions', sortable: false }
            ],
            categories: {
                data: [],
                total: 0,
                per_page: 10,
                current_page: 1,
                last_page: 1,
            },
            isLoading: true,
            currentPage: 1,
            selectedCategoryId: null,
            isDeleting: false,
            showDeleteDialog: false, 
            snackbar: {
                    show: false,
                    text: '',
                    color: 'success'
                },

        }
    },
    mounted() {
        this.listPage()

    },
    methods: {
        updatePage(page) {
            this.currentPage = page;
            this.listPage();
        },
        listPage() {

            this.isLoading = true;
            this.$axios.get('/api/categories?page=' + this.currentPage).then((res) => {
                this.categories = {
                    data: res.data.data,
                    total: res.data.total,
                    per_page: res.data.per_page,
                    current_page: res.data.current_page,
                    last_page: Math.ceil(res.data.total / res.data.per_page)
                }
                this.isLoading = false;
                console.log(this.categories)
            })

        },
        deleteCategory(id) {
            this.selectedCategoryId = id;
            this.showDeleteDialog = true;
        },

        async confirmDelete() {
            this.isDeleting = true;
            try {
                await this.$axios.delete('/api/categories/' + this.selectedCategoryId);
                this.listPage();
                this.showDeleteDialog = false;
                this.snackbar = {
                    show: true,
                    text: 'Categoria eliminada con éxito',
                    color: 'success'
                };
            } catch (error) {
                console.error('Error al eliminar la categoría:', error);
                this.snackbar = {
                    show: true,
                    text: 'Error al eliminar la categoria',
                    color: 'error'
                };
            } finally {
                this.isDeleting = false;
            }
        },


    }
}
</script>