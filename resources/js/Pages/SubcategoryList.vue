<template>
    <div class="container mx-auto">

        <div class="text-center mb-6">
            <h1 class="text-h4 text-grey-darken-3 text-uppercase">Subcategorias</h1>
        </div>
        

        <v-btn color="primary" rounded="lg" prepend-icon="mdi-plus"
            @click="$router.push({ name: 'saveSubcategory' })">Crear</v-btn>

        <div class="mb-5"></div>

        <v-data-table :items="subcategories.data" :headers="headers" :loading="isLoading" class="elevation-1">
            <template v-slot:item.index="{ index }">
                {{ (currentPage - 1) * subcategories.per_page + index + 1 }}
            </template>
            <template v-slot:item.actions="{ item }">
                <div class="d-flex gap-2">
                    <v-btn icon color="warning" size="small"
                        @click="$router.push({ name: 'saveSubcategory', params: { 'id': item.id } })">
                        <v-icon>mdi-pencil</v-icon>
                    </v-btn>
                    <v-btn icon color="error" size="small" @click="deleteSubcategory(item.id)">
                        <v-icon>mdi-delete</v-icon>
                    </v-btn>
                </div>
            </template>


        </v-data-table>



        <div class="mt-4">

        <v-pagination    
                v-if="subcategories.data && subcategories.data.length > 0"
                v-model="currentPage"
                :length="Math.ceil(subcategories.total / subcategories.per_page)"
                @update:modelValue="updatePage"
                rounded>
        </v-pagination>

        </div>


        <delete-dialog
            v-model:show="showDeleteDialog"
            title="Eliminar Subcategoria"
            message="¿Estás seguro de que quieres eliminar esta subcategoría? Esta acción no se puede deshacer."
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
import CustomSnackbar from '../Components/CustomSnackbar.vue';
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
                {title: 'CATEGORIA', key: 'category.name'},
                { title: 'ACCIONES', key: 'actions', sortable: false }
            ],
                subcategories: {
                data: [],
                total: 0,
                per_page: 10,
                current_page: 1,
                last_page: 1,
            },
            isLoading: true,
            currentPage: 1,
            small: true,
            isModalActive: false,
            selectedSubcategoryId: null,
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
            this.$axios.get('/api/subcategories?page=' + this.currentPage).then((res) => {
                console.log('API Response:', res.data); // Debug log
                this.subcategories = {
                    data: res.data.data,
                    total: res.data.total,
                    per_page: res.data.per_page,
                    current_page: res.data.current_page,
                    last_page: Math.ceil(res.data.total / res.data.per_page)
                }
                this.isLoading = false;
                console.log(this.subcategories)
            })

        },
        deleteSubcategory(id) {
            this.selectedSubcategoryId = id;
            this.showDeleteDialog = true;
        },

        async confirmDelete() {
            this.isDeleting = true;
            try{
                await this.$axios.delete('/api/subcategories/' + this.selectedSubcategoryId);
                this.listPage();
                this.showDeleteDialog = false;
                this.snackbar = {
                    show: true,
                    text: 'Subcategoria eliminada con éxito',
                    color: 'success'
                };
            }catch (error) {
                console.error('Error al eliminar la subcategoría:', error);
                this.snackbar = {
                    show: true,
                    text: 'Error al eliminar la subcategoria',
                    color: 'error'
                };
            } finally {
                this.isDeleting = false;
            }
        },

    }
}
</script>