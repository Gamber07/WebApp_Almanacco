<template>
  <v-dialog v-model="open" max-width="600">
    <!-- Dialog riutilizzabile per creare o modificare un giocatore. -->
    <template v-if="!noActivator" #activator="{ props }">
      <v-btn v-if="isAdmin" v-bind="props" class="btn-premium">
        <v-icon left>{{ isEditMode ? 'mdi-pencil' : 'mdi-plus' }}</v-icon>
        {{ isEditMode ? 'Modifica Giocatore' : 'Aggiungi Giocatore' }}
      </v-btn>
    </template>

    <v-card class="card-glass">
      <v-card-title class="gradient-text">
        <v-icon left>{{ isEditMode ? 'mdi-pencil' : 'mdi-plus' }}</v-icon>
        {{ isEditMode ? 'Modifica Giocatore' : 'Nuovo Giocatore' }}
      </v-card-title>
      <v-card-text>
        <v-text-field v-model="nome" label="Nome" variant="outlined" density="compact" class="mb-4" />
        <v-text-field v-model="cognome" label="Cognome" variant="outlined" density="compact" class="mb-4" />
        <v-text-field v-model="data_nascita" label="Data di nascita (YYYY-MM-DD)" variant="outlined" density="compact" class="mb-4" />
        <v-select :items="ruoli" v-model="ruolo" label="Ruolo" variant="outlined" density="compact" class="mb-4" />
        <v-select :items="squadre" item-title="nome" item-value="id" v-model="id_squadra" label="Squadra" variant="outlined" density="compact" class="mb-4" />
        <v-text-field v-model="nazionalita" label="Nazionalità" variant="outlined" density="compact" />
      </v-card-text>
      <v-card-actions>
        <v-spacer />
        <v-btn text @click="open=false">Annulla</v-btn>
        <v-btn class="btn-premium" @click="save">
          <v-icon left>mdi-check</v-icon>
          Salva
        </v-btn>
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
      cognome: '',
      data_nascita: '',
      ruolo: 'Attaccante',
      nazionalita: '',
      id_squadra: null,
      ruoli: ['Portiere', 'Difensore', 'Centrocampista', 'Attaccante'],
      squadre: [],
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
        this.cognome = this.item.cognome || ''
        this.data_nascita = this.item.data_nascita || ''
        this.ruolo = this.item.ruolo || 'Attaccante'
        this.nazionalita = this.item.nazionalita || ''
        this.id_squadra = this.item.id_squadra || null
      } else {
        this.nome = ''
        this.cognome = ''
        this.data_nascita = ''
        this.ruolo = 'Attaccante'
        this.nazionalita = ''
        this.id_squadra = null
      }
    },
    open(val) {
      if (val) this.fetchSquadre()
    }
  },
  methods: {
    async fetchSquadre() {
      try {
        const res = await axios.get((import.meta.env.VITE_API_BASE ?? '') + '/squadre')
        this.squadre = res.data.data?.squadre || []
      } catch (e) {
        console.error('Errore caricamento squadre', e)
      }
    },
    openDialog() {
      if (this.item) {
        this.nome = this.item.nome || ''
        this.cognome = this.item.cognome || ''
        this.data_nascita = this.item.data_nascita || ''
        this.ruolo = this.item.ruolo || 'Attaccante'
        this.nazionalita = this.item.nazionalita || ''
        this.id_squadra = this.item.id_squadra || null
      } else {
        this.nome = ''
        this.cognome = ''
        this.data_nascita = ''
        this.ruolo = 'Attaccante'
        this.nazionalita = ''
        this.id_squadra = null
      }
      this.fetchSquadre()
      this.open = true
    },
    async save() {
      if (!this.auth.token) { alert('Autenticazione richiesta'); return }
      try {
        const payload = {
          nome: this.nome,
          cognome: this.cognome,
          data_nascita: this.data_nascita,
          ruolo: this.ruolo,
          nazionalita: this.nazionalita,
          id_squadra: this.id_squadra,
        }
        const url = (import.meta.env.VITE_API_BASE ?? '') + '/giocatori' + (this.isEditMode ? `/${this.item.id}` : '')
        const res = this.isEditMode ? await axios.put(url, payload) : await axios.post(url, payload)
        this.$emit('saved', res.data.data.giocatore)
        this.open = false
      } catch (e) { alert('Errore salvataggio') }
    }
  },
}
</script>

<style scoped>
.card-glass {
  border-radius: 16px !important;
}

.gradient-text {
  background: linear-gradient(135deg, #7c3aed, #06b6d4);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  font-weight: 600 !important;
}
</style>
