<template>
    <div>
      <h1 class="text-h4 mb-4">Dashboard</h1>
      <v-row>
        <v-col cols="12" sm="6" md="4">
          <v-card>
            <v-card-title>
              <v-icon left color="primary" class="mr-2">mdi-shape</v-icon>
              Categorías
            </v-card-title>
            <v-card-text class="text-h5">
              {{ categories.length }}
            </v-card-text>
          </v-card>
        </v-col>
  
        <v-col cols="12" sm="6" md="4">
          <v-card>
            <v-card-title>
              <v-icon left color="primary" class="mr-2">mdi-shape-outline</v-icon>
              Subcategorías
            </v-card-title>
            <v-card-text class="text-h5">
              {{ subcategories.length }}
            </v-card-text>
          </v-card>
        </v-col>
  
        <v-col cols="12" sm="6" md="4">
          <v-card>
            <v-card-title>
              <v-icon left color="primary" class="mr-2">mdi-package-variant-closed</v-icon>
              Productos
            </v-card-title>
            <v-card-text class="text-h5">
              {{ products.length }}
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
        products: []
      }
    },
    async mounted() {
      try {
        const [categoriesRes, subcategoriesRes, productsRes] = await Promise.all([
          this.$axios.get('/api/categories/all'),
          this.$axios.get('/api/subcategories/all'),
          this.$axios.get('/api/products/all')
        ]);
  
        this.categories = categoriesRes.data;
        this.subcategories = subcategoriesRes.data;
        this.products = productsRes.data;
      } catch (error) {
        console.error('Error loading dashboard data:', error);
      }
    }
  }
  </script>