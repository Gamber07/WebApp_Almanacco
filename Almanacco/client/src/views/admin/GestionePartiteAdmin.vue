<template>
  <div>
    <!-- Tabella admin delle partite con inserimento e modifica rapida. -->
    <h2>Gestione Partite</h2>
    <v-btn color="primary" class="mb-4" prepend-icon="mdi-plus" @click="insertPartita">Aggiungi</v-btn>

    <v-simple-table>
      <thead>
        <tr><th>Id</th><th>Stagione</th><th>Casa</th><th>Trasferta</th><th>Risultato</th><th>Azioni</th></tr>
      </thead>
      <tbody>
        <tr v-for="p in partite" :key="p.id">
          <td>{{ p.id }}</td>
          <td>{{ p.nome_stagione }}</td>
          <td>{{ p.casa }}</td>
          <td>{{ p.trasferta }}</td>
          <td>{{ p.gol_casa }} - {{ p.gol_trasferta }}</td>
          <td style="vertical-align: middle; white-space: nowrap;">
            <v-btn size="small" variant="text" color="warning" icon="mdi-pencil" @click="editPartita(p)" />
          </td>
        </tr>
      </tbody>
    </v-simple-table>

    <new-partita-dialog ref="dialogNew" :item="null" :no-activator="true" @saved="onSaved" />
    <new-partita-dialog ref="dialogEdit" :item="editingPartita" :no-activator="true" @saved="onSaved" />
  </div>
</template>

<script>
import axios from 'axios'
import NewPartitaDialog from '../../components/NewPartitaDialog.vue'

export default {
  name: 'GestionePartiteAdmin',
  components: { NewPartitaDialog },
  data() {
    return {
      partite: [],
      showNew: false,
      editingPartita: null
    }
  },
  methods: {
    async fetch() {
      const res = await axios.get((import.meta.env.VITE_API_BASE ?? '') + '/partite')
      this.partite = res.data.data.partite || []
    },
    insertPartita() {
      this.editingPartita = null
      this.$refs.dialogNew?.openDialog()
    },
    editPartita(p) {
      this.editingPartita = p
      this.$refs.dialogEdit?.openDialog()
    },
    async onSaved() {
      this.editingPartita = null
      await this.fetch()
    }
  },
  mounted() {
    this.fetch()
  }
}
</script>