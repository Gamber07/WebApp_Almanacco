<template>
  <div>
    <v-dialog v-model="open" width="500">
      <template v-if="!noActivator" #activator="{ on, attrs }">
        <v-btn v-bind="attrs" v-on="on" class="btn-premium">
          <v-icon left>mdi-login</v-icon>
          {{ label }}
        </v-btn>
      </template>

          <!-- Dialog di accesso usato dalla navbar e dai trigger globali. -->
      <v-card class="card-glass">
        <v-card-title class="gradient-text">
          <v-icon left>mdi-shield-account</v-icon>
          Accedi al tuo account
        </v-card-title>
        <v-card-text class="pt-6">
          <v-text-field 
            label="Username" 
            v-model="username" 
            @keyup.enter="doLogin"
            prepend-inner-icon="mdi-account"
            variant="outlined"
            density="compact"
            class="mb-4"
          />
          <v-text-field 
            label="Password" 
            v-model="password" 
            type="password" 
            @keyup.enter="doLogin"
            prepend-inner-icon="mdi-lock"
            variant="outlined"
            density="compact"
          />
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn text size="small" @click="openRegister" class="text-secondary">Non hai un account?</v-btn>
          <v-btn text @click="open=false">Annulla</v-btn>
          <v-btn class="btn-primary" @click="doLogin">
            <v-icon left>mdi-login</v-icon>
            Accedi
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import { ref } from 'vue'
import { useAuthStore } from '../stores/auth'
import { useRouter } from 'vue-router'

export default {
  props: { label: { type: String, default: 'Login' }, noActivator: { type: Boolean, default: false } },
  setup() {
    const open = ref(false)
    const username = ref('')
    const password = ref('')
    const auth = useAuthStore()

    const router = useRouter()

    async function doLogin() {
      try {
        const ok = await auth.login(username.value, password.value)
        if (ok) {
          // Small delay to ensure reactivity updates propagate
          await new Promise(resolve => setTimeout(resolve, 100))
          open.value = false
          router.push('/')
        } else {
          alert('Credenziali errate')
        }
      } catch (e) {
        alert('Errore login')
      }
    }

    function openRegister() {
      open.value = false
      window.dispatchEvent(new Event('open-register'))
    }

    // allow external open via window event
    window.addEventListener('open-login', () => { open.value = true })

    return { open, username, password, doLogin, openRegister }
  }
}
</script>

<style scoped>
.card-glass {
  border-radius: 16px !important;
}

.gradient-text {
  background: linear-gradient(135deg, #7c3aed, #06b6d4);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  font-weight: 600 !important;
}

.text-secondary {
  color: #06b6d4 !important;
}

.btn-primary {
  background: linear-gradient(135deg, #7c3aed, #06b6d4) !important;
  color: white !important;
}
</style>
