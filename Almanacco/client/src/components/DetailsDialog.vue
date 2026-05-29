<template>
  <v-dialog v-model="open" max-width="700">
    <!-- Dialog generico per mostrare i dettagli in sola lettura. -->
    <template #activator="{ on, attrs }">
      <v-btn v-bind="attrs" v-on="on" variant="text" size="small">Dettaglio</v-btn>
    </template>

    <v-card>
      <v-card-title>{{ title }}</v-card-title>
      <v-card-text>
        <v-table density="compact">
          <tbody>
            <tr v-for="field in fields" :key="field.label">
              <td class="font-weight-medium">{{ field.label }}</td>
              <td>{{ field.value }}</td>
            </tr>
          </tbody>
        </v-table>
      </v-card-text>
      <v-card-actions>
        <v-spacer />
        <v-btn text @click="open = false">Chiudi</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import { ref } from 'vue'

export default {
  props: {
    title: {
      type: String,
      default: 'Dettaglio',
    },
    fields: {
      type: Array,
      default: () => [],
    },
  },
  setup() {
    const open = ref(false)

    return { open }
  },
}
</script>
