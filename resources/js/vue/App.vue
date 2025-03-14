<template>
    <v-app>
      <div style="display: flex; height: 100vh;">

         <!-- Side Navigation Drawer -->
         <v-navigation-drawer 
                v-model="drawer"
                color="#222631" 
                theme="dark"
                class="fill-height"
                permanent
                width="256"
                >
                  <div class="pa-4 d-flex align-center" style="height: 64px; border-bottom: 3px solid rgba(255,255,255,0.12);">
                    <v-icon icon="mdi-account" class="mr-2"></v-icon>
                  <div class="text-subtitle-1">Hola {{ user.name }}</div>
                </div>
            <v-list>

              <v-list-item to="/dashboard" active-color="indigo-darken-1">
                <template v-slot:prepend>
                  <v-icon>mdi-view-dashboard</v-icon>
                </template>
                <v-list-item-title>Dashboard</v-list-item-title>
              </v-list-item>
      
              <v-list-item to="/categories" active-color="indigo-darken-1">
                <template v-slot:prepend>
                  <v-icon>mdi-shape</v-icon>
                </template>
                <v-list-item-title>Categorías</v-list-item-title>
              </v-list-item>
      
              <v-list-item to="/subcategories" active-color="indigo-darken-1">
                <template v-slot:prepend>
                  <v-icon>mdi-shape-outline</v-icon>
                </template>
                <v-list-item-title>Subcategorías</v-list-item-title>
              </v-list-item>
      
              <v-list-item to="/products" active-color="indigo-darken-1">
                <template v-slot:prepend>
                  <v-icon>mdi-package-variant-closed</v-icon>
                </template>
                <v-list-item-title>Productos</v-list-item-title>
              </v-list-item>

              <v-list-item to="/suppliers" active-color="indigo-darken-1">
                <template v-slot:prepend>
                  <v-icon>mdi-package-variant-closed</v-icon>
                </template>
                <v-list-item-title>Proveedores</v-list-item-title>
              </v-list-item>
            </v-list>
          </v-navigation-drawer>

          <div style="flex-grow: 1; display: flex; flex-direction: column;">
       <!-- Top Navigation Bar -->
       <v-app-bar   color="#white" :elevation="0" height="64"    >
            <v-app-bar-nav-icon color="black" @click="drawer = !drawer"></v-app-bar-nav-icon>
            <v-spacer></v-spacer>
            <v-toolbar-title class="text-center text-h5">BackGenesis</v-toolbar-title>
            <v-spacer></v-spacer>

            <template v-if="isLoggedIn">
                 <v-menu location="bottom end">
                  <template  v-slot:activator="{ props }">
                    <v-btn icon v-bind="props">
                     
                        <v-avatar color="black">
                             <!-- <v-icon icon="mdi-account"></v-icon> -->
                            <span class="text-h6 text-white">{{ getUserInitials }}</span>
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
                    color="warning"
                    variant="text"
                    to="/login"
                >
                    Iniciar Sesión
                </v-btn>
            </template>

          </v-app-bar>

     <!-- Main Content -->
          <v-main class="grey lighten-2">
            <v-container fluid>
              <router-view></router-view>
            </v-container>
          </v-main>
        </div>
      </div>
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
          window.location.href = '/login';
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
      },
      computed: {
        getUserInitials() {
            if (!this.user?.name) return '?';
            return this.user.name
                .split(' ')
                .map(word => word[0])
                .join('')
                .toUpperCase()
                .substring(0, 2);
        }
    },
 
}
</script>


