<script setup>
import { ref } from 'vue'

const props = defineProps({
  shipId: {
    type: Number
  },
  ship: {
    default: null,
  },
  clases: {
    type: String,
    default: '',
  },
  avance: {
    type: Boolean,
    default: true,
  },
  activo: {
    type: Boolean,
    default: false,
  },
  menu: {
    type: Boolean,
    default: true,
  }
})

const ship = ref({})
const cargando = ref(true)

if (props.ship) {
  ship.value = props.ship
  cargando.value = false
} else {
  axios.get(route('ships.index'))
    .then(response => {
      ship.value = response.data[0]
      cargando.value = false
    })
}
</script>

<template>
  <div :class="clases" class="flex items-center rounded-md text-gray-90 h-14 min-w-max">
    <img :src="ship.file" onerror="this.src='/public/images/generic-boat.png'"
      class="h-0 mr-1 rounded-lg sm:h-12 sm:w-16" />
    <div class="flex-col">
      <p class="text-xs font-bold">{{ ship.name }}</p>
      <div class="flex space-x-1">
        <p class="text-xs italic">Casco:</p>
        <p class="text-xs italic"> {{ ship.idHull }}</p>
      </div>
    </div>
  </div>
</template>
