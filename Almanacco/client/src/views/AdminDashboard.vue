<template>
  <div>
    <!-- Dashboard di amministrazione: ingresso rapido ai moduli CRUD. -->
    <h1>Dashboard Amministrazione</h1>
    <v-row>
      <v-col cols="12" md="6">
        <v-card>
          <v-card-title>Gestione Partite</v-card-title>
          <v-card-text>Inserisci risultati delle partite e aggiorna il calendario</v-card-text>
          <v-card-actions>
            <v-btn to="/admin/gestione-partite" color="primary">Apri</v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
      <v-col cols="12" md="6">
        <v-card>
          <v-card-title>Gestione Giocatori</v-card-title>
          <v-card-text>Crea e modifica i profili dei giocatori</v-card-text>
          <v-card-actions>
            <v-btn to="/admin/gestione-giocatori" color="primary">Apri</v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>
    <v-row>
      <v-col cols="12" md="6">
        <v-card>
          <v-card-title>Gestione Squadre</v-card-title>
          <v-card-text>Aggiungi e modifica le informazioni delle squadre</v-card-text>
          <v-card-actions>
            <v-btn to="/admin/gestione-squadre" color="primary">Apri</v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
      <v-col cols="12" md="6">
        <v-card>
          <v-card-title>Gestione Stagioni</v-card-title>
          <v-card-text>Crea e gestisci le stagioni calcistiche</v-card-text>
          <v-card-actions>
            <v-btn to="/admin/gestione-stagioni" color="primary">Apri</v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>
    <v-row>
      <v-col cols="12" md="6">
        <v-card>
          <v-card-title>Gestione Mercato</v-card-title>
          <v-card-text>Gestisci contratti e ingaggi dei giocatori</v-card-text>
          <v-card-actions>
            <v-btn to="/admin/gestione-mercato" color="primary">Apri</v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

<script>
export default {
  name: 'AdminDashboard'
}
</script>
