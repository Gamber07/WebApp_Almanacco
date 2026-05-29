<template>
  <v-dialog v-model="open" max-width="600">
    <!-- Dialog riutilizzabile per creare o modificare una stagione. -->
    <template v-if="!noActivator" #activator="{ props }">
      <v-btn v-if="isAdmin" v-bind="props" color="primary" variant="elevated">
        {{ isEditMode ? 'Modifica Stagione' : 'Nuova Stagione' }}
      </v-btn>
    </template>

    <v-card>
      <v-card-title>{{ isEditMode ? 'Modifica Stagione' : 'Nuova Stagione' }}</v-card-title>
      <v-card-text>
        <v-text-field v-model="nome_stagione" label="Nome (es. 2023/2024)" />
        <v-text-field v-model.number="anno_inizio" label="Anno inizio" type="number" />
        <v-text-field v-model.number="anno_fine" label="Anno fine" type="number" />
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
      nome_stagione: '',
      anno_inizio: new Date().getFullYear(),
      anno_fine: new Date().getFullYear(),
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
        this.nome_stagione = this.item.nome_stagione || ''
        this.anno_inizio = this.item.anno_inizio || new Date().getFullYear()
        this.anno_fine = this.item.anno_fine || new Date().getFullYear()
      } else {
        this.nome_stagione = ''
        this.anno_inizio = new Date().getFullYear()
        this.anno_fine = new Date().getFullYear()
      }
    },
  },
  methods: {
    openDialog() {
      if (this.item) {
        this.nome_stagione = this.item.nome_stagione || ''
        this.anno_inizio = this.item.anno_inizio || new Date().getFullYear()
        this.anno_fine = this.item.anno_fine || new Date().getFullYear()
      } else {
        this.nome_stagione = ''
        this.anno_inizio = new Date().getFullYear()
        this.anno_fine = new Date().getFullYear()
      }
      this.open = true
    },
    async save() {
      if (!this.auth.token) { alert('Autenticazione richiesta'); return }
      try {
        const payload = {
          nome_stagione: this.nome_stagione,
          anno_inizio: this.anno_inizio,
          anno_fine: this.anno_fine,
        }
        const url = (import.meta.env.VITE_API_BASE ?? '') + '/stagioni' + (this.isEditMode ? `/${this.item.id}` : '')
        const res = this.isEditMode ? await axios.put(url, payload) : await axios.post(url, payload)
        this.$emit('saved', res.data.data.stagione)
        this.open = false
      } catch (e) { alert('Errore salvataggio') }
    }
  },
}
</script>
