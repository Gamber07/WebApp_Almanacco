<template>
  <div class="data-view">
    <!-- Tabella admin dei campionati con CRUD e cancellazione logica. -->
    <div class="view-header mb-6">
      <div class="header-content">
        <h2 class="gradient-text">Campionati</h2>
        <p style="color: #94a3b8; margin-top: 0.5rem;">Gestione dei campionati disponibili per partite e classifiche</p>
      </div>
      <div class="header-actions">
        <v-text-field
          v-model="q"
          label="Cerca campionati..."
          prepend-inner-icon="mdi-magnify"
          class="search-field"
          variant="outlined"
          density="compact"
        />
        <new-campionato-dialog @saved="fetch" />
      </div>
    </div>

    <v-card class="card-glass">
      <v-table density="compact" class="data-table">
        <thead>
          <tr>
            <th style="color: #cbd5e1;">Id</th>
            <th style="color: #cbd5e1;">Nome</th>
            <th class="actions-header" style="color: #cbd5e1;">Azioni</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="c in campionatiFiltrati" :key="c.id" class="data-row">
            <td>{{ c.id }}</td>
            <td class="font-weight-600">{{ c.nome }}</td>
            <td class="actions-cell">
              <div class="actions-wrap">
                <details-dialog :title="c.nome" :fields="detailFields(c)" />
                <new-campionato-dialog :item="c" @saved="fetch" />
                <v-btn size="small" variant="text" color="error" icon="mdi-trash-can" @click="deleteCampionato(c.id)" />
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
import NewCampionatoDialog from '../../components/NewCampionatoDialog.vue'
import DetailsDialog from '../../components/DetailsDialog.vue'

export default {
  name: 'GestioneCampionatiAdmin',
  components: { NewCampionatoDialog, DetailsDialog },
  data() {
    return {
      campionati: [],
      q: '',
    }
  },
  computed: {
    campionatiFiltrati() {
      const query = this.q.trim().toLowerCase()
      if (!query) return this.campionati
      return this.campionati.filter((c) => {
        return [c.id, c.nome].some((value) => String(value).toLowerCase().includes(query))
      })
    },
  },
  methods: {
    async fetch() {
      const res = await axios.get((import.meta.env.VITE_API_BASE ?? '') + '/campionati')
      this.campionati = res.data.data.campionati || []
    },
    detailFields(c) {
      return [
        { label: 'ID', value: c.id },
        { label: 'Nome', value: c.nome },
      ]
    },
    async deleteCampionato(id) {
      if (!confirm('Sei sicuro di voler eliminare questo campionato?')) return
      try {
        await axios.delete((import.meta.env.VITE_API_BASE ?? '') + `/campionati/${id}`)
        await this.fetch()
      } catch (e) {
        alert('Errore eliminazione: ' + (e.response?.data?.message || e.message))
      }
    },
  },
  mounted() {
    this.fetch()
  },
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
