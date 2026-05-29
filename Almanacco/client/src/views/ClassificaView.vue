<template>
  <div class="classifica-view">
    <!-- Vista classifica con filtri stagione/campionato e ranking in tempo reale. -->
    <!-- Header -->
    <div class="view-header mb-6">
      <div class="header-content">
        <h2 class="gradient-text">Classifiche</h2>
        <p style="color: #94a3b8; margin-top: 0.5rem;">Posizioni in tempo reale del campionato</p>
      </div>
      <div class="header-actions">
        <v-select :items="stagioni" item-title="nome_stagione" item-value="id" v-model="id_stagione" label="Stagione" @change="fetch" density="compact" variant="outlined" class="filter-select" />
        <v-select :items="campionati" item-title="nome" item-value="id" v-model="id_campionato" label="Campionato" @change="fetch" density="compact" variant="outlined" class="filter-select" />
      </div>
    </div>

    <!-- Ranking Table -->
    <v-card class="card-glass">
      <v-table density="compact" class="data-table">
        <thead>
          <tr>
            <th style="color: #cbd5e1; width: 60px;">#</th>
            <th style="color: #cbd5e1;">Squadra</th>
            <th style="color: #cbd5e1; text-align: right;">Punti</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(c, idx) in classifica" :key="c.nome" class="ranking-row">
            <td class="ranking-number" :class="{ 'top-3': idx < 3 }">
              {{ idx + 1 }}
            </td>
            <td class="team-name" style="font-weight: 600; color: #e2e8f0;">{{ c.nome }}</td>
            <td class="points-cell" style="text-align: right;">
              <span class="points-badge">{{ c.punti }}</span>
            </td>
          </tr>
        </tbody>
      </v-table>
    </v-card>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'ClassificaView',
  data() {
    return {
      stagioni: [],
      campionati: [],
      classifica: [],
      id_stagione: null,
      id_campionato: null
    }
  },
  methods: {
    async fetch() {
      // Ensure we send numeric ids or undefined
      const params = {}
      if (this.id_stagione) params.id_stagione = this.id_stagione
      if (this.id_campionato) params.id_campionato = this.id_campionato

      const res = await axios.get((import.meta.env.VITE_API_BASE ?? '') + '/classifica', { params })
      const data = res.data.data || {}
      this.stagioni = data.stagioni || []
      this.campionati = data.campionati || []
      this.classifica = data.classifica || []

      // If no selected filters, default to returned filters
      if (!this.id_stagione && data.filters?.id_stagione) this.id_stagione = data.filters.id_stagione
      if (!this.id_campionato && data.filters?.id_campionato) this.id_campionato = data.filters.id_campionato
    }
  },
  watch: {
    id_stagione() { this.fetch() },
    id_campionato() { this.fetch() }
  },
  mounted() {
    this.fetch()
  }
}
</script>

<style scoped>
.classifica-view {
  width: 100%;
}

.view-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 2rem;
  flex-wrap: wrap;
  margin-bottom: 2rem;
}

.header-content h2 {
  font-size: 2rem;
  margin: 0;
  font-weight: 700;
}

.header-actions {
  display: flex;
  gap: 1rem;
  flex: 1;
  min-width: 350px;
}

.filter-select {
  flex: 1;
  min-width: 150px;
}

.data-table {
  background: transparent !important;
  border-collapse: collapse !important;
}

.data-table thead tr {
  border-bottom: 2px solid rgba(148, 163, 184, 0.12) !important;
}

.ranking-row {
  border-bottom: 1px solid rgba(148, 163, 184, 0.08) !important;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
  color: #e2e8f0;
}

.ranking-row:hover {
  background: rgba(124, 58, 237, 0.1) !important;
}

.ranking-number {
  text-align: center;
  font-weight: 700;
  color: #94a3b8;
  font-size: 1rem;
  padding: 0.75rem 0.5rem !important;
  min-width: 50px;
}

.ranking-number.top-3 {
  color: #fbbf24;
  font-weight: 700;
}

.team-name {
  color: #e2e8f0;
  padding: 0.75rem !important;
  font-weight: 600;
}

.points-cell {
  padding: 0.75rem !important;
}

.points-badge {
  background: linear-gradient(135deg, rgba(6, 182, 212, 0.2), rgba(34, 197, 94, 0.2));
  color: #7dd3fc;
  padding: 0.5rem 1rem;
  border-radius: 8px;
  font-weight: 700;
  border: 1px solid rgba(6, 182, 212, 0.3);
  display: inline-block;
}

.data-table td {
  color: #cbd5e1;
}

@media (max-width: 768px) {
  .view-header {
    flex-direction: column;
  }
  
  .header-actions {
    width: 100%;
    flex-direction: column;
  }
  
  .filter-select {
    width: 100%;
  }
}
</style>
