<template>
  <div>
    <!-- Elenco contratti con modali di dettaglio e modifica. -->
    <v-row class="mb-4">
      <v-col cols="12" class="text-right">
        <new-contratto-dialog @saved="fetch" />
      </v-col>
    </v-row>

    <v-simple-table>
      <thead>
        <tr><th>Id</th><th>Giocatore</th><th>Squadra</th><th>Scadenza</th><th>Maglia</th><th>Azioni</th></tr>
      </thead>
      <tbody>
        <tr v-for="c in contratti" :key="c.id">
          <td>{{ c.id }}</td>
          <td>{{ c.atleta }}</td>
          <td>{{ c.squadra }}</td>
          <td>{{ c.scadenza }}</td>
          <td>{{ c.numero_maglia }}</td>
          <td class="d-flex ga-2">
            <details-dialog :title="'Contratto #' + c.id" :fields="detailFields(c)" />
            <new-contratto-dialog :item="c" @saved="fetch" />
          </td>
        </tr>
      </tbody>
    </v-simple-table>
  </div>
</template>

<script>
import axios from 'axios'
import NewContrattoDialog from '../components/NewContrattoDialog.vue'
import DetailsDialog from '../components/DetailsDialog.vue'
export default {
  name: 'ContrattiView',
  components: { NewContrattoDialog, DetailsDialog },
  data() { return { contratti: [] } },
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
  },
  mounted() { this.fetch() }
}
</script>
