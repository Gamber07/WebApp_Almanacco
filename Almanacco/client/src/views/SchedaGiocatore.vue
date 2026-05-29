<template>
  <div class="detail-view">
    <!-- Scheda di dettaglio del giocatore con informazioni anagrafiche essenziali. -->
    <v-btn text @click="$router.back" class="back-btn mb-6">
      <v-icon left>mdi-arrow-left</v-icon>Torna indietro
    </v-btn>
    
    <v-card v-if="giocatore" class="card-glass detail-card glow-cyan">
      <v-card-text class="pa-8">
        <div class="detail-header">
          <div>
            <h1 class="gradient-text mb-2">{{ giocatore.nome }} {{ giocatore.cognome }}</h1>
            <v-chip class="role-badge">{{ giocatore.ruolo }}</v-chip>
          </div>
          <v-icon size="80" class="icon-accent">mdi-account-circle</v-icon>
        </div>
        
        <v-divider class="my-6" />
        
        <div class="detail-grid">
          <div class="detail-item">
            <p class="detail-label">Data di nascita</p>
            <p class="detail-value">{{ giocatore.data_nascita }}</p>
          </div>
          <div class="detail-item">
            <p class="detail-label">Ruolo</p>
            <p class="detail-value">{{ giocatore.ruolo }}</p>
          </div>
          <div class="detail-item">
            <p class="detail-label">Nazionalità</p>
            <p class="detail-value">{{ giocatore.nazionalita }}</p>
          </div>
        </div>
      </v-card-text>
    </v-card>
  </div>
</template>

<script>
import axios from 'axios'
import { useAuthStore } from '../stores/auth'
import NewGiocatoreDialog from '../components/NewGiocatoreDialog.vue'

export default {
  name: 'SchedaGiocatore',
  components: { },
  data() {
    return {
      giocatore: null,
      auth: useAuthStore()
    }
  },
  methods: {
    async fetchGiocatore() {
      const id = this.$route.params.id
      try {
        const res = await axios.get((import.meta.env.VITE_API_BASE ?? '') + `/giocatori/${id}`)
        this.giocatore = res.data.data?.giocatore || {}
      } catch (e) {
        console.error('Errore caricamento giocatore', e)
      }
    }
  },
  mounted() {
    this.fetchGiocatore()
  }
}
</script>

<style scoped>
.detail-view { width: 100%; }
.back-btn { color: #06b6d4 !important; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; transition: all 0.3s; }
.back-btn:hover { color: #7c3aed !important; transform: translateX(-4px); }
.detail-card { border-radius: 20px !important; max-width: 900px; margin: 0 auto; }
.detail-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 2rem; }
.detail-header h1 { font-size: 2.5rem; margin: 0; font-weight: 700; }
.role-badge { background: linear-gradient(135deg, rgba(124, 58, 237, 0.2), rgba(168, 85, 247, 0.2)) !important; color: #c4b5fd !important; border: 1px solid rgba(168, 85, 247, 0.3) !important; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
.icon-accent { color: #06b6d4 !important; opacity: 0.8; }
.detail-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; }
.detail-item { padding: 1.5rem; background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.1); border-radius: 12px; transition: all 0.3s; }
.detail-item:hover { background: rgba(6, 182, 212, 0.08); border-color: rgba(6, 182, 212, 0.2); }
.detail-label { color: #94a3b8; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 0.5rem 0; font-weight: 600; }
.detail-value { color: #e2e8f0; font-size: 1.1rem; font-weight: 600; margin: 0; }
</style>
