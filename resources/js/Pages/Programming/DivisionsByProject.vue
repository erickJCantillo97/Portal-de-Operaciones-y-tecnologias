<script setup>
import { ref } from 'vue'
import { useCommonUtilities } from '@/composable/useCommonUtilities'
import Badge from 'primevue/badge'
import ListBox from 'primevue/listbox'
import OverlayPanel from 'primevue/overlaypanel'

const { truncateString } = useCommonUtilities()

const dates = ref([]);

const props = defineProps({
  projects: {
    type: Array,
    required: true
  }
})

const items = ref(
  [
    'Lunes',
    'Martes',
    'Miércoles',
    'Jueves',
    'Viernes',
    'Sábado',
  ])

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
    name: 'PINTURA',
    short: 'DVPIN'
  },
  {
    name: 'PINTURA',
    short: 'DVPIN'
  },
  {
    name: 'PINTURA',
    short: 'DVPIN'
  },
])

//#region OverlayPanel
const op = ref()
const task = ref({})
const personals = ref([])

const toggle = (event, data, fecha) => {
  task.value = data
  axios.get(route('get.schedule.task.date', {
    task: data.id,
    date: fecha
  })).then((res) => {
    personals.value = res.data.schedules
  })
  op.value.toggle(event)
}
//#endregion
</script>
<template>
  <!-- Iterar los días de la semana -->
  <div class="mb-2 mt-2 flex w-full justify-between rounded-lg border bg-slate-200 py-4">
    <div class="flex w-[12%] flex-col items-center justify-center">
      <p class="font-semibold uppercase">Proyectos</p>
      <span class="text-xs font-semibold uppercase">($1.200.400.000)</span>
    </div>
    <div v-for="day in daysOfWeek"
      class="mx-4 flex-1 rounded-lg bg-gray-300 p-1 py-2 text-center font-semibold italic shadow-sm">
      <p class="uppercase">{{ day }}</p>
      <div class="flex justify-between w-full space-x-2 px-4">
        <div>12/04/2024</div>
        <div class="space-x-2">
          <Badge v-tooltip.bottom="'Programados'" value="10" />
          <Badge v-tooltip.bottom="'No Programados'" value="2" severity="danger" />
        </div>
      </div>
    </div>
  </div>

  <!--COLUMNA DE PROYECTOS-->
  <div class="h-[62vh]  space-y-1 overflow-y-auto">
    <div v-for="project in projects" class="flex w-full bg-slate-200 rounded-lg justify-left items-center">
      <div v-tooltip.top="`${project.name}\n($127 M)`" class="w-[14%]  border-r border-slate-400 text-center">
        <h2 class="text-lg text-primary font-bold">
          {{ truncateString(project.name, 20) }}
        </h2>
        <span class="text-[0.7rem]">($127 M)</span>
      </div>

      <!--CHIPS DE DIVISIONES-->
      <div class="flex justify-between w-full text-sm ">
        <div v-for="day in daysOfWeek"
          class="grid grid-cols-2 gap-2 text-center px-6 border-r border-dashed  min-h-full py-4 border-red-600">
          <div v-for="division in divisions" @click="toggle($event, task, day.date)"
            class="flex cursor-pointer items-center justify-center rounded-lg bg-slate-400 text-center text-white hover:scale-110">
            <div class="flex h-full w-full items-center rounded-l-lg bg-primary p-1">
              <span class="font-extrabold text-white">
                12
              </span>
            </div>
            <div class="space-y-1">
              <div class="rounded-tr-lg border-b bg-white px-2 font-bold text-black">
                {{ division.short }}
              </div>
              <span>$12M</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!--BARRA DE COSTOS INFERIOR-->
  <div class="grid h-6 w-full grid-cols-7 rounded-b-lg border border-primary">
    <div class="flex justify-center items-center">
      <span v-tooltip.top="'Costo Total de Proyectos en la Semana'" class="font-semibold text-emerald-500">
        $127 M
      </span>
    </div>
    <div v-for="item in items" class="col-span-1 px-2 justify-center items-center">
      <span v-tooltip.top="'Costo Programado'" class="font-semibold text-emerald-500">
        $127 M
      </span>
      <span>&nbsp;|&nbsp;</span>
      <span v-tooltip.top="'Costo Día Gerencia'" class="font-semibold text-red-500">
        $230 M
      </span>
    </div>
  </div>

  <!--OverlayPanel-->
  <OverlayPanel ref="op" class="h-[50%] w-[20%]" :pt="{
    content: '!p-2'
  }">
    <ListBox v-model="selectedTask" :options="divisions" filter optionLabel="name" filterPlaceholder="Buscar Actividad"
      :virtualScrollerOptions="{ itemSize: 8 }" listStyle="height:37vh" class="w-full md:w-14rem" :pt="{
        filterInput: '!h-8',
        item: '!h-20 !pt-2 !mb-2',
      }">
      <template #header>
        <h1 class="bg-primary text-white rounded-t-lg p-1 text-center">
          Lista de Actividades (DVPIN)
        </h1>
      </template>

      <template #option>
        <div class="flex justify-between">
          <p>{{ divisions[0].name }}</p>
          <span>$127 M</span>
        </div>
        <div class="grid grid-flow-col overflow-x-auto">
          <img v-tooltip="'Perfil Persona 1'"
            src="https://dthezntil550i.cloudfront.net/f4/latest/f41908291942413280009640715/1280_960/1b2d9510-d66d-43a2-971a-cfcbb600e7fe.png"
            class="border-full size-10 rounded-full border-2 border-green-500 object-cover m-1" alt="personals-profile">
          <img v-tooltip="'Perfil Persona 2'"
            src="https://dthezntil550i.cloudfront.net/f4/latest/f41908291942413280009640715/1280_960/1b2d9510-d66d-43a2-971a-cfcbb600e7fe.png"
            class="border-full size-10 rounded-full border-2 border-green-500 object-cover m-1" alt="personals-profile">
          <img v-tooltip="'Perfil Persona 3'"
            src="https://dthezntil550i.cloudfront.net/f4/latest/f41908291942413280009640715/1280_960/1b2d9510-d66d-43a2-971a-cfcbb600e7fe.png"
            class="border-full size-10 rounded-full border-2 border-green-500 object-cover m-1" alt="personals-profile">
          <img v-tooltip="'Perfil Persona 4'"
            src="https://dthezntil550i.cloudfront.net/f4/latest/f41908291942413280009640715/1280_960/1b2d9510-d66d-43a2-971a-cfcbb600e7fe.png"
            class="border-full size-10 rounded-full border-2 border-green-500 object-cover m-1" alt="personals-profile">
          <img v-tooltip="'Perfil Persona 5'"
            src="https://dthezntil550i.cloudfront.net/f4/latest/f41908291942413280009640715/1280_960/1b2d9510-d66d-43a2-971a-cfcbb600e7fe.png"
            class="border-full size-10 rounded-full border-2 border-green-500 object-cover m-1" alt="personals-profile">
          <img v-tooltip="'Perfil Persona 6'"
            src="https://dthezntil550i.cloudfront.net/f4/latest/f41908291942413280009640715/1280_960/1b2d9510-d66d-43a2-971a-cfcbb600e7fe.png"
            class="border-full size-10 rounded-full border-2 border-green-500 object-cover m-1" alt="personals-profile">
          <img v-tooltip="'Perfil Persona 7'"
            src="https://dthezntil550i.cloudfront.net/f4/latest/f41908291942413280009640715/1280_960/1b2d9510-d66d-43a2-971a-cfcbb600e7fe.png"
            class="border-full size-10 rounded-full border-2 border-green-500 object-cover m-1" alt="personals-profile">
        </div>

      </template>
    </ListBox>
  </OverlayPanel>
</template>
