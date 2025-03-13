<template>
    <div>
      <div class="text-center mb-6">
        <h1 class="text-h4 text-grey-darken-3 text-uppercase">Panel de Control</h1>
      </div>
      <v-row>
        <v-col cols="12" sm="6" md="3">
          <v-card :loading="isLoading" height="100%" class="elevation-3" hover  >
            <v-card-title class="d-flex align-center">
              <v-icon size="large" color="primary" class="mr-2">mdi-shape</v-icon>
              Categorías
            </v-card-title>
            <v-card-text>
              <div class="text-h3 text-center">{{ categories.length }}</div>
              <div class="text-caption text-center text-grey">Total de categorías registradas</div>
            </v-card-text>
            <v-card-actions>
              <v-btn block color="primary" variant="text" to="/categories">
                Ver Categorías
                <v-icon end>mdi-arrow-right</v-icon>
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-col>
  
        <v-col cols="12" sm="6" md="3">
          <v-card :loading="isLoading" height="100%" class="elevation-3" hover>
            <v-card-title class="d-flex align-center">
              <v-icon size="large" color="primary" class="mr-2">mdi-shape-outline</v-icon>
              Subcategorías
            </v-card-title>
            <v-card-text>
              <div class="text-h3 text-center">{{ subcategories.length }}</div>
              <div class="text-caption text-center text-grey">Total de subcategorías registradas</div>
            </v-card-text>
            <v-card-actions>
              <v-btn block color="primary" variant="text" to="subcategories">
                Ver Subcategorías
                <v-icon end>mdi-arrow-right</v-icon>
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-col>
  
        <v-col cols="12" sm="6" md="3">
          <v-card :loading="isLoading" height="100%" class="elevation-3" hover>
            <v-card-title class="d-flex align-center">
              <v-icon size="large" color="primary" class="mr-2">mdi-package-variant-closed</v-icon>
              Productos
            </v-card-title>
            <v-card-text>
              <div class="text-h3 text-center">{{ products.length }}</div>
              <div class="text-caption text-center text-grey">Total de productos registrados</div>
            </v-card-text>
            <v-card-actions>
              <v-btn block color="primary" variant="text" to="/products">
                Ver Productos
                <v-icon end>mdi-arrow-right</v-icon>
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" md="3">
          <v-card :loading="isLoading" height="100%" class="elevation-3" hover>
            <v-card-title class="d-flex align-center">
              <v-icon size="large" color="primary" class="mr-2">mdi-account-group</v-icon>
              Proveedores
            </v-card-title>
            <v-card-text>
              <div class="text-h3 text-center">{{ suppliers.length }}</div>
              <div class="text-caption text-center text-grey">Total de proveedores registrados</div>
            </v-card-text>
            <v-card-actions>
              <v-btn block color="primary" variant="text" to="/suppliers">
                Ver Proveedores
                <v-icon end>mdi-arrow-right</v-icon>
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-col>

  
      </v-row>


      <v-row class="mt-6">
        <v-col cols="12" md="8">
          <v-card class="elevation-3">
            <v-card-title>
              Actividad Reciente
              <v-spacer></v-spacer>
              <v-btn icon="mdi-refresh" @click="loadData"></v-btn>
            </v-card-title>
            <v-card-text>
              <v-timeline density="compact" align="start">
                <v-timeline-item
                  v-for="activity in recentActivity"
                  :key="activity.id"
                  :dot-color="activity.color"
                  size="small"
                >
                  <div class="text-subtitle-2">{{ activity.title }}</div>
                  <div class="text-caption">{{ activity.time }}</div>
                </v-timeline-item>
              </v-timeline>
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" md="4">
          <v-card class="elevation-3">
            <v-card-title>
              Estadísticas Rápidas
            </v-card-title>
            <v-card-text>
              <v-list>
                <v-list-item>
                  <template v-slot:prepend>
                    <v-icon color="primary">mdi-trending-up</v-icon>
                  </template>
                  <v-list-item-title>Productos más vistos</v-list-item-title>
                  <v-list-item-subtitle>{{ mostViewedProduct }}</v-list-item-subtitle>
                </v-list-item>
                <v-list-item>
                  <template v-slot:prepend>
                    <v-icon color="success">mdi-star</v-icon>
                  </template>
                  <v-list-item-title>Categoría más popular</v-list-item-title>
                  <v-list-item-subtitle>{{ topCategory }}</v-list-item-subtitle>
                </v-list-item>
              </v-list>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </div>
  </template>
  
  <script>
  export default {

    name: 'Dashboard',
    data() {
      return {
        categories: [],
        subcategories: [],
        products: [],
        suppliers: [],
        isLoading: true,
        recentActivity: [
        { id: 1, title: 'Nuevo producto agregado', time: 'Hace 5 minutos', color: 'primary' },
        { id: 2, title: 'Categoría actualizada', time: 'Hace 1 hora', color: 'info' },
        { id: 3, title: 'Usuario registrado', time: 'Hace 2 horas', color: 'success' },
      ],
      mostViewedProduct: 'Producto A',
      topCategory: 'Categoría Principal',
      }
    },
    methods: {
      async loadData() {
        this.isLoading = true;
      try {
        const [categoriesRes, subcategoriesRes, productsRes, suppliersRes] = await Promise.all([
          this.$axios.get('/api/categories/all'),
          this.$axios.get('/api/subcategories/all'),
          this.$axios.get('/api/products/all'),
          this.$axios.get('/api/suppliers/all'),
        ]);
  
        this.categories = categoriesRes.data;
        this.subcategories = subcategoriesRes.data;
        this.products = productsRes.data;
        this.suppliers = suppliersRes.data;
      } catch (error) {
        console.error('Error loading dashboard data:', error);
      }
    }
    } ,
    mounted() {
      this.loadData();
    }
    
  }
  </script>