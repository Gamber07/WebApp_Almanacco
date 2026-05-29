<template>
  <v-dialog v-model="open" max-width="700">
    <!-- Dialog riutilizzabile per creare o modificare una partita. -->
    <template v-if="!noActivator" #activator="{ props }">
      <v-btn v-if="isAdmin" v-bind="props" color="primary" variant="elevated">
        {{ isEditMode ? 'Modifica Partita' : 'Nuova Partita' }}
      </v-btn>
    </template>

    <v-card>
      <v-card-title>{{ isEditMode ? 'Modifica Partita' : 'Nuova Partita' }}</v-card-title>
      <v-card-text>
        <v-select :items="stagioni" item-title="nome_stagione" item-value="id" v-model="id_stagione" label="Stagione" />
        <v-select :items="campionati" item-title="nome" item-value="id" v-model="id_campionato" label="Campionato" />
        <v-select :items="squadre" item-title="nome" item-value="id" v-model="id_casa" label="Squadra Casa" />
        <v-select :items="squadre" item-title="nome" item-value="id" v-model="id_trasferta" label="Squadra Trasferta" />
        <v-text-field v-model.number="gol_casa" label="Gol Casa" type="number" min="0" />
        <v-text-field v-model.number="gol_trasferta" label="Gol Trasferta" type="number" min="0" />
        <v-text-field v-model="data" label="Data (YYYY-MM-DD)" />
        <v-text-field v-model="sede" label="Sede" />
      </v-card-text>
      <v-card-actions>
        <v-spacer />
        <v-btn text @click="open=false">Annulla</v-btn>
        <v-btn color="primary" @click="save">Salva</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import axios from 'axios'
import { useAuthStore } from '../stores/auth'

export default {
  props: {
    item: {
      type: Object,
      default: null,
    },
    noActivator: {
      type: Boolean,
      default: false,
    },
  },
  emits: ['saved'],
  data() {
    return {
      open: false,
      id_stagione: 1,
      id_campionato: 1,
      id_casa: 1,
      id_trasferta: 2,
      gol_casa: 0,
      gol_trasferta: 0,
      data: '',
      sede: '',
      auth: useAuthStore(),
      stagioni: [],
      squadre: [],
      campionati: [],
    }
  },
  computed: {
    isEditMode() {
      return this.item !== null
    },
    isAdmin() {
      return this.auth.user?.ruolo === 'admin'
    },
  },
  watch: {
    item() {
      if (this.item) {
        this.id_stagione = this.item.id_stagione || 1
        this.id_campionato = this.item.id_campionato || 1
        this.id_casa = this.item.id_squadra_casa || 1
        this.id_trasferta = this.item.id_squadra_trasferta || 2
        this.gol_casa = this.item.gol_casa || 0
        this.gol_trasferta = this.item.gol_trasferta || 0
        this.data = this.item.data_partita || ''
        this.sede = this.item.sede || ''
      } else {
        this.id_stagione = 1
        this.id_campionato = 1
        this.id_casa = 1
        this.id_trasferta = 2
        this.gol_casa = 0
        this.gol_trasferta = 0
        this.data = ''
        this.sede = ''
      }
    },
  },
  methods: {
    async fetchData() {
      try {
        const [stRes, sqRes, cpRes] = await Promise.all([
          axios.get((import.meta.env.VITE_API_BASE ?? '') + '/stagioni'),
          axios.get((import.meta.env.VITE_API_BASE ?? '') + '/squadre'),
          axios.get((import.meta.env.VITE_API_BASE ?? '') + '/campionati')
        ])
        this.stagioni = stRes.data.data.stagioni || []
        this.squadre = sqRes.data.data.squadre || []
        this.campionati = cpRes.data.data.campionati || []
      } catch (e) {
        console.error('Errore caricamento dati', e)
      }
    },
    openDialog() {
      if (this.item) {
        this.id_stagione = this.item.id_stagione || 1
        this.id_campionato = this.item.id_campionato || 1
        this.id_casa = this.item.id_squadra_casa || 1
        this.id_trasferta = this.item.id_squadra_trasferta || 2
        this.gol_casa = this.item.gol_casa || 0
        this.gol_trasferta = this.item.gol_trasferta || 0
        this.data = this.item.data_partita || ''
        this.sede = this.item.sede || ''
      } else {
        this.id_stagione = 1
        this.id_campionato = 1
        this.id_casa = 1
        this.id_trasferta = 2
        this.gol_casa = 0
        this.gol_trasferta = 0
        this.data = ''
        this.sede = ''
      }
      this.open = true
    },
    async save() {
      if (!this.auth.token) { alert('Autenticazione richiesta'); return }
      try {
        const payload = {
          id_stagione: this.id_stagione,
          id_campionato: this.id_campionato,
          id_casa: this.id_casa,
          id_trasferta: this.id_trasferta,
          gol_casa: this.gol_casa,
          gol_trasferta: this.gol_trasferta,
          data: this.data,
          sede: this.sede,
        }
        const url = (import.meta.env.VITE_API_BASE ?? '') + '/partite' + (this.isEditMode ? `/${this.item.id}` : '')
        const res = this.isEditMode ? await axios.put(url, payload) : await axios.post(url, payload)
        this.$emit('saved', res.data.data.partita)
        this.open = false
      } catch (e) { alert('Errore salvataggio: ' + (e.response?.data?.message || e.message)) }
    }
  },
  mounted() {
    this.fetchData()
  },
}
</script>
