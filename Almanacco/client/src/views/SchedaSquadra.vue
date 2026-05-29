<template>
  <div class="detail-view">
    <!-- Scheda di dettaglio della squadra con riepilogo e attributi principali. -->
    <v-btn text @click="$router.back" class="back-btn mb-6">
      <v-icon left>mdi-arrow-left</v-icon>Torna indietro
    </v-btn>
    
    <v-card v-if="squadra" class="card-glass detail-card glow-purple">
      <v-card-text class="pa-8">
        <div class="detail-header">
          <div>
            <h1 class="gradient-text mb-2">{{ squadra.nome }}</h1>
            <p class="subtitle">Dettagli della squadra</p>
          </div>
          <v-icon size="80" class="icon-accent">mdi-shield-account</v-icon>
        </div>
        
        <v-divider class="my-6" />
        
        <div class="detail-grid">
          <div class="detail-item">
            <p class="detail-label">Città</p>
            <p class="detail-value">{{ squadra.citta }}</p>
          </div>
          <div class="detail-item">
            <p class="detail-label">Stadio</p>
            <p class="detail-value">{{ squadra.stadio }}</p>
          </div>
          <div class="detail-item">
            <p class="detail-label">Anno fondazione</p>
            <p class="detail-value">{{ squadra.anno_fondazione }}</p>
          </div>
        </div>
      </v-card-text>
    </v-card>
  </div>
</template>

<script>
import axios from 'axios'
import { useAuthStore } from '../stores/auth'
import NewSquadraDialog from '../components/NewSquadraDialog.vue'

export default {
  name: 'SchedaSquadra',
  components: { },
  data() {
    return {
      squadra: null,
      auth: useAuthStore()
    }
  },
  methods: {
    async fetchSquadra() {
      const id = this.$route.params.id
      try {
        const res = await axios.get((import.meta.env.VITE_API_BASE ?? '') + `/squadre/${id}`)
        this.squadra = res.data.data?.squadra || {}
      } catch (e) {
        console.error('Errore caricamento squadra', e)
      }
    }
  },
  mounted() {
    this.fetchSquadra()
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
.team-title { font-size: 2rem; font-weight: 700; color: #e2e8f0; display: flex; align-items: center; gap: 1rem; }
.team-icon { width: 60px; height: 60px; border: 2px solid #7c3aed; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: rgba(124, 58, 237, 0.1); }
.stats-summary { background: linear-gradient(135deg, rgba(124, 58, 237, 0.15), rgba(6, 182, 212, 0.15)); border: 1px solid rgba(124, 58, 237, 0.2); border-radius: 16px; padding: 1.5rem; display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; }
.stat-item { text-align: center; }
.stat-value { font-size: 2.2rem; font-weight: 700; background: linear-gradient(135deg, #7c3aed, #06b6d4); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
.stat-label { color: #94a3b8; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 0.5rem; font-weight: 600; }
.detail-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; }
.detail-item { padding: 1.5rem; background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.1); border-radius: 12px; transition: all 0.3s; }
.detail-item:hover { background: rgba(6, 182, 212, 0.08); border-color: rgba(6, 182, 212, 0.2); }
.detail-label { color: #94a3b8; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 0.5rem 0; font-weight: 600; }
.detail-value { color: #e2e8f0; font-size: 1.1rem; font-weight: 600; margin: 0; }
.players-section { margin-top: 3rem; }
.section-title { font-size: 1.3rem; font-weight: 700; color: #e2e8f0; margin-bottom: 1.5rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid rgba(124, 58, 237, 0.3); padding-bottom: 1rem; }
.players-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 1.5rem; }
.player-card { padding: 1.2rem; background: rgba(124, 58, 237, 0.05); border: 1px solid rgba(124, 58, 237, 0.1); border-radius: 12px; text-align: center; transition: all 0.3s; }
.player-card:hover { background: rgba(124, 58, 237, 0.1); border-color: rgba(124, 58, 237, 0.3); transform: translateY(-2px); }
.player-name { color: #e2e8f0; font-weight: 600; margin-bottom: 0.5rem; }
.player-role { color: #7dd3fc; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }
</style>
