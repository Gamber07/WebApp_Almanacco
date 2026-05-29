<template>
  <v-dialog v-model="open" max-width="600">
    <!-- Dialog riutilizzabile per creare o modificare un contratto. -->
    <template v-if="!noActivator" #activator="{ props }">
      <v-btn v-if="isAdmin" v-bind="props" color="primary" variant="elevated">
        {{ isEditMode ? 'Modifica Contratto' : 'Nuovo Contratto' }}
      </v-btn>
    </template>

    <v-card>
      <v-card-title>{{ isEditMode ? 'Modifica Contratto' : 'Nuovo Contratto' }}</v-card-title>
      <v-card-text>
        <v-text-field v-model.number="id_giocatore" label="Id Giocatore" />
        <v-text-field v-model.number="id_squadra" label="Id Squadra" />
        <v-text-field v-model="data_inizio" label="Data inizio (YYYY-MM-DD)" />
        <v-text-field v-model="scadenza" label="Scadenza (YYYY-MM-DD)" />
        <v-text-field v-model.number="numero_maglia" label="Numero maglia" type="number" min="0" />
        <v-text-field v-model="tipo_contratto" label="Tipo contratto" />
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
      id_giocatore: 0,
      id_squadra: 0,
      data_inizio: '',
      scadenza: '',
      numero_maglia: 0,
      tipo_contratto: 'Definitivo',
      auth: useAuthStore(),
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
        this.id_giocatore = this.item.id_giocatore || 0
        this.id_squadra = this.item.id_squadra || 0
        this.data_inizio = this.item.data_inizio || ''
        this.scadenza = this.item.scadenza || ''
        this.numero_maglia = this.item.numero_maglia || 0
        this.tipo_contratto = this.item.tipo_contratto || 'Definitivo'
      } else {
        this.id_giocatore = 0
        this.id_squadra = 0
        this.data_inizio = ''
        this.scadenza = ''
        this.numero_maglia = 0
        this.tipo_contratto = 'Definitivo'
      }
    },
  },
  methods: {
    openDialog() {
      if (this.item) {
        this.id_giocatore = this.item.id_giocatore || 0
        this.id_squadra = this.item.id_squadra || 0
        this.data_inizio = this.item.data_inizio || ''
        this.scadenza = this.item.scadenza || ''
        this.numero_maglia = this.item.numero_maglia || 0
        this.tipo_contratto = this.item.tipo_contratto || 'Definitivo'
      } else {
        this.id_giocatore = 0
        this.id_squadra = 0
        this.data_inizio = ''
        this.scadenza = ''
        this.numero_maglia = 0
        this.tipo_contratto = 'Definitivo'
      }
      this.open = true
    },
    async save() {
      if (!this.auth.token) { alert('Autenticazione richiesta'); return }
      try {
        const payload = {
          id_giocatore: this.id_giocatore,
          id_squadra: this.id_squadra,
          data_inizio: this.data_inizio,
          scadenza: this.scadenza,
          numero_maglia: this.numero_maglia,
          tipo_contratto: this.tipo_contratto,
        }
        const url = (import.meta.env.VITE_API_BASE ?? '') + '/contratti' + (this.isEditMode ? `/${this.item.id}` : '')
        const res = this.isEditMode ? await axios.put(url, payload) : await axios.post(url, payload)
        this.$emit('saved', res.data.data.contratto)
        this.open = false
      } catch (e) { alert('Errore salvataggio') }
    }
  },
}
</script>
