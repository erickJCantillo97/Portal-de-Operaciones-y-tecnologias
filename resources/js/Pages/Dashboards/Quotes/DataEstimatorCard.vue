<script setup>
import { EllipsisVerticalIcon } from '@heroicons/vue/20/solid'
import Badge from 'primevue/badge'

const people = [
  {
    name: 'Erick Cantillo',
    email: 'ecantillo@cotecmar.com',
    role: '18 Estimaciones',
    imageUrl:
      'https://images.unsplash.com/photo-1519244703995-f4e0f30006d5?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80',
  },
  {
    name: 'Giovanni Buelvas',
    email: 'gbuelvas@cotecmar.com',
    role: '20 Estimaciones',
    imageUrl:
      'https://images.unsplash.com/photo-1570295999919-56ceb5ecca61?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=4&w=256&h=256&q=60',
  },
  {
    name: 'Ronny Gutierrez',
    email: 'rgutierrez@cotecmar.com',
    role: '52 Estimaciones',
    imageUrl:
      'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80',
  },
  {
    name: 'Yessica Liñan',
    email: 'ylinan@cotecmar.com',
    role: '15 Estimaciones',
    imageUrl:
      'https://images.unsplash.com/photo-1498551172505-8ee7ad69f235?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=4&w=256&h=256&q=60',
  },
  {
    name: 'Carlos Ospino',
    email: 'cospino@cotecmar.com',
    role: '2 Estimaciones',
    imageUrl:
      'https://images.unsplash.com/photo-1566492031773-4f4e44671857?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=4&w=256&h=256&q=60',
  },
  {
    name: 'Lina Matoso',
    email: 'lmatoso@cotecmar.com',
    role: '19 Estimaciones',
    imageUrl:
      'https://images.unsplash.com/photo-1532417344469-368f9ae6d187?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=4&w=256&h=256&q=60',
  },
  // More people...
]
</script>

<template>
  <div class="max-h-full h-56 p-3 mb-8">
    <div class="bg-blue-800 text-center mb-2 rounded-t-md sticky">
      <h3 class="text-white text-lg font-semibold p-1">Tiempo Promedio de Respuesta de Estimadores</h3>
    </div>
    <div class="max-h-full h-56 p-3 overflow-y-auto custom-scroll border rounded-lg">
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div v-for="person in people" :key="person.email"
          class="relative flex items-center space-x-3 rounded-lg border border-gray-300 bg-white p-3 shadow-sm focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-offset-2 hover:border-gray-400"
          v-tooltip="'Proceso: 3 \n Entregada: 5 \n Pendiente por Firma: 1 \n Firmada: 6 \n No Firmada: 1 \n Contratada: 2'"
          type="text" placeholder="Right">
          <div class="flex-shrink-0">
            <img class="size-10 rounded-full" :src="person.imageUrl" alt="" />
          </div>
          <div class="min-w-0 flex-1">
            <a href="#" class="focus:outline-none">
              <span class="absolute inset-0" aria-hidden="true" />
              <p class="text-sm font-medium text-gray-900">{{ person.name }}</p>
              <p class="truncate text-sm text-gray-500">{{ person.role }}</p>
            </a>
          </div>
          <div>
            <Badge value="2 Dias"></Badge>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
