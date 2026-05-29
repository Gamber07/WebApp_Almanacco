<template>
  <div>
    <!-- Tabella admin dei giocatori con apertura del dialog di modifica. -->
    <h2>Gestione Giocatori</h2>
    <v-btn color="primary" class="mb-4" prepend-icon="mdi-plus" @click="insertGiocatore">Aggiungi</v-btn>

    <v-simple-table>
      <thead>
        <tr><th>ID</th><th>Nome</th><th>Cognome</th><th>Ruolo</th><th>Nazionalità</th><th>Azioni</th></tr>
      </thead>
      <tbody>
        <tr v-for="g in giocatori" :key="g.id">
          <td>{{ g.id }}</td>
          <td>{{ g.nome }}</td>
          <td>{{ g.cognome }}</td>
          <td>{{ g.ruolo }}</td>
          <td>{{ g.nazionalita }}</td>
          <td style="vertical-align: middle; white-space: nowrap;">
            <v-btn size="small" variant="text" color="warning" icon="mdi-pencil" @click="editGiocatore(g)" />
          </td>
        </tr>
      </tbody>
    </v-simple-table>

    <new-giocatore-dialog ref="dialogNew" :item="null" :no-activator="true" @saved="onSaved" />
    <new-giocatore-dialog ref="dialogEdit" :item="editingGiocatore" :no-activator="true" @saved="onSaved" />
  </div>
</template>

<script>
import axios from 'axios'
import NewGiocatoreDialog from '../../components/NewGiocatoreDialog.vue'

export default {
  name: 'GestioneGiocatoriAdmin',
  components: { NewGiocatoreDialog },
  data() {
    return {
      giocatori: [],
      editingGiocatore: null
    }
  },
  methods: {
    async fetch() {
      const res = await axios.get((import.meta.env.VITE_API_BASE ?? '') + '/giocatori')
      this.giocatori = res.data.data.giocatori || []
    },
    insertGiocatore() {
      this.editingGiocatore = null
      this.$refs.dialogNew?.openDialog()
    },
    editGiocatore(g) {
      this.editingGiocatore = g
      this.$refs.dialogEdit?.openDialog()
    },
    async onSaved() {
      this.editingGiocatore = null
      await this.fetch()
    }
  },
  mounted() {
    this.fetch()
  }
}
</script>