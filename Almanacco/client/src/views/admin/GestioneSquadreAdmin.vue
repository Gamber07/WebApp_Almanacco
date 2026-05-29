<template>
  <div>
    <!-- Tabella admin delle squadre con dialog di creazione e modifica. -->
    <h2>Gestione Squadre</h2>
    <v-btn color="primary" class="mb-4" prepend-icon="mdi-plus" @click="insertSquadra">Aggiungi</v-btn>

    <v-simple-table>
      <thead>
        <tr><th>ID</th><th>Nome</th><th>Città</th><th>Stadio</th><th>Anno Fondazione</th><th>Azioni</th></tr>
      </thead>
      <tbody>
        <tr v-for="s in squadre" :key="s.id">
          <td>{{ s.id }}</td>
          <td>{{ s.nome }}</td>
          <td>{{ s.citta }}</td>
          <td>{{ s.stadio }}</td>
          <td>{{ s.anno_fondazione }}</td>
          <td style="vertical-align: middle; white-space: nowrap;">
            <v-btn size="small" variant="text" color="warning" icon="mdi-pencil" @click="editSquadra(s)" />
          </td>
        </tr>
      </tbody>
    </v-simple-table>

    <new-squadra-dialog ref="dialogNew" :item="null" :no-activator="true" @saved="onSaved" />
    <new-squadra-dialog ref="dialogEdit" :item="editingSquadra" :no-activator="true" @saved="onSaved" />
  </div>
</template>

<script>
import axios from 'axios'
import NewSquadraDialog from '../../components/NewSquadraDialog.vue'

export default {
  name: 'GestioneSquadreAdmin',
  components: { NewSquadraDialog },
  data() {
    return {
      squadre: [],
      editingSquadra: null
    }
  },
  methods: {
    async fetch() {
      const res = await axios.get((import.meta.env.VITE_API_BASE ?? '') + '/squadre')
      this.squadre = res.data.data.squadre || []
    },
    insertSquadra() {
      this.editingSquadra = null
      this.$refs.dialogNew?.openDialog()
    },
    editSquadra(s) {
      this.editingSquadra = s
      this.$refs.dialogEdit?.openDialog()
    },
    async onSaved() {
      this.editingSquadra = null
      await this.fetch()
    }
  },
  mounted() {
    this.fetch()
  }
}
</script>