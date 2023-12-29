<script setup>
import { onMounted, ref } from 'vue'
import Loading from '@/Components/Loading.vue'

const status = ref([])
const loadingStatus = ref(false)

const props = defineProps({
  quoteId: Number
})

onMounted(() => {
  axios.get(route('quotestatus.index', { id: props.quoteId }))
    .then((res) => {
      status.value = res.data.status
      loadingStatus.value = false
    })
})

const icons = [
  'fa-solid fa-user-clock', //0 -> Proceso
  'fa-solid fa-thumbs-up', //1 -> Entregada
  'fa-solid fa-calendar-days', //2 -> Pendiente por Firma
  'fa-solid fa-check', //3 -> Firmada
  'fa-solid fa-xmark', //4 -> No Firmada
  'fa-solid fa-dollar-sign', //5 -> Contratada
]

const colors = [
  'bg-orange-500', //0 -> Proceso
  'bg-purple-500', //1 -> Entregada
  'bg-yellow-500', //2 -> Pendiente por Firma
  'bg-blue-500', //3 -> Firmada
  'bg-red-500', //4 -> No Firmada
  'bg-emerald-500', //5 -> Contratada
]

const format_ES_Date = (date) => {
  return new Date(date).toLocaleString('es-CO',
    { day: '2-digit', month: 'long', year: 'numeric', weekday: "long" })
}
</script>

<template>
  <div class="flow-root">
    <ul role="list" class="shadow-md rounded-lg p-8 max-h-[258px] overflow-y-auto">
      <li v-for="(event, eventIdx) in status" :key="event.id">
        <section v-if="loadingStatus" class="h-[50vh] w-full flex flex-col justify-center items-center col-span-6">
          <Loading message="Cargando Estados" />
        </section>
        <div class="relative pb-8">
          <span v-if="eventIdx !== status.length - 1" class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200"
            aria-hidden="true" />
          <div class="relative flex space-x-3">
            <div>
              <span
                :class="[colors[event.status], 'size-8 rounded-full flex text-center items-center justify-center ring-white']">
                <i :class="icons[event.status]" class="text-white" aria-hidden="true" />
              </span>
            </div>
            <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
              <div>
                <p class="text-sm text-gray-500">
                  {{ event.get_status }} <a class="font-medium text-gray-900">{{ event.user.short_name }}</a>
                </p>
              </div>
              <div class="whitespace-nowrap text-right text-sm text-gray-500">
                <time :date="event.fecha">{{ format_ES_Date(event.fecha) }}</time>
              </div>
            </div>
          </div>
        </div>
      </li>
    </ul>
  </div>
</template>
