<script setup>
import { ref, onMounted } from 'vue'
import Badge from 'primevue/badge'
import CardSkeleton from '@/Components/CardSkeleton.vue'

const people = ref([])
const showCardSkeleton = ref(true)

const getEstimatorData = async () => {
  await axios.get(route('get.estimator.data'))
    .then((res) => {
      people.value = res.data.people
      showCardSkeleton.value = false
    })
}

onMounted(() => {
  getEstimatorData()
})
</script>

<template>
  <div class="max-h-full  mb-8">
    <div class="bg-blue-800 text-center sticky">
      <h3 class="text-white text-lg font-semibold p-1">Tiempo Promedio de Respuesta de Estimadores</h3>
    </div>
    <div class=" custom-scroll border">
      <CardSkeleton v-if="showCardSkeleton" />
      <div class="block space-y-2 max-h-full h-56 p-1 overflow-y-auto">
        <div v-for="person in  people" :key="person.email"
          class="relative flex items-center space-x-3 rounded-lg border border-gray-300 bg-white p-3 shadow-sm focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-offset-2 hover:border-gray-400"
          v-tooltip="'Proceso: 3 \n Entregada: 5 \n Pendiente por Firma: 1 \n Firmada: 6 \n No Firmada: 1 \n Contratada: 2'"
          type="text" placeholder="Right">
          <div class="flex-shrink-0">
            <img class="size-10 object-cover rounded-full" :src="person.photo" alt="" />
          </div>
          <div class="min-w-0 flex-1">
            <a href="#" class="focus:outline-none">
              <span class="absolute inset-0" aria-hidden="true" />
              <p class="text-sm font-medium text-gray-900">{{ person.name }}</p>
              <p class="truncate text-sm text-gray-500">{{ person.quotes }} Estimación(es)</p>
            </a>
          </div>
          <div>
            <Badge :value="person.average + ' Dias'"></Badge>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
