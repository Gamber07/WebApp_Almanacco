<template>
  <div>
    <!-- Tabella admin dei contratti/ingaggi con modifica rapida. -->
    <h2>Gestione Mercato (Contratti/Ingaggi)</h2>
    <v-btn color="primary" class="mb-4" prepend-icon="mdi-plus" @click="insertContratto">Aggiungi</v-btn>

    <v-simple-table>
      <thead>
        <tr><th>ID</th><th>Giocatore</th><th>Squadra</th><th>Maglia</th><th>Inizio</th><th>Scadenza</th><th>Azioni</th></tr>
      </thead>
      <tbody>
        <tr v-for="c in contratti" :key="c.id">
          <td>{{ c.id }}</td>
          <td>{{ c.id_giocatore }}</td>
          <td>{{ c.id_squadra }}</td>
          <td>{{ c.numero_maglia }}</td>
          <td>{{ c.data_inizio }}</td>
          <td>{{ c.scadenza }}</td>
          <td>
            <v-btn size="small" variant="text" color="warning" icon="mdi-pencil" @click="editContratto(c)" />
          </td>
        </tr>
      </tbody>
    </v-simple-table>

    <new-contratto-dialog ref="dialogNew" :item="null" :no-activator="true" @saved="onSaved" />
    <new-contratto-dialog ref="dialogEdit" :item="editingContratto" :no-activator="true" @saved="onSaved" />
  </div>
</template>

<script>
import axios from 'axios'
import NewContrattoDialog from '../../components/NewContrattoDialog.vue'

export default {
  name: 'GestioneMercatoAdmin',
  components: { NewContrattoDialog },
  data() {
    return {
      contratti: [],
      editingContratto: null
    }
  },
  methods: {
    async fetch() {
      const res = await axios.get((import.meta.env.VITE_API_BASE ?? '') + '/contratti')
      this.contratti = res.data.data.contratti || []
    },
    insertContratto() {
      this.editingContratto = null
      this.$refs.dialogNew?.openDialog()
    },
    editContratto(c) {
      this.editingContratto = c
      this.$refs.dialogEdit?.openDialog()
    },
    async onSaved() {
      this.editingContratto = null
      await this.fetch()
    }
  },
  mounted() {
    this.fetch()
  }
}
</script>
