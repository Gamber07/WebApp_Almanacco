<template>
  <v-app>
    <v-app-bar app class="navbar-glass">
      <v-toolbar-title to="/" class="text-decoration-none">
        <router-link to="/" class="gradient-text text-decoration-none" style="font-size: 1.3rem; font-weight: 700; letter-spacing: 0.5px;">⚽ ALMANACCO</router-link>
      </v-toolbar-title>
      <v-spacer />
      <!-- Public navigation: Home | Classifiche | Calendario | Squadre | Giocatori -->
      <v-btn text to="/" :href="null" class="nav-btn">Home</v-btn>
      <v-btn text to="/classifiche" :href="null" class="nav-btn">Classifiche</v-btn>
      <v-btn text to="/calendario" :href="null" class="nav-btn">Calendario</v-btn>
      <v-btn text to="/squadre" :href="null" class="nav-btn">Squadre</v-btn>
      <v-btn text to="/giocatori" :href="null" class="nav-btn">Giocatori</v-btn>

      <v-divider vertical class="mx-3" />

      <!-- Auth section: anonymous, user, or admin -->
      <template v-if="!auth.token">
        <!-- Anonymous: show only Login -->
        <v-btn text @click="openLogin" class="nav-btn">Accedi</v-btn>
      </template>
      <template v-else-if="auth.user?.ruolo === 'admin'">
        <!-- Admin: show username + Logout -->
        <v-btn text disabled class="nav-btn-disabled">{{ auth.user.username }} <v-icon small class="ml-2">mdi-shield-admin</v-icon></v-btn>
        <v-btn text @click="logout" class="nav-btn">Logout</v-btn>
      </template>
      <template v-else>
        <!-- Standard user: show welcome + Logout -->
        <v-btn text disabled class="nav-btn-disabled">{{ auth.user?.username }}</v-btn>
        <v-btn text @click="logout" class="nav-btn">Logout</v-btn>
      </template>

      <!-- Hidden login/register dialogs triggered by window events -->
      <register-dialog :no-activator="true" />
      <login-dialog :no-activator="true" label="Login" />
    </v-app-bar>

    <v-main>
      <v-container fluid class="main-container">
        <router-view />
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
import LoginDialog from './components/LoginDialog.vue'
import RegisterDialog from './components/RegisterDialog.vue'

import { useAuthStore } from './stores/auth'

export default {
  name: 'App',
  components: { LoginDialog, RegisterDialog },
  setup() {
    const auth = useAuthStore()
    function logout() { auth.clear(); location.reload() }
    function openLogin() { window.dispatchEvent(new Event('open-login')) }
    return { auth, logout, openLogin }
  }
}
</script>

<style scoped>
.navbar-glass {
  background: rgba(15, 23, 42, 0.7) !important;
  backdrop-filter: blur(16px) !important;
  border-bottom: 1px solid rgba(148, 163, 184, 0.12) !important;
  box-shadow: 
    inset 0 1px 1px rgba(255, 255, 255, 0.05),
    0 10px 30px rgba(0, 0, 0, 0.3) !important;
}

.nav-btn {
  color: #cbd5e1 !important;
  font-weight: 500 !important;
  letter-spacing: 0.3px !important;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
  text-transform: uppercase;
  font-size: 0.85rem;
}

.nav-btn:hover {
  color: #7c3aed !important;
  text-shadow: 0 0 10px rgba(124, 58, 237, 0.5);
}

.nav-btn-disabled {
  color: #94a3b8 !important;
  font-weight: 500 !important;
  cursor: default !important;
  font-size: 0.85rem;
}

.main-container {
  padding: 2.5rem 1rem !important;
  max-width: 1600px;
  margin: 0 auto;
}

a {
  text-decoration: none;
}
</style>
