<template>
  <div class="data-view">
    <!-- Gestione stagioni con ricerca, dettagli e modifica rapida. -->
    <div class="view-header mb-6">
      <div class="header-content">
        <h2 class="gradient-text">Stagioni</h2>
        <p style="color: #94a3b8; margin-top: 0.5rem;">Archivio delle stagioni sportive e storico completato</p>
      </div>
      <div class="header-actions">
        <v-text-field
          v-model="q"
          label="Cerca stagioni..."
          prepend-inner-icon="mdi-magnify"
          class="search-field"
          variant="outlined"
          density="compact"
        />
        <new-stagione-dialog @saved="fetch" />
      </div>
    </div>

    <v-card class="card-glass">
      <v-table density="compact" class="data-table">
        <thead>
          <tr>
            <th style="color: #cbd5e1;">Id</th>
            <th style="color: #cbd5e1;">Nome</th>
            <th style="color: #cbd5e1;">Inizio</th>
            <th style="color: #cbd5e1;">Fine</th>
            <th class="actions-header" style="color: #cbd5e1;">Azioni</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="s in stagioniFiltrate" :key="s.id" class="data-row">
            <td>{{ s.id }}</td>
            <td class="font-weight-600">{{ s.nome_stagione }}</td>
            <td>{{ s.anno_inizio }}</td>
            <td>{{ s.anno_fine }}</td>
            <td class="actions-cell">
              <div class="actions-wrap">
                <details-dialog :title="s.nome_stagione" :fields="detailFields(s)" />
                <new-stagione-dialog :item="s" @saved="fetch" />
                <v-btn size="small" variant="text" color="error" icon="mdi-trash-can" @click="deleteStagione(s.id)" />
              </div>
            </td>
          </tr>
        </tbody>
      </v-table>
    </v-card>
  </div>
</template>

<script>
import axios from 'axios'
import NewStagioneDialog from '../components/NewStagioneDialog.vue'
import DetailsDialog from '../components/DetailsDialog.vue'

export default {
  name: 'StagioniView',
  components: { NewStagioneDialog, DetailsDialog },
  data() { return { stagioni: [], q: '' } },
  computed: {
    stagioniFiltrate() {
      const query = this.q.trim().toLowerCase()
      if (!query) return this.stagioni
      return this.stagioni.filter((s) => {
        return [s.id, s.nome_stagione, s.anno_inizio, s.anno_fine]
          .some((value) => String(value).toLowerCase().includes(query))
      })
    }
  },
  methods: {
    async fetch() {
      const res = await axios.get((import.meta.env.VITE_API_BASE ?? '') + '/stagioni')
      this.stagioni = res.data.data.stagioni || []
    },
    detailFields(s) {
      return [
        { label: 'ID', value: s.id },
        { label: 'Nome', value: s.nome_stagione },
        { label: 'Anno inizio', value: s.anno_inizio },
        { label: 'Anno fine', value: s.anno_fine },
      ]
    },
    async deleteStagione(id) {
      if (!confirm('Sei sicuro di voler eliminare questa stagione?')) return
      try {
        await axios.delete((import.meta.env.VITE_API_BASE ?? '') + `/stagioni/${id}`)
        await this.fetch()
      } catch (e) {
        alert('Errore eliminazione: ' + (e.response?.data?.message || e.message))
      }
    },
  },
  mounted() { this.fetch() }
}
</script>

<style scoped>
.data-view {
  width: 100%;
}

.view-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 2rem;
  flex-wrap: wrap;
}

.header-content h2 {
  font-size: 2rem;
  margin: 0;
}

.header-actions {
  display: flex;
  gap: 1rem;
  flex: 1;
  min-width: 300px;
}

.search-field {
  flex: 1;
  min-width: 250px;
}

.data-table {
  background: transparent !important;
  border-collapse: collapse !important;
}

.data-table thead tr {
  border-bottom: 2px solid rgba(148, 163, 184, 0.12) !important;
}

.data-table tbody tr {
  border-bottom: 1px solid rgba(148, 163, 184, 0.08) !important;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
  color: #e2e8f0;
}

.data-table tbody tr:hover {
  background: rgba(124, 58, 237, 0.1) !important;
}

.data-table td {
  color: #cbd5e1;
  padding: 0.75rem !important;
  vertical-align: middle;
}

.actions-cell {
  position: relative;
  padding: 0 !important;
  border: 0 !important;
  background: transparent !important;
  vertical-align: middle;
}

.actions-header {
  width: 1%;
  white-space: nowrap;
  text-align: left;
}

.actions-wrap {
  display: flex;
  gap: 0.25rem;
  justify-content: flex-start;
  align-items: center;
  position: relative;
  left: -0.25rem;
}

@media (max-width: 768px) {
  .view-header {
    flex-direction: column;
  }

  .header-actions {
    width: 100%;
    flex-direction: column;
  }

  .search-field {
    width: 100%;
  }
}
</style>
