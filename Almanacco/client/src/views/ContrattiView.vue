<template>
  <div class="data-view">
    <!-- Elenco contratti con modali di dettaglio e modifica. -->
    <div class="view-header mb-6">
      <div class="header-content">
        <h2 class="gradient-text">Contratti</h2>
        <p style="color: #94a3b8; margin-top: 0.5rem;">Gestione degli ingaggi e dei contratti attivi</p>
      </div>
      <div class="header-actions">
        <v-text-field
          v-model="q"
          label="Cerca contratti..."
          prepend-inner-icon="mdi-magnify"
          class="search-field"
          variant="outlined"
          density="compact"
        />
        <new-contratto-dialog @saved="fetch" />
      </div>
    </div>

    <v-card class="card-glass">
      <v-table density="compact" class="data-table">
        <thead>
          <tr>
            <th style="color: #cbd5e1;">Id</th>
            <th style="color: #cbd5e1;">Giocatore</th>
            <th style="color: #cbd5e1;">Squadra</th>
            <th style="color: #cbd5e1;">Scadenza</th>
            <th style="color: #cbd5e1;">Maglia</th>
            <th class="actions-header" style="color: #cbd5e1;">Azioni</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="c in contrattiFiltrati" :key="c.id" class="data-row">
            <td>{{ c.id }}</td>
            <td class="font-weight-600">{{ c.atleta }}</td>
            <td>{{ c.squadra }}</td>
            <td>
              <v-chip size="small" variant="tonal" color="secondary" class="contract-chip">
                {{ c.scadenza }}
              </v-chip>
            </td>
            <td>{{ c.numero_maglia }}</td>
            <td class="actions-cell">
              <div class="actions-wrap">
                <details-dialog :title="'Contratto #' + c.id" :fields="detailFields(c)" />
                <new-contratto-dialog :item="c" @saved="fetch" />
                <v-btn size="small" variant="text" color="error" icon="mdi-trash-can" @click="deleteContratto(c.id)" />
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
import NewContrattoDialog from '../components/NewContrattoDialog.vue'
import DetailsDialog from '../components/DetailsDialog.vue'
export default {
  name: 'ContrattiView',
  components: { NewContrattoDialog, DetailsDialog },
  data() { return { contratti: [], q: '' } },
  computed: {
    contrattiFiltrati() {
      const query = this.q.trim().toLowerCase()
      if (!query) return this.contratti
      return this.contratti.filter((c) => {
        return [c.id, c.atleta, c.squadra, c.scadenza, c.numero_maglia, c.tipo_contratto]
          .some((value) => String(value).toLowerCase().includes(query))
      })
    }
  },
  methods: {
    async fetch() {
      const res = await axios.get((import.meta.env.VITE_API_BASE ?? '') + '/contratti')
      this.contratti = res.data.data.contratti || []
    },
    detailFields(c) {
      return [
        { label: 'ID', value: c.id },
        { label: 'Giocatore', value: c.atleta },
        { label: 'Squadra', value: c.squadra },
        { label: 'Data inizio', value: c.data_inizio },
        { label: 'Scadenza', value: c.scadenza },
        { label: 'Numero maglia', value: c.numero_maglia },
        { label: 'Tipo contratto', value: c.tipo_contratto },
      ]
    },
    async deleteContratto(id) {
      if (!confirm('Sei sicuro di voler eliminare questo contratto?')) return
      try {
        await axios.delete((import.meta.env.VITE_API_BASE ?? '') + `/contratti/${id}`)
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

.contract-chip {
  letter-spacing: 0.2px;
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
