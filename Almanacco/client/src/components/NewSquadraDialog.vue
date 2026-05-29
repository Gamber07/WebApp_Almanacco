<template>
  <v-dialog v-model="open" max-width="600">
    <!-- Dialog riutilizzabile per creare o modificare una squadra. -->
    <template v-if="!noActivator" #activator="{ props }">
      <v-btn v-if="isAdmin" v-bind="props" color="primary" variant="elevated">
        {{ isEditMode ? 'Modifica Squadra' : 'Nuova Squadra' }}
      </v-btn>
    </template>

    <v-card>
      <v-card-title>{{ isEditMode ? 'Modifica Squadra' : 'Nuova Squadra' }}</v-card-title>
      <v-card-text>
        <v-text-field v-model="nome" label="Nome" />
        <v-text-field v-model="citta" label="Città" />
        <v-text-field v-model="stadio" label="Stadio" />
        <v-text-field v-model.number="anno_fondazione" label="Anno fondazione" type="number" />
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
      nome: '',
      citta: '',
      stadio: '',
      anno_fondazione: new Date().getFullYear(),
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
        this.nome = this.item.nome || ''
        this.citta = this.item.citta || ''
        this.stadio = this.item.stadio || ''
        this.anno_fondazione = this.item.anno_fondazione || new Date().getFullYear()
      } else {
        this.nome = ''
        this.citta = ''
        this.stadio = ''
        this.anno_fondazione = new Date().getFullYear()
      }
    },
  },
  methods: {
    openDialog() {
      if (this.item) {
        this.nome = this.item.nome || ''
        this.citta = this.item.citta || ''
        this.stadio = this.item.stadio || ''
        this.anno_fondazione = this.item.anno_fondazione || new Date().getFullYear()
      } else {
        this.nome = ''
        this.citta = ''
        this.stadio = ''
        this.anno_fondazione = new Date().getFullYear()
      }
      this.open = true
    },
    async save() {
      if (!this.auth.token) { alert('Autenticazione richiesta'); return }
      try {
        const payload = {
          nome: this.nome,
          citta: this.citta,
          stadio: this.stadio,
          anno_fondazione: this.anno_fondazione,
        }
        const url = (import.meta.env.VITE_API_BASE ?? '') + '/squadre' + (this.isEditMode ? `/${this.item.id}` : '')
        const res = this.isEditMode ? await axios.put(url, payload) : await axios.post(url, payload)
        this.$emit('saved', res.data.data.squadra)
        this.open = false
      } catch (e) { alert('Errore salvataggio') }
    }
  },
}
</script>
