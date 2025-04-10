<template>
    <div class="container mx-auto">

        <div class="text-center mb-6">
            <h1 class="text-h4 text-grey-darken-3 text-uppercase">Proveedores</h1>
        </div>


        <v-btn color="primary" rounded="lg" prepend-icon="mdi-plus" @click="$router.push({ name: 'saveSupplier' })"> Crear
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
            :items="filteredSuppliers"
            :headers="headers"
            :loading="isLoading"
            :items-per-page="suppliers.per_page"
            hide-default-footer
            class="elevation-1 force-bold-headers"
            >

            <template v-slot:item.index="{ index }">
                {{ (currentPage - 1) * suppliers.per_page + index + 1 }}
            </template>

            <template v-slot:item.actions="{ item }">
                <div class="d-flex gap-2">
                    <v-btn icon color="warning" size="small"
                        @click="$router.push({ name: 'saveSupplier', params: { 'id': item.id } })">
                        <v-icon>mdi-pencil</v-icon>
                    </v-btn>
                   <v-btn icon color="error" size="small" @click="deleteSupplier(item.id)">
                        <v-icon>mdi-delete</v-icon>
                    </v-btn>
                </div>
            </template>
        </v-data-table>

        <div class="mt-4">
            <v-pagination v-if="suppliers.data && suppliers.data.length > 0" v-model="currentPage"
                :length="Math.ceil(suppliers.total / suppliers.per_page)" @update:modelValue="updatePage"
                rounded>
            </v-pagination>
        </div>

        <delete-dialog
            v-model:show="showDeleteDialog"
            title="Eliminar Proveedor"
            message="¿Estás seguro de que quieres eliminar este proveedor? Esta acción no se puede deshacer."
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
                { title: 'EMAIL', key: 'email' },
                { title: 'DIRECCIÖN', key: 'address' },
                { title: 'TELEFONO', key: 'phone' },
                { title: 'ACCIONES', key: 'actions', sortable: false }
            ],
            suppliers: {
                data: [],
                total: 0,
                per_page: 10,
                current_page: 1,
                last_page: 1,
            },
            isLoading: true,
            currentPage: 1,
            selectedSupplierId: null,
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
        filteredSuppliers(){
            if(!this.search){
                return this.suppliers.data; // Si no hay búsqueda, mostrar todos los datos
            }
                const searchLower = this.search.toLowerCase();
                return this.suppliers.data.filter(supplier => supplier.name.toLowerCase().includes(searchLower) ||
                (supplier.description && supplier.description.toLowerCase().includes(searchLower))
            );
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
            const config = {
                headers: {
                Authorization: `Bearer ${this.$root.token}`
            }
            }

            this.isLoading = true;
            this.$axios.get('/api/suppliers?page=' + this.currentPage, config).then((res) => {
                this.suppliers = {
                    data: res.data.data,
                    total: res.data.total,
                    per_page: res.data.per_page,
                    current_page: res.data.current_page,
                    last_page: Math.ceil(res.data.total / res.data.per_page)
                }
                this.isLoading = false;
                console.log(this.suppliers)
            })

        },
        deleteSupplier(id) {
            this.selectedSupplierId = id;
            this.showDeleteDialog = true;
        },

        async confirmDelete() {
            this.isDeleting = true;
            try {
                await this.$axios.delete('/api/suppliers/' + this.selectedSupplierId);
                this.listPage();
                this.showDeleteDialog = false;
                this.snackbar = {
                    show: true,
                    text: 'Proveedor eliminado con éxito',
                    color: 'success'
                };

            } catch (error) {
                console.error('Error al eliminar el proveedor:', error);
                this.snackbar = {
                    show: true,
                    text: 'Error al eliminar el proveedor',
                    color: 'error'
                };
            } finally {
                this.isDeleting = false;
            }
        },


    }
}
</script>
