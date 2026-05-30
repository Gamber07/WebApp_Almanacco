import './assets/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import router from './router'
// Vuetify
import 'vuetify/styles'
import {createVuetify} from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import '@mdi/font/css/materialdesignicons.css'
import '@fontsource/roboto'

// global axios default (if token in localStorage)
import axios from 'axios'
axios.defaults.baseURL = import.meta.env.VITE_API_BASE || (import.meta.env.PROD ? '/api' : '')
const token = localStorage.getItem('token')
if (token) axios.defaults.headers.common['Authorization'] = 'Bearer ' + token

// Bootstrap del frontend: tema Vuetify, router, store e componenti globali.
const vuetify = createVuetify({
  components,
  directives,
  theme: {
    defaultTheme: 'dark',
    themes: {
      dark: {
        colors: {
          background: '#0f172a',
          surface: '#1a1f35',
          primary: '#7c3aed',
          secondary: '#06b6d4',
          accent: '#a855f7',
          error: '#ef4444',
          warning: '#f59e0b',
          info: '#3b82f6',
          success: '#10b981',
        }
      }
    }
  }
})

const app = createApp(App)

app.use(createPinia())
app.use(router)
app.use(vuetify)

// register global components
import LoginDialog from './components/LoginDialog.vue'
import RegisterDialog from './components/RegisterDialog.vue'
// I dialog di login/registrazione sono disponibili globalmente.
app.component('login-dialog', LoginDialog)
app.component('register-dialog', RegisterDialog)

// Initialize auth store (if token present) to fetch user
import { useAuthStore } from './stores/auth'
const auth = useAuthStore()
auth.init()

app.mount('#app')
