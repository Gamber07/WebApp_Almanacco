<template>
  <v-dialog v-model="open" max-width="600">
    <!-- Dialog riutilizzabile per creare o modificare un campionato. -->
    <template v-if="!noActivator" #activator="{ props }">
      <v-btn v-if="isAdmin" v-bind="props" color="primary" variant="elevated">
        {{ isEditMode ? 'Modifica Campionato' : 'Nuovo Campionato' }}
      </v-btn>
    </template>

    <v-card>
      <v-card-title>{{ isEditMode ? 'Modifica Campionato' : 'Nuovo Campionato' }}</v-card-title>
      <v-card-text>
        <v-text-field v-model="nome" label="Nome campionato" variant="outlined" density="compact" />
      </v-card-text>
      <v-card-actions>
        <v-spacer />
        <v-btn text @click="open = false">Annulla</v-btn>
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
      this.nome = this.item?.nome || ''
    },
  },
  methods: {
    openDialog() {
      this.nome = this.item?.nome || ''
      this.open = true
    },
    async save() {
      if (!this.auth.token) { alert('Autenticazione richiesta'); return }
      try {
        const payload = { nome: this.nome }
        const url = (import.meta.env.VITE_API_BASE ?? '') + '/campionati' + (this.isEditMode ? `/${this.item.id}` : '')
        const res = this.isEditMode ? await axios.put(url, payload) : await axios.post(url, payload)
        this.$emit('saved', res.data.data.campionato)
        this.open = false
      } catch (e) { alert('Errore salvataggio') }
    }
  },
}
</script>
