<template>
    <v-app>
       <!-- Top Navigation Bar -->
       <v-app-bar color="#263238">
            <v-app-bar-nav-icon @click="drawer = !drawer"></v-app-bar-nav-icon>
            <v-toolbar-title>BackGenesis</v-toolbar-title>
            <v-spacer></v-spacer>

            <template v-if="isLoggedIn">
                 <v-menu location="bottom end">
                  <template  v-slot:activator="{ props }">
                    <v-btn icon v-bind="props">
                        <v-avatar color="grey-darken-1">
                            <v-icon icon="mdi-account"></v-icon>
                        </v-avatar>
                    </v-btn>
                  </template>

                  <v-list>
                      <v-list-item>
                          <v-list-item-title>{{ user?.name || 'Usuario' }}</v-list-item-title>
                      </v-list-item>
                      <v-divider></v-divider>
                      <v-list-item @click="logout">
                          <template v-slot:prepend>
                              <v-icon>mdi-logout</v-icon>
                          </template>
                          <v-list-item-title>Cerrar Sesión</v-list-item-title>
                      </v-list-item>
                  </v-list>
                  </v-menu>

              </template>

              <template v-else>
                <v-btn
                    color="primary"
                    variant="text"
                    to="/vue/login"
                >
                    Iniciar Sesión
                </v-btn>
            </template>
          </v-app-bar>

             <!-- Side Navigation Drawer -->
             <v-navigation-drawer v-model="drawer" app color="#263238" >

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
  mounted(){
    // console.log(window.laravel)

    //Procesamos los datos
    if(window.laravel && window.laravel.isLoggedIn){
      this.isLoggedIn = true
      this.user = window.laravel.user
      this.token = window.laravel.token

      //Actualizamos la cookie
      this.$root.setCookieAuth({
                isLoggedIn: this.isLoggedIn,
                token: this.token,
                user: this.user,
            })
    }else{
      const auth = this.$cookies.get('auth')
      if(auth && auth.token){
        this.isLoggedIn = true
        this.user = auth.user
        this.token = auth.token

        console.log(auth.token)
        //verificacion
        this.$axios.post(this.urls.tokenCheck,{
          token: auth.token
        }).then(()=> {
          console.log('tokenCheck')
        }).catch(()=>{
          console.log('Token inválido, cerrando sesión...');
          this.setCookieAuth(null);
          window.location.href = '/vue/login';
        })

      }else {
          this.setCookieAuth(null);
      }
    }

    console.log( this.$cookies.get('auth'))
   
  },
    name: 'App',
    data() {
          return {
            isLoggedIn: false,
            user: '',
            token: '',
            drawer: true,
            urls:{
              tokenCheck:'/api/user/token-check'
            }
          }
        },
      methods:{
        setCookieAuth(data){
            if(data){
              this.$cookies.set('auth', data)
        }else {
                this.$cookies.remove('auth');
                this.isLoggedIn = false;
                this.user = null;
                this.token = null;
            }
        },
    
        logout()
        {
          this.$axios.post('/api/logout', null, {
          headers: {
            'Authorization': `Bearer ${this.token}`,  // Agrega el token de acceso aquí
          }
        })
          .then(()=>{
            console.log("Sesión cerrada correctamente");
            this.setCookieAuth(null)
            this.$router.push({ name: 'login' });
          })
          .catch(()=>{
            console.log("Error al cerrar sesión en el backend, limpiando localmente...");
            this.setCookieAuth(null);
            this.isLoggedIn = false;
            this.$router.push({ name: 'login' });
          })
        }
      }
 
}
</script>