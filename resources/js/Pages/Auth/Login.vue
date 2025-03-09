<template>
    <div class="text-center mb-6">
        <h1 class="text-h4 text-grey-darken-3 text-uppercase ">Login</h1>
    </div>

    <div class="d-flex justify-center">

        <div class="pa-4" style="width: 100%; max-width: 600px;">

            <v-form @submit.prevent="submit" style="max-width: 600px; width:100%">
                <v-row>
                    <v-col cols="12">
                        <v-text-field 
                            v-model="form.email" 
                            label="Email" 
                            placeholder="correo@correo.com" 
                            type="email" 
                            :error-messages="errors.email"
                            variant="outlined">
                        </v-text-field>

                    </v-col>
                    <v-col cols="12">
                        <v-text-field 
                            v-model="form.password" 
                            label="Password" 
                            type="password"
                            :error-messages="errors.password"
                            variant="outlined" >
                        </v-text-field>

                    </v-col>

                    <v-col cols="12" class="text-center">
                        <v-btn color="primary" :disabled="disabledButton"  @click="submit" size="large" min-width="150">
                            Iniciar Sesión
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

import CustomSnackbar from '../../Components/CustomSnackBar.vue';

export default{
    components: {
        CustomSnackbar
    },
    mounted(){
        if (this.$cookies.get('auth')) {
        this.$router.push({ name: 'dashboard' });
    }
    },
    data(){
        return{
            disabledButton: false,
            form:{
                email:'admin@admin.com',
                password:'password'
            },
            errors:{
                email:'',
                password:'',
            },
            snackbar: {
                show: false,
                text: '',
                color: 'success'
            },
        }
    },
    methods:{
   
        cleanErrorsForm(){
            this.errors.email='';
            this.errors.password='';
        },
        validateForm() {
            let isValid = true;
            this.cleanErrorsForm();

            if (!this.form.email) {
                this.errors.email = 'El email es requerido';
                isValid = false;
            }
            if (!this.form.password) {
                this.errors.password = 'La contraseña es requerida';
                isValid = false;
            }

            return isValid;
        },
      
        submit(){
        
            if (!this.validateForm()) {
                return;
            }

            this.disabledButton = true
            
            axios.get('/sanctum/csrf-cookie').then(() => {
            axios.post('/api/vue/login',this.form).then(response => {
            // Login...
            console.log("Login exitoso:", response.data);

          this.$root.setCookieAuth({
                    isLoggedIn: true,
                    token: response.data.token,
                    user: response.data.user,
                });

            
            
            this.snackbar = {
                        show: true,
                        text: 'Inicio de sesión exitoso',
                        color: 'success'
                    };

                    setTimeout(() => {
                    // this.$router.push({ name: 'dashboard' }); // ✅ Usa Vue Router
                    window.location.href = '/vue/dashboard';
                }, 1000);

                // this.disabledButton = false

        }).catch(error => {
          console.log(error)
          this.disabledButton = false
          if(error.response?.data?.errors){
            this.errors = error.response.data.errors;
          }else{
                       this.snackbar = {
                            show: true,
                            text: error.response?.data?.message || 'Error al iniciar sesión',
                            color: 'error'
                        };
            }

        });
      
    })
    }
}
}


</script>