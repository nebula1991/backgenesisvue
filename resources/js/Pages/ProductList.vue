<template>
    <div class="container mx-auto">

        <div class="text-center mb-6">
            <h1 class="text-h4 text-grey-darken-3 text-uppercase">Productos</h1>
        </div>


        <v-btn color="primary" rounded="lg" prepend-icon="mdi-plus" @click="$router.push({ name: 'saveProduct' })"> Crear
        </v-btn>

        <div class="mb-5"></div>

        <v-card-title class="d-flex align-center pe-2">

            <v-spacer></v-spacer>

            <v-text-field
                v-model="search"
                density="compact"
                label="Search"
                prepend-inner-icon="mdi-magnify"
                variant="solo-filled"
                flat
                hide-details
                single-line
            ></v-text-field>

        </v-card-title>

        <v-data-table
            :items="filteredProducts"
            :headers="headers"
            :loading="isLoading"
            :items-per-page="products.per_page"
            hide-default-footer
            class="elevation-1 force-bold-headers"

        >
            <template v-slot:item.index="{ index }">
                {{ (currentPage - 1) * products.per_page + index + 1 }}
            </template>
            <template v-slot:item.stock="{ item }">
                <div class="d-flex align-center">
                    <v-progress-circular
                        :model-value="getStockPercentage(item)"
                        :color="getColor(item)"
                        size="32"
                        width="4"
                        class="mr-3"
                    >
                        <span class="text-body-2 font-weight-bold">{{ item.stock }}</span>
                    </v-progress-circular>

                </div>
            </template>
            <template v-slot:item.price="{ item }">
                {{ item.price }}€
            </template>
            <template v-slot:item.actions="{ item }">
                <div class="d-flex gap-2">
                    <v-btn
                        icon
                        color="warning"
                        size="small"
                        @click="$router.push({name:'saveProduct', params:{'id': item.id}})"
                    >
                        <v-icon>mdi-pencil</v-icon>
                    </v-btn>
                    <v-btn
                        icon
                        color="error"
                        size="small"
                        @click="deleteProduct(item.id)"
                    >
                        <v-icon>mdi-delete</v-icon>
                    </v-btn>
                </div>
            </template>
        </v-data-table>

        <div class="mb-5"></div>

        <div class="mt-4">
            <v-pagination
                v-if="products.data && products.data.length > 0"
                v-model="currentPage"
                :length="Math.ceil(products.total / products.per_page)"
                @update:modelValue="updatePage"
                rounded
            ></v-pagination>
        </div>

        <delete-dialog
            v-model:show="showDeleteDialog"
            title="Eliminar Producto"
            message="¿Estás seguro de que quieres eliminar este producto? Esta acción no se puede deshacer."
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

    export default{
        components: {
                    CustomSnackbar,
                    DeleteDialog
                },
        data(){
            return{

                headers:[
                    { title: '#', key: 'index', align: 'start', sortable: false },
                    { title: 'NOMBRE', key: 'name' },
                    { title: 'DESCRIPCIÓN', key: 'description' },
                    { title: 'PRECIO', key: 'price' },
                    { title: 'STOCK', key: 'stock' },
                    { title: 'CATEGORÍA', key: 'category.name' },
                    { title: 'SUBCATEGORÍA', key: 'subcategory.name' },
                    { title: 'ACCIONES', key: 'actions', sortable: false }
                ],
                products: {
                    data: [],
                    total: 0,
                    per_page: 10,
                    current_page: 1,
                    last_page: 1,
                },

                isLoading: true,
                currentPage:1,
                showDeleteDialog: false,
                selectedProductId: null,
                isDeleting: false,
                showDeleteDialog: false,
                snackbar: {
                    show: false,
                    text: '',
                    color: 'success'
                },
                search: '',


            }
        },
        computed: {
        filteredProducts(){
            if(!this.search){
                return this.products.data; // Si no hay búsqueda, mostrar todos los datos
            }
                const searchLower = this.search.toLowerCase();
                return this.products.data.filter(product => product.name.toLowerCase().includes(searchLower) ||
                (product.description && product.description.toLowerCase().includes(searchLower))
            );
        }
    },

        mounted(){
            this.listPage()

        },

        methods:{
                updatePage(page){
                    this.currentPage = page;
                    this.listPage();
                    console.log(this.currentPage)
                },
                listPage(){
                    const config = {
                headers: {
                Authorization: `Bearer ${this.$root.token}`
                }
                }
                    this.isLoading = true;
                        this.$axios.get('/api/products?page='+this.currentPage).then((res) => {
                            this.products = {
                                data: res.data.data,
                                total: res.data.total,
                                per_page: res.data.per_page,
                                current_page: res.data.current_page,
                                last_page: Math.ceil(res.data.total / res.data.per_page)
                            }
                            this.isLoading = false;
                        })
                },
                getStockPercentage(item) {
                    // Asume que el stock máximo esperado es 50 para la visualización
                    const maxStock = 50
                    return Math.min((item.stock / maxStock) * 100, 100)
                },
                getColor(item) {
                    if (item.stock > 10) return 'success'
                    if (item.stock > 0) return 'warning'
                    return 'error'
                },

                            deleteProduct(id){
                        this.selectedProductId = id;
                        this.showDeleteDialog = true;
                        },
                        async confirmDelete() {
                        this.isDeleting = true;
                        try{
                            await this.$axios.delete('/api/products/' + this.selectedProductId);
                            this.listPage();
                            this.showDeleteDialog = false;
                            this.snackbar = {
                                show: true,
                                text: 'Producto eliminadao con éxito',
                                color: 'success'
                            };
                        }catch (error) {
                            console.error('Error al eliminar el producto:', error);
                            this.snackbar = {
                                show: true,
                                text: 'Error al eliminar el producto',
                                color: 'error'
                            };
                        } finally {
                            this.isDeleting = false;
                        }
                    },

                }
            }


</script>
