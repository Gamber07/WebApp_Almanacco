<template>
  <div class="data-view">
    <!-- Tabella pubblica dei giocatori con ricerca, dettaglio e azioni admin. -->
    <!-- Header with Search and Add Button -->
    <div class="view-header mb-6">
      <div class="header-content">
        <h2 class="gradient-text">Giocatori</h2>
        <p style="color: #94a3b8; margin-top: 0.5rem;">Gestisci e visualizza tutti i giocatori del campionato</p>
      </div>
      <div class="header-actions">
        <v-text-field 
          v-model="q" 
          label="Cerca giocatori..." 
          @input="fetch"
          prepend-inner-icon="mdi-magnify"
          class="search-field"
          variant="outlined"
          density="compact"
        />
        <new-giocatore-dialog @saved="fetch" />
      </div>
    </div>

    <!-- Data Grid -->
    <v-card class="card-glass">
      <v-table density="compact" class="data-table">
        <thead>
          <tr>
            <th style="color: #cbd5e1;">Id</th>
            <th style="color: #cbd5e1;">Nome</th>
            <th style="color: #cbd5e1;">Cognome</th>
            <th style="color: #cbd5e1;">Ruolo</th>
            <th style="color: #cbd5e1;">Squadra</th>
            <th v-if="auth.user?.ruolo === 'admin'" class="actions-header" style="color: #cbd5e1;">Azioni</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="g in giocatori" :key="g.id" class="data-row" @click="goToDetail(g)">
            <td>{{ g.id }}</td>
            <td class="font-weight-600">{{ g.nome }}</td>
            <td>{{ g.cognome }}</td>
            <td><v-chip size="small" class="role-chip">{{ g.ruolo }}</v-chip></td>
            <td class="text-cyan-500">{{ g.nome_squadra }}</td>
            <td v-if="auth.user?.ruolo === 'admin'" class="actions-cell" @click.stop>
              <div class="actions-wrap">
                <v-btn size="small" variant="text" color="warning" icon="mdi-pencil" @click="openEdit(g)" />
                <v-btn size="small" variant="text" color="error" icon="mdi-trash-can" @click="deleteGiocatore(g.id)" />
              </div>
            </td>
          </tr>
        </tbody>
      </v-table>
    </v-card>

    <new-giocatore-dialog ref="dialogEdit" :item="editingGiocatore" :no-activator="true" @saved="fetch" />
  </div>
</template>

<script>
import axios from 'axios'
import NewGiocatoreDialog from '../components/NewGiocatoreDialog.vue'
import { useAuthStore } from '../stores/auth'

export default {
  name: 'GiocatoriView',
  components: { NewGiocatoreDialog },
  data() { return { giocatori: [], q: '', auth: useAuthStore(), editingGiocatore: null } },
  methods: {
    async fetch() {
      const res = await axios.get((import.meta.env.VITE_API_BASE ?? '') + '/giocatori', { params: { q: this.q } })
      this.giocatori = res.data.data.giocatori || []
    },
    openEdit(g) {
      this.editingGiocatore = g
      this.$refs.dialogEdit?.openDialog()
    },
    async deleteGiocatore(id) {
      if (confirm('Sei sicuro di voler eliminare questo giocatore?')) {
        try {
          await axios.delete((import.meta.env.VITE_API_BASE ?? '') + `/giocatori/${id}`)
          await this.fetch()
        } catch (e) {
          alert('Errore eliminazione: ' + (e.response?.data?.message || e.message))
        }
      }
    },
    goToDetail(g) {
      this.$router.push(`/giocatori/${g.id}`)
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

.role-chip {
  background: linear-gradient(135deg, rgba(124, 58, 237, 0.2), rgba(168, 85, 247, 0.2)) !important;
  color: #c4b5fd !important;
  border: 1px solid rgba(168, 85, 247, 0.3) !important;
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
