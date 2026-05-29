<template>
  <div>
    <!-- Gestione stagioni con ricerca, dettagli e modifica rapida. -->
    <v-row class="mb-4">
      <v-col cols="12" md="6">
        <v-text-field v-model="q" label="Cerca stagioni" @input="fetch" />
      </v-col>
      <v-col cols="12" md="6" class="text-right">
        <new-stagione-dialog @saved="fetch" />
      </v-col>
    </v-row>

    <v-simple-table>
      <thead>
        <tr><th>Id</th><th>Nome</th><th>Inizio</th><th>Fine</th><th>Azioni</th></tr>
      </thead>
      <tbody>
        <tr v-for="s in stagioni" :key="s.id">
          <td>{{ s.id }}</td>
          <td>{{ s.nome_stagione }}</td>
          <td>{{ s.anno_inizio }}</td>
          <td>{{ s.anno_fine }}</td>
          <td class="d-flex ga-2">
            <details-dialog :title="s.nome_stagione" :fields="detailFields(s)" />
            <new-stagione-dialog :item="s" @saved="fetch" />
          </td>
        </tr>
      </tbody>
    </v-simple-table>
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
  },
  mounted() { this.fetch() }
}
</script>
