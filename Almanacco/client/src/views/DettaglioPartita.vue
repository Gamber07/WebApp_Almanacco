<template>
  <div class="detail-view">
    <!-- Scheda di dettaglio della partita con risultato e dati contestuali. -->
    <v-btn text @click="$router.back" class="back-btn mb-6">
      <v-icon left>mdi-arrow-left</v-icon>Torna indietro
    </v-btn>
    
    <v-card v-if="partita" class="card-glass detail-card glow-purple">
      <v-card-text class="pa-8">
        <div class="detail-header">
          <div class="flex-grow">
            <h1 class="match-title mb-4">{{ partita.casa }} <span class="vs">vs</span> {{ partita.trasferta }}</h1>
            <v-chip class="season-badge">{{ partita.nome_stagione }}</v-chip>
          </div>
          <div class="result-display">
            <div class="result-score">{{ partita.gol_casa }}<span class="dash">-</span>{{ partita.gol_trasferta }}</div>
          </div>
        </div>
        
        <v-divider class="my-6" />
        
        <div class="detail-grid">
          <div class="detail-item">
            <p class="detail-label">Data Partita</p>
            <p class="detail-value">{{ partita.data_partita }}</p>
          </div>
          <div class="detail-item">
            <p class="detail-label">Sede</p>
            <p class="detail-value">{{ partita.sede }}</p>
          </div>
          <div class="detail-item">
            <p class="detail-label">Stagione</p>
            <p class="detail-value">{{ partita.nome_stagione }}</p>
          </div>
        </div>
      </v-card-text>
    </v-card>
  </div>
</template>

<script>
import axios from 'axios'
import { useAuthStore } from '../stores/auth'
import NewPartitaDialog from '../components/NewPartitaDialog.vue'

export default {
  name: 'DettaglioPartita',
  components: { },
  data() {
    return {
      partita: null,
      auth: useAuthStore()
    }
  },
  methods: {
    async fetchPartita() {
      const id = this.$route.params.id
      try {
        const res = await axios.get((import.meta.env.VITE_API_BASE ?? '') + `/partite/${id}`)
        this.partita = res.data.data?.partita || {}
      } catch (e) {
        console.error('Errore caricamento partita', e)
      }
    }
  },
  mounted() {
    this.fetchPartita()
  }
}
</script>

<style scoped>
.detail-view { width: 100%; }
.back-btn { color: #06b6d4 !important; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; transition: all 0.3s; }
.back-btn:hover { color: #7c3aed !important; transform: translateX(-4px); }
.detail-card { border-radius: 20px !important; max-width: 900px; margin: 0 auto; }
.detail-header { display: flex; justify-content: space-between; align-items: center; gap: 3rem; }
.flex-grow { flex: 1; }
.match-title { font-size: 2rem; font-weight: 700; color: #e2e8f0; display: flex; align-items: center; gap: 1rem; }
.vs { color: #7c3aed; font-size: 1.3rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }
.season-badge { background: linear-gradient(135deg, rgba(6, 182, 212, 0.2), rgba(34, 197, 94, 0.2)) !important; color: #7dd3fc !important; border: 1px solid rgba(6, 182, 212, 0.3) !important; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
.result-display { background: linear-gradient(135deg, rgba(124, 58, 237, 0.15), rgba(6, 182, 212, 0.15)); border: 1px solid rgba(124, 58, 237, 0.2); border-radius: 16px; padding: 1.5rem; min-width: 200px; text-align: center; }
.result-score { font-size: 3.5rem; font-weight: 700; background: linear-gradient(135deg, #7c3aed, #06b6d4); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
.dash { margin: 0 0.5rem; color: #a855f7; }
.detail-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; }
.detail-item { padding: 1.5rem; background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.1); border-radius: 12px; transition: all 0.3s; }
.detail-item:hover { background: rgba(6, 182, 212, 0.08); border-color: rgba(6, 182, 212, 0.2); }
.detail-label { color: #94a3b8; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 0.5rem 0; font-weight: 600; }
.detail-value { color: #e2e8f0; font-size: 1.1rem; font-weight: 600; margin: 0; }
</style>
