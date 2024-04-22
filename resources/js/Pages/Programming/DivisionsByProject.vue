<script setup>
import { ref } from 'vue'
import Badge from 'primevue/badge';

const props = defineProps({
  projects: {
    type: Array,
    required: true
  }
})

const daysOfWeek = ref(
  [
    'Lunes',
    'Martes',
    'Miércoles',
    'Jueves',
    'Viernes',
    'Sábado',
  ])

const divisions = ref([
  {
    name: 'PINTURA',
    short: 'DVPIN'
  },
  {
    name: 'MECANICA',
    short: 'DVMEC'
  },
  {
    name: 'HABITABILIDAD',
    short: 'DVHAB'
  },
  {
    name: 'HABITABILIDAD',
    short: 'DVHAB'
  },
  {
    name: 'HABITABILIDAD',
    short: 'DVHAB'
  },
  {
    name: 'HABITABILIDAD',
    short: 'DVHAB'
  },
  {
    name: 'HABITABILIDAD',
    short: 'DVHAB'
  },
  {
    name: 'HABITABILIDAD',
    short: 'DVHAB'
  },
])

const dates = ref([]);
</script>
<template>
  <!-- Iterar los días de la semana -->
  <div class="flex justify-between w-full pl-12 py-4">
    <div v-for="day in daysOfWeek" class="flex-1 mx-2 py-2 p-1 text-center bg-gray-300 rounded-lg shadow">
      <p>{{ day }}</p>
      <div class="flex justify-between w-full space-x-2 px-4">
        <div>12/04/2024</div>
        <div class="space-x-2">
          <Badge value="10" />
          <Badge value="10" severity="danger" />
        </div>
      </div>
      <!-- <div class="divide-y divide-dashed"></div> -->
    </div>
  </div>
  <div class="space-y-2">
    <div v-for="project in projects" class="flex w-full  rounded-lg p-2 justify-left items-center">
      <h2>{{ project.name }}</h2>
      <div class="flex justify-between p-4 bg-gray-200 w-full text-xs">
        <div v-for="day in daysOfWeek" class="grid grid-cols-3 gap-2 text-center px-2">
          <div v-for="division in divisions"
            class="flex px-1.5 text-center bg-slate-400 space-x-2 justify-center items-center rounded-lg">
            <div class="bg-primary p-1 rounded-full">
              <p class="font-extrabold text-white">12</p>
            </div>
            <div class="space-y-1">
              <div class="border-b">DVPIN</div>
              <div>$12M</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
