import { createApp } from 'vue'

import '../../css/vue.css'
import '@mdi/font/css/materialdesignicons.min.css'

import VueCookies from 'vue3-cookies'

// Vuetify
import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

import axios from "axios"

// import the root component App from a single-file component.
import App from './App.vue';

import router from "./router"

const vuetify = createVuetify({
    components,
    directives,
  })
  
const app = createApp(App)
app.use(vuetify).use(router).use(VueCookies)

app.config.globalProperties.$axios = axios
window.axios = axios

app.mount("#app")