<template>
  <div class="home-view">
    <!-- Hero iniziale e collegamenti rapidi ai moduli principali dell'app. -->
    <!-- Hero Card -->
    <v-card class="card-glass glow-purple mb-8">
      <v-card-text class="hero-section">
        <h1 v-if="!auth.token" class="text-center mb-2">Benvenuto in Almanacco</h1>
        <h1 v-else class="text-center mb-2">Bentornato, {{ auth.user?.username }}!</h1>
        <p class="text-center text-subtitle-1" style="color: #cbd5e1;">La piattaforma per gestire il tuo campionato calcistico</p>
        
        <div v-if="!auth.token" class="text-center mt-8">
          <v-btn class="btn-premium hero-action" size="large" @click="openLogin">
            <v-icon left>mdi-login</v-icon> Accedi Ora
          </v-btn>
        </div>
      </v-card-text>
    </v-card>

    <!-- Features Bento Grid -->
    <div class="bento-grid">
      <v-card class="bento-item card-glass glow-cyan" role="button" tabindex="0" @click="goTo('/squadre')" @keyup.enter="goTo('/squadre')">
        <v-card-text class="h-full d-flex flex-column justify-center align-center text-center pa-6">
          <v-icon size="48" class="mb-4">mdi-shield-account-outline</v-icon>
          <h3 class="text-h6 mb-2">Squadre</h3>
          <p style="color: #94a3b8; font-size: 0.9rem;">Gestisci le tue squadre e i loro dati</p>
        </v-card-text>
      </v-card>

      <v-card class="bento-item card-glass glow-purple" role="button" tabindex="0" @click="goTo('/giocatori')" @keyup.enter="goTo('/giocatori')">
        <v-card-text class="h-full d-flex flex-column justify-center align-center text-center pa-6">
          <v-icon size="48" class="mb-4">mdi-account-multiple</v-icon>
          <h3 class="text-h6 mb-2">Giocatori</h3>
          <p style="color: #94a3b8; font-size: 0.9rem;">Consulta i dati di tutti i giocatori</p>
        </v-card-text>
      </v-card>

      <v-card class="bento-item card-glass glow-cyan" role="button" tabindex="0" @click="goTo('/calendario')" @keyup.enter="goTo('/calendario')">
        <v-card-text class="h-full d-flex flex-column justify-center align-center text-center pa-6">
          <v-icon size="48" class="mb-4">mdi-calendar</v-icon>
          <h3 class="text-h6 mb-2">Calendario</h3>
          <p style="color: #94a3b8; font-size: 0.9rem;">Partite, risultati e statistiche</p>
        </v-card-text>
      </v-card>

      <v-card class="bento-item card-glass glow-purple" role="button" tabindex="0" @click="goTo('/classifiche')" @keyup.enter="goTo('/classifiche')">
        <v-card-text class="h-full d-flex flex-column justify-center align-center text-center pa-6">
          <v-icon size="48" class="mb-4">mdi-trophy-outline</v-icon>
          <h3 class="text-h6 mb-2">Classifiche</h3>
          <p style="color: #94a3b8; font-size: 0.9rem;">Vedi le posizioni in tempo reale</p>
        </v-card-text>
      </v-card>

      <template v-if="auth.user?.ruolo === 'admin'">
        <v-card class="bento-item card-glass glow-purple" role="button" tabindex="0" @click="goTo('/stagioni')" @keyup.enter="goTo('/stagioni')">
          <v-card-text class="h-full d-flex flex-column justify-center align-center text-center pa-6">
            <v-icon size="48" class="mb-4">mdi-calendar-multiple</v-icon>
            <h3 class="text-h6 mb-2">Stagioni</h3>
            <p style="color: #94a3b8; font-size: 0.9rem;">Archivio stagioni e storico</p>
          </v-card-text>
        </v-card>

        <v-card class="bento-item card-glass glow-cyan" role="button" tabindex="0" @click="goTo('/contratti')" @keyup.enter="goTo('/contratti')">
          <v-card-text class="h-full d-flex flex-column justify-center align-center text-center pa-6">
            <v-icon size="48" class="mb-4">mdi-file-document-outline</v-icon>
            <h3 class="text-h6 mb-2">Contratti</h3>
            <p style="color: #94a3b8; font-size: 0.9rem;">Elenco contratti e ingaggi</p>
          </v-card-text>
        </v-card>

        <v-card class="bento-item card-glass glow-purple" role="button" tabindex="0" @click="goTo('/admin/gestione-campionati')" @keyup.enter="goTo('/admin/gestione-campionati')">
          <v-card-text class="h-full d-flex flex-column justify-center align-center text-center pa-6">
            <v-icon size="48" class="mb-4">mdi-trophy-award</v-icon>
            <h3 class="text-h6 mb-2">Campionati</h3>
            <p style="color: #94a3b8; font-size: 0.9rem;">Gestione dei campionati</p>
          </v-card-text>
        </v-card>
      </template>
    </div>
  </div>
</template>

<script>
import { useAuthStore } from '../stores/auth'
import { useRouter } from 'vue-router'

export default {
  name: 'HomeView',
  setup() {
    const auth = useAuthStore()
    const router = useRouter()
    function openLogin() { window.dispatchEvent(new Event('open-login')) }
    function goTo(path) { router.push(path) }
    return { auth, openLogin, goTo }
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

.hero-action {
  width: 100%;
  min-height: 48px;
}

@media (max-width: 960px) {
  .hero-section {
    padding: 2.25rem 1.5rem !important;
  }
}

@media (max-width: 600px) {
  .hero-section {
    padding: 1.5rem 1rem !important;
  }

  .hero-section h1 {
    font-size: 1.55rem;
    line-height: 1.2;
  }

  .hero-action {
    width: 100%;
  }

  .bento-item {
    min-height: 160px;
  }

  .h-full {
    min-height: 160px;
  }
}

</style>
