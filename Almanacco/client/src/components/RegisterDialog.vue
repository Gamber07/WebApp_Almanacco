<template>
  <div>
    <v-dialog v-model="open" width="500">
      <template v-if="!noActivator" #activator="{ on, attrs }">
        <v-btn v-bind="attrs" v-on="on" class="btn-secondary">
          <v-icon left>mdi-account-plus</v-icon>
          Registrati
        </v-btn>
      </template>

          <!-- Dialog di registrazione utente accessibile dalla schermata pubblica. -->
      <v-card class="card-glass">
        <v-card-title class="gradient-text-accent">
          <v-icon left>mdi-account-check</v-icon>
          Crea il tuo account
        </v-card-title>
        <v-card-text class="pt-6">
          <v-text-field 
            v-model="username" 
            label="Username" 
            @keyup.enter="doRegister"
            prepend-inner-icon="mdi-account"
            variant="outlined"
            density="compact"
            class="mb-4"
          />
          <v-text-field 
            v-model="password" 
            label="Password" 
            type="password" 
            @keyup.enter="doRegister"
            prepend-inner-icon="mdi-lock"
            variant="outlined"
            density="compact"
            class="mb-4"
          />
          <v-text-field 
            v-model="password_confirm" 
            label="Conferma password" 
            type="password" 
            @keyup.enter="doRegister"
            prepend-inner-icon="mdi-lock-check"
            variant="outlined"
            density="compact"
          />
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn text @click="open=false">Annulla</v-btn>
          <v-btn class="btn-secondary" @click="doRegister">
            <v-icon left>mdi-account-plus</v-icon>
            Registrati
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import { ref } from 'vue'
import axios from 'axios'

export default {
  props: { noActivator: { type: Boolean, default: false } },
  setup() {
    const open = ref(false)
    const username = ref('')
    const password = ref('')
    const password_confirm = ref('')

    async function doRegister() {
      try {
        const res = await axios.post((import.meta.env.VITE_API_BASE ?? '') + '/registrazione', {
          username: username.value,
          password: password.value,
          password_confirm: password_confirm.value,
        })
        if (res.data.status === 'ok') {
          alert('Registrazione completata! Ora puoi accedere.')
          open.value = false
          // open login dialog automatically
          window.dispatchEvent(new Event('open-login'))
        }
      } catch (e) {
        alert(e.response?.data?.message || 'Errore registrazione')
      }
    }

    // allow external open via window event
    window.addEventListener('open-register', () => { open.value = true })

    return { open, username, password, password_confirm, doRegister }
  }
}
</script>

<style scoped>
.card-glass {
  border-radius: 16px !important;
}

.gradient-text-accent {
  background: linear-gradient(135deg, #a855f7, #06b6d4);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  font-weight: 600 !important;
}
</style>
