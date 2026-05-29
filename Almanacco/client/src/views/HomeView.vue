<template>
  <div class="home-view">
    <!-- Hero iniziale e collegamenti rapidi ai moduli principali dell'app. -->
    <!-- Hero Card -->
    <v-card class="card-glass glow-purple mb-8">
      <v-card-text class="hero-section">
        <h1 v-if="!auth.token" class="gradient-text text-center mb-2">Benvenuto in Almanacco</h1>
        <h1 v-else class="gradient-text text-center mb-2">Bentornato, {{ auth.user?.username }}! ⚽</h1>
        <p class="text-center text-subtitle-1" style="color: #cbd5e1;">La piattaforma premium per gestire il tuo campionato calcistico</p>
        
        <div v-if="!auth.token" class="text-center mt-8">
          <v-btn class="btn-premium" size="large" @click="openLogin">
            <v-icon left>mdi-login</v-icon> Accedi Ora
          </v-btn>
        </div>
        <div v-else class="text-center mt-8">
          <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <v-btn class="btn-premium" to="/squadre">
              <v-icon left>mdi-shield-account-outline</v-icon> Squadre
            </v-btn>
            <v-btn class="btn-secondary" to="/giocatori">
              <v-icon left>mdi-account-multiple</v-icon> Giocatori
            </v-btn>
            <v-btn class="btn-premium" to="/calendario">
              <v-icon left>mdi-calendar-outline</v-icon> Calendario
            </v-btn>
            <v-btn class="btn-secondary" to="/classifiche">
              <v-icon left>mdi-trophy-outline</v-icon> Classifiche
            </v-btn>
          </div>
        </div>
      </v-card-text>
    </v-card>

    <!-- Features Bento Grid -->
    <div class="bento-grid">
      <div class="bento-item card-glass glow-cyan" @click="$router.push('/squadre')" style="cursor: pointer;">
        <v-card-text class="h-full d-flex flex-column justify-center align-center text-center pa-6">
          <v-icon size="48" class="gradient-text mb-4">mdi-shield-account-outline</v-icon>
          <h3 class="text-h6 mb-2">Squadre</h3>
          <p style="color: #94a3b8; font-size: 0.9rem;">Gestisci le tue squadre e i loro dati</p>
        </v-card-text>
      </div>

      <div class="bento-item card-glass glow-purple" @click="$router.push('/giocatori')" style="cursor: pointer;">
        <v-card-text class="h-full d-flex flex-column justify-center align-center text-center pa-6">
          <v-icon size="48" class="text-cyan-400 mb-4">mdi-account-multiple</v-icon>
          <h3 class="text-h6 mb-2">Giocatori</h3>
          <p style="color: #94a3b8; font-size: 0.9rem;">Consulta i dati di tutti i giocatori</p>
        </v-card-text>
      </div>

      <div class="bento-item card-glass glow-cyan" @click="$router.push('/calendario')" style="cursor: pointer;">
        <v-card-text class="h-full d-flex flex-column justify-center align-center text-center pa-6">
          <v-icon size="48" class="gradient-text mb-4">mdi-calendar</v-icon>
          <h3 class="text-h6 mb-2">Calendario</h3>
          <p style="color: #94a3b8; font-size: 0.9rem;">Partite, risultati e statistiche</p>
        </v-card-text>
      </div>

      <div class="bento-item card-glass glow-purple" @click="$router.push('/classifiche')" style="cursor: pointer;">
        <v-card-text class="h-full d-flex flex-column justify-center align-center text-center pa-6">
          <v-icon size="48" class="text-cyan-400 mb-4">mdi-trophy-outline</v-icon>
          <h3 class="text-h6 mb-2">Classifiche</h3>
          <p style="color: #94a3b8; font-size: 0.9rem;">Vedi le posizioni in tempo reale</p>
        </v-card-text>
      </div>
    </div>
  </div>
</template>

<script>
import { useAuthStore } from '../stores/auth'

export default {
  name: 'HomeView',
  setup() {
    const auth = useAuthStore()
    function openLogin() { window.dispatchEvent(new Event('open-login')) }
    return { auth, openLogin }
  }
}
</script>

<style scoped>
.home-view {
  width: 100%;
}

.hero-section {
  padding: 3rem 2rem !important;
}

.bento-item {
  border-radius: 16px !important;
  padding: 0 !important;
  min-height: 200px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

.bento-item:hover {
  transform: translateY(-8px) !important;
}

.h-full {
  min-height: 200px;
}

.text-cyan-400 {
  color: #06b6d4 !important;
}
</style>
