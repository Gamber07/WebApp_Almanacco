import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import SquadreView from '../views/SquadreView.vue'
import GiocatoriView from '../views/GiocatoriView.vue'
import ContrattiView from '../views/ContrattiView.vue'
import PartiteView from '../views/PartiteView.vue'
import ClassificaView from '../views/ClassificaView.vue'
import StagioniView from '../views/StagioniView.vue'

const routes = [
  // Rotte pubbliche: pagine consultabili senza permessi amministrativi.
  { path: '/', component: HomeView },
  { path: '/classifiche', component: ClassificaView },
  { path: '/calendario', component: PartiteView },
  { path: '/squadre', component: SquadreView },
  { path: '/squadre/:id', component: () => import('../views/SchedaSquadra.vue') },
  { path: '/giocatori', component: GiocatoriView },
  { path: '/giocatori/:id', component: () => import('../views/SchedaGiocatore.vue') },
  { path: '/partite/:id', component: () => import('../views/DettaglioPartita.vue') },

  // Rotte admin: accesso protetto dal guard globale.
  { path: '/admin', component: () => import('../views/AdminDashboard.vue'), meta: { requiresAdmin: true } },
  { path: '/admin/gestione-partite', component: () => import('../views/admin/GestionePartiteAdmin.vue'), meta: { requiresAdmin: true } },
  { path: '/admin/gestione-giocatori', component: () => import('../views/admin/GestioneGiocatoriAdmin.vue'), meta: { requiresAdmin: true } },
  { path: '/admin/gestione-squadre', component: () => import('../views/admin/GestioneSquadreAdmin.vue'), meta: { requiresAdmin: true } },
  { path: '/admin/gestione-stagioni', component: () => import('../views/admin/GestioneStagioniAdmin.vue'), meta: { requiresAdmin: true } },
  { path: '/admin/gestione-mercato', component: () => import('../views/admin/GestioneMercatoAdmin.vue'), meta: { requiresAdmin: true } },

  // Rotte legacy: mantenute per compatibilità con vecchi link e redirect.
  { path: '/classifica', redirect: '/classifiche' },
  { path: '/partite', redirect: '/calendario' },
  { path: '/stagioni', component: StagioniView },
  { path: '/contratti', component: ContrattiView },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

// Guard globale: blocca l'accesso admin a chi non ha il ruolo corretto.
router.beforeEach((to, from, next) => {
  if (to.meta.requiresAdmin) {
    // Check if user is admin
    const { useAuthStore } = require('../stores/auth')
    const auth = useAuthStore()
    
    if (auth.user?.ruolo === 'admin') {
      next()
    } else {
      // Redirect to home if not admin
      next('/')
    }
  } else {
    next()
  }
})

export default router
