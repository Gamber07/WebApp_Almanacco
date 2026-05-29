<template>
  <div class="data-view">
    <!-- Tabella pubblica del calendario con ricerca, dettaglio e azioni admin. -->
    <!-- Header -->
    <div class="view-header mb-6">
      <div class="header-content">
        <h2 class="gradient-text">Calendario</h2>
        <p style="color: #94a3b8; margin-top: 0.5rem;">Partite, risultati e statistiche del campionato</p>
      </div>
      <div class="header-actions">
        <v-text-field 
          v-model="q" 
          label="Cerca partite..." 
          @input="fetch"
          prepend-inner-icon="mdi-magnify"
          class="search-field"
          variant="outlined"
          density="compact"
        />
        <new-partita-dialog v-if="auth.user?.ruolo === 'admin'" @saved="fetch" />
      </div>
    </div>

    <!-- Data Grid -->
    <v-card class="card-glass">
      <v-table density="compact" class="data-table">
        <thead>
          <tr>
            <th style="color: #cbd5e1;">Id</th>
            <th style="color: #cbd5e1;">Stagione</th>
            <th style="color: #cbd5e1;">Casa</th>
            <th style="color: #cbd5e1;">Trasferta</th>
            <th style="color: #cbd5e1;">Risultato</th>
            <th style="color: #cbd5e1;">Data</th>
            <th v-if="auth.user?.ruolo === 'admin'" class="actions-header" style="color: #cbd5e1;">Azioni</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="p in partite" :key="p.id" class="data-row" @click="goToDetail(p)">
            <td>{{ p.id }}</td>
            <td class="text-cyan-500 font-weight-600">{{ p.nome_stagione }}</td>
            <td class="font-weight-600">{{ p.casa }}</td>
            <td class="font-weight-600">{{ p.trasferta }}</td>
            <td class="result-cell">
              <v-chip size="small" class="result-chip">
                {{ p.gol_casa }} - {{ p.gol_trasferta }}
              </v-chip>
            </td>
            <td style="color: #cbd5e1;">{{ p.data_partita }}</td>
            <td v-if="auth.user?.ruolo === 'admin'" class="actions-cell" @click.stop>
              <div class="actions-wrap">
                <v-btn size="small" variant="text" color="warning" icon="mdi-pencil" @click="openEdit(p)" />
                <v-btn size="small" variant="text" color="error" icon="mdi-trash-can" @click="deletePartita(p.id)" />
              </div>
            </td>
          </tr>
        </tbody>
      </v-table>
    </v-card>

    <new-partita-dialog ref="dialogEdit" :item="editingPartita" :no-activator="true" @saved="fetch" />
  </div>
</template>

<script>
import axios from 'axios'
import NewPartitaDialog from '../components/NewPartitaDialog.vue'
import { useAuthStore } from '../stores/auth'
export default {
  name: 'PartiteView',
  components: { NewPartitaDialog },
  data() { return { partite: [], q: '', auth: useAuthStore(), editingPartita: null } },
  methods: {
    async fetch() {
      const res = await axios.get((import.meta.env.VITE_API_BASE ?? '') + '/partite', { params: { q: this.q } })
      this.partite = res.data.data.partite || []
    },
    openEdit(p) {
      this.editingPartita = p
      this.$refs.dialogEdit?.openDialog()
    },
    async deletePartita(id) {
      if (confirm('Sei sicuro di voler eliminare questa partita?')) {
        try {
          await axios.delete((import.meta.env.VITE_API_BASE ?? '') + `/partite/${id}`)
          await this.fetch()
        } catch (e) {
          alert('Errore eliminazione: ' + (e.response?.data?.message || e.message))
        }
      }
    },
    goToDetail(p) {
      this.$router.push(`/partite/${p.id}`)
    }
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
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
  color: #e2e8f0;
}

.data-table tbody tr:hover {
  background: rgba(124, 58, 237, 0.1) !important;
}

.result-chip {
  background: linear-gradient(135deg, rgba(6, 182, 212, 0.2), rgba(34, 197, 94, 0.2)) !important;
  color: #7dd3fc !important;
  border: 1px solid rgba(6, 182, 212, 0.3) !important;
}

.result-cell {
  color: #cbd5e1;
}

.text-cyan-500 {
  color: #06b6d4 !important;
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

.data-table td {
  color: #cbd5e1;
  padding: 0.75rem !important;
  vertical-align: middle;
}

.data-table td.actions-cell {
  padding-right: 0.25rem !important;
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
