<template>
    <v-app>
       <!-- Top Navigation Bar -->
       <v-app-bar color="#263238">
            <v-app-bar-nav-icon @click="drawer = !drawer"></v-app-bar-nav-icon>
            <v-toolbar-title>BackGenesis</v-toolbar-title>
            <v-spacer></v-spacer>

                 <v-menu location="bottom end">
           
           
                </v-menu>
          </v-app-bar>

             <!-- Side Navigation Drawer -->
             <v-navigation-drawer v-model="drawer" app color="#263238">
            <v-list>
              <v-list-item to="/vue/dashboard" active-color="primary">
                <template v-slot:prepend>
                  <v-icon>mdi-view-dashboard</v-icon>
                </template>
                <v-list-item-title>Dashboard</v-list-item-title>
              </v-list-item>
      
              <v-list-item to="/vue/categories" active-color="primary">
                <template v-slot:prepend>
                  <v-icon>mdi-shape</v-icon>
                </template>
                <v-list-item-title>Categorías</v-list-item-title>
              </v-list-item>
      
              <v-list-item to="/vue/subcategories" active-color="primary">
                <template v-slot:prepend>
                  <v-icon>mdi-shape-outline</v-icon>
                </template>
                <v-list-item-title>Subcategorías</v-list-item-title>
              </v-list-item>
      
              <v-list-item to="/vue/products" active-color="primary">
                <template v-slot:prepend>
                  <v-icon>mdi-package-variant-closed</v-icon>
                </template>
                <v-list-item-title>Productos</v-list-item-title>
              </v-list-item>
            </v-list>
          </v-navigation-drawer>

     <!-- Main Content -->
          <v-main>
            <v-container fluid>
              <router-view></router-view>
            </v-container>
          </v-main>
</v-app>
</template>

<script>
export default {
    name: 'App',
    data() {
          return {
            drawer: true,
          }
        },
}
</script>