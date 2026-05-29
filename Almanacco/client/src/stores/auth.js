import { defineStore } from 'pinia'
import axios from 'axios'

// Store unico per token, sessione utente e sincronizzazione delle chiamate API.
export const useAuthStore = defineStore('auth', {
  state: () => ({ token: localStorage.getItem('token') || null, user: null }),
  actions: {
    // Salva il token e aggiorna subito l'header Authorization globale.
    setToken(token) {
      this.token = token
      localStorage.setItem('token', token)
      axios.defaults.headers.common['Authorization'] = 'Bearer ' + token
    },
    // Pulisce l'autenticazione sia dallo store sia da localStorage.
    clear() {
      this.token = null
      this.user = null
      localStorage.removeItem('token')
      delete axios.defaults.headers.common['Authorization']
    },
    // Esegue il login e memorizza utente e token se le credenziali sono corrette.
    async login(username, password) {
      const res = await axios.post((import.meta.env.VITE_API_BASE ?? '') + '/auth', { username, password })
      if (res.data.status === 'ok') {
        this.setToken(res.data.token)
        this.user = res.data.user
        return true
      }
      return false
    }
    ,
    // All'avvio prova a ripristinare la sessione già salvata nel browser.
    async init() {
      if (!this.token) return
      try {
        const res = await axios.get((import.meta.env.VITE_API_BASE ?? '') + '/me')
        if (res.data.status === 'ok') {
          // /me returns data which may contain user
          this.user = res.data.data.user ?? res.data.data
          this.setToken(res.data.token ?? this.token)
        } else {
          this.clear()
        }
      } catch (e) {
        this.clear()
      }
    }
  }
})
