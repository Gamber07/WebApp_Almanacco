<template>
  <div>
    <!-- Tabella admin delle stagioni con dialog di editing. -->
    <h2>Gestione Stagioni</h2>
    <v-btn color="primary" class="mb-4" prepend-icon="mdi-plus" @click="insertStagione">Aggiungi</v-btn>

    <v-simple-table>
      <thead>
        <tr><th>ID</th><th>Nome</th><th>Anno Inizio</th><th>Anno Fine</th><th>Azioni</th></tr>
      </thead>
      <tbody>
        <tr v-for="s in stagioni" :key="s.id">
          <td>{{ s.id }}</td>
          <td>{{ s.nome_stagione }}</td>
          <td>{{ s.anno_inizio }}</td>
          <td>{{ s.anno_fine }}</td>
          <td style="vertical-align: middle; white-space: nowrap;">
            <v-btn size="small" variant="text" color="warning" icon="mdi-pencil" @click="editStagione(s)" />
          </td>
        </tr>
      </tbody>
    </v-simple-table>

    <new-stagione-dialog ref="dialogNew" :item="null" :no-activator="true" @saved="onSaved" />
    <new-stagione-dialog ref="dialogEdit" :item="editingStagione" :no-activator="true" @saved="onSaved" />
  </div>
</template>

<script>
import axios from 'axios'
import NewStagioneDialog from '../../components/NewStagioneDialog.vue'

export default {
  name: 'GestioneStagioniAdmin',
  components: { NewStagioneDialog },
  data() {
    return {
      stagioni: [],
      showNew: false,
      editingStagione: null
    }
  },
  methods: {
    async fetch() {
      const res = await axios.get((import.meta.env.VITE_API_BASE ?? '') + '/stagioni')
      this.stagioni = res.data.data.stagioni || []
    },
    insertStagione() {
      this.editingStagione = null
      this.$refs.dialogNew?.openDialog()
    },
    editStagione(s) {
      this.editingStagione = s
      this.$refs.dialogEdit?.openDialog()
    },
    async onSaved() {
      this.editingStagione = null
      await this.fetch()
    }
  },
  mounted() {
    this.fetch()
  }
}
</script>