<template>
  <v-app>
    <v-app-bar app class="navbar-glass">
      <v-app-bar-nav-icon
        v-if="mobile"
        class="mobile-nav-icon"
        @click="drawer = !drawer"
      />
      <v-toolbar-title to="/" class="text-decoration-none">
        <router-link to="/" class="gradient-text text-decoration-none" style="font-size: 1.3rem; font-weight: 700; letter-spacing: 0.5px;">ALMANACCO</router-link>
      </v-toolbar-title>
      <v-spacer />
      <!-- Public navigation: Home | Classifiche | Calendario | Squadre | Giocatori -->
      <div v-if="!mobile" class="desktop-nav">
        <v-btn text to="/" :href="null" class="nav-btn">Home</v-btn>
        <v-btn text to="/classifiche" :href="null" class="nav-btn">Classifiche</v-btn>
        <v-btn text to="/calendario" :href="null" class="nav-btn">Calendario</v-btn>
        <v-btn text to="/squadre" :href="null" class="nav-btn">Squadre</v-btn>
        <v-btn text to="/giocatori" :href="null" class="nav-btn">Giocatori</v-btn>

        <v-divider vertical class="mx-3" />
      </div>

      <!-- Auth section: anonymous, user, or admin -->
      <div v-if="!mobile" class="desktop-auth">
        <template v-if="!auth.token">
          <v-btn text @click="openLogin" class="nav-btn">Accedi</v-btn>
        </template>
        <template v-else-if="auth.user?.ruolo === 'admin'">
          <v-btn text disabled class="nav-btn-disabled">{{ auth.user.username }} <v-icon small class="ml-2">mdi-shield-admin</v-icon></v-btn>
          <v-btn text @click="logout" class="nav-btn">Logout</v-btn>
        </template>
        <template v-else>
          <v-btn text disabled class="nav-btn-disabled">{{ auth.user?.username }}</v-btn>
          <v-btn text @click="logout" class="nav-btn">Logout</v-btn>
        </template>
      </div>

      <!-- Hidden login/register dialogs triggered by window events -->
      <register-dialog :no-activator="true" />
      <login-dialog :no-activator="true" label="Login" />
    </v-app-bar>

    <v-navigation-drawer v-model="drawer" temporary location="left" class="mobile-drawer">
      <div class="drawer-content">
        <v-btn variant="text" block to="/" class="drawer-link">Home</v-btn>
        <v-btn variant="text" block to="/classifiche" class="drawer-link">Classifiche</v-btn>
        <v-btn variant="text" block to="/calendario" class="drawer-link">Calendario</v-btn>
        <v-btn variant="text" block to="/squadre" class="drawer-link">Squadre</v-btn>
        <v-btn variant="text" block to="/giocatori" class="drawer-link">Giocatori</v-btn>

        <v-divider class="drawer-divider my-4" />

        <template v-if="!auth.token">
          <v-btn block class="btn-premium" @click="openLogin; drawer = false">Accedi</v-btn>
        </template>
        <template v-else>
          <div class="drawer-user">{{ auth.user?.username }}</div>
          <v-btn block class="btn-secondary" @click="logout">Logout</v-btn>
        </template>
      </div>
    </v-navigation-drawer>

    <v-main>
      <v-container fluid class="main-container">
        <router-view />
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
import { ref } from 'vue'
import LoginDialog from './components/LoginDialog.vue'
import RegisterDialog from './components/RegisterDialog.vue'

import { useAuthStore } from './stores/auth'
import { useDisplay } from 'vuetify'

export default {
  name: 'App',
  components: { LoginDialog, RegisterDialog },
  setup() {
    const auth = useAuthStore()
    const { mobile } = useDisplay()
    const drawer = ref(false)
    function logout() { auth.clear(); location.reload() }
    function openLogin() { window.dispatchEvent(new Event('open-login')) }
    return { auth, logout, openLogin, mobile, drawer }
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

.desktop-nav,
.desktop-auth {
  display: flex;
  align-items: center;
}

.mobile-nav-icon {
  margin-right: 0.5rem;
}

.mobile-drawer {
  background: rgba(15, 23, 42, 0.98) !important;
  color: #e2e8f0 !important;
}

.drawer-content {
  padding: 1.25rem;
}

.drawer-link {
  justify-content: flex-start !important;
  color: #e2e8f0 !important;
  margin-bottom: 0.35rem;
  letter-spacing: 0.2px;
}

.drawer-divider {
  border-color: rgba(148, 163, 184, 0.12) !important;
}

.drawer-user {
  color: #94a3b8;
  font-size: 0.9rem;
  margin-bottom: 0.75rem;
}

.main-container {
  padding: 2.5rem 1rem !important;
  max-width: 1600px;
  margin: 0 auto;
}

a {
  text-decoration: none;
}

@media (max-width: 960px) {
  .main-container {
    padding: 1rem 0.75rem 1.5rem !important;
  }
}

@media (max-width: 600px) {
  .navbar-glass {
    padding-inline: 0.5rem !important;
  }

  .main-container {
    padding: 0.75rem 0.5rem 1rem !important;
  }
}
</style>
