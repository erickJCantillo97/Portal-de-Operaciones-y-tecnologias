<script setup>
import { ref } from 'vue'
import { useCommonUtilities } from '@/composable/useCommonUtilities'
import Badge from 'primevue/badge'
import ListBox from 'primevue/listbox'
import OverlayPanel from 'primevue/overlaypanel'
import UserStatusProgramming from '@/Components/sections/UserStatusProgramming.vue'

const { truncateString } = useCommonUtilities()



const props = defineProps({
  projects: {
    type: Array,
    required: true
  }
})

function format24h(hora) {
  try {
    if (hora.length > 5) {
      return new Date(hora).toLocaleString('es-CO',
        { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' })
    } else {
      return new Date("1970-01-01T" + hora).toLocaleString('es-CO',
        { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' })
    }
  } catch (error) {
    console.log(error)
    return 'error'
  }

}
function obtenerFormatoSemana(fecha) {
  const fechaActual = fecha;
  const año = fechaActual.getFullYear();
  const diferencia = fecha - (new Date(fecha.getFullYear(), 0, 1));
  const diasTranscurridos = Math.floor(diferencia / (24 * 60 * 60 * 1000));
  const semana = Math.ceil((diasTranscurridos + (new Date(fecha.getFullYear(), 0, 1)).getDay() + 1) / 7); // Función para obtener el número de la semana
  return `${año}-W${semana}`;
}
function obtenerFechasSemana(diaSeleccionado) {
  // const diasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
  const fechasSemana = [];

  // Asegurémonos de que el día seleccionado sea un objeto Date
  const fechaSeleccionada = new Date(diaSeleccionado);

  // Restamos los días necesarios para llegar al lunes
  fechaSeleccionada.setDate(fechaSeleccionada.getDate() - fechaSeleccionada.getDay() + 1);

  // Generamos las fechas para cada día de la semana
  for (let i = 0; i < 6; i++) {
    fechasSemana.push(new Date(fechaSeleccionada));
    fechaSeleccionada.setDate(fechaSeleccionada.getDate() + 1);
  }

  return fechasSemana;
}

function obtenerDiaSemana(dato) {
  const partes = dato.split('-W');
  const año = parseInt(partes[0]);
  const semana = parseInt(partes[1]);

  // Crear una fecha para el primer día de la semana
  const fecha = new Date(año, 0, 1 + (semana - 1) * 7);

  return fecha;
}
//#endregion
const date = ref(new Date())
const dates = ref(new Date(date.value.getFullYear(), date.value.getMonth(), date.value.getDate() + 1))
const diasSemana = ref(obtenerFechasSemana(dates.value))

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
    name: 'MECANICA',
    short: 'DVMEC'
  },
  {
    name: 'HABITABILIDAD',
    short: 'DVHAB'
  },
])

const profiles = ref([
  {
    id: 1,
    tooltip: 'Perfil Persona 1',
    url: 'https://dthezntil550i.cloudfront.net/f4/latest/f41908291942413280009640715/1280_960/1b2d9510-d66d-43a2-971a-cfcbb600e7fe.png'
  },
  {
    id: 2,
    tooltip: 'Perfil Persona 2',
    url: 'https://dthezntil550i.cloudfront.net/f4/latest/f41908291942413280009640715/1280_960/1b2d9510-d66d-43a2-971a-cfcbb600e7fe.png'
  },
  {
    id: 3,
    tooltip: 'Perfil Persona 3',
    url: 'https://dthezntil550i.cloudfront.net/f4/latest/f41908291942413280009640715/1280_960/1b2d9510-d66d-43a2-971a-cfcbb600e7fe.png'
  },
  {
    id: 4,
    tooltip: 'Perfil Persona 4',
    url: 'https://dthezntil550i.cloudfront.net/f4/latest/f41908291942413280009640715/1280_960/1b2d9510-d66d-43a2-971a-cfcbb600e7fe.png'
  },
  {
    id: 5,
    tooltip: 'Perfil Persona 5',
    url: 'https://dthezntil550i.cloudfront.net/f4/latest/f41908291942413280009640715/1280_960/1b2d9510-d66d-43a2-971a-cfcbb600e7fe.png'
  },
  {
    id: 6,
    tooltip: 'Perfil Persona 6',
    url: 'https://dthezntil550i.cloudfront.net/f4/latest/f41908291942413280009640715/1280_960/1b2d9510-d66d-43a2-971a-cfcbb600e7fe.png'
  },
  {
    id: 7,
    tooltip: 'Perfil Persona 7',
    url: 'https://dthezntil550i.cloudfront.net/f4/latest/f41908291942413280009640715/1280_960/1b2d9510-d66d-43a2-971a-cfcbb600e7fe.png'
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
    </div>
    <div v-for="day in diasSemana" class="mx-4 flex-1 rounded-lg  p-1 py-2 text-center font-semibold italic shadow-sm"
      :class="day.toISOString().split('T')[0] == date.toISOString().split('T')[0] ? 'bg-primary text-white' : 'bg-gray-300'">
      <p class="uppercase">{{
      day.toLocaleDateString('es-CO', {
        weekday: 'long', year: 'numeric', month: 'numeric', day:
          'numeric'
      }) }}</p>
      <div class="flex justify-between w-full space-x-2 px-4">
        <p>$ 120.000.000</p>
        <div class="space-x-2">
          <UserStatusProgramming :date="day" :key="dia" loadingType="spiner" />
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
        <span class="text-[0.7rem]">${{ Math.ceil(Math.random() * 200) }} M</span>
      </div>

      <!--CHIPS DE DIVISIONES-->
      <div class="flex justify-between w-full text-sm ">
        <div v-for="day in daysOfWeek"
          class="grid grid-cols-2 gap-2 text-center px-6 border-r border-dashed  min-h-full py-4 border-red-600">
          <div v-for="division in divisions" @click="toggle($event, task, day.date)"
            class="flex cursor-pointer items-center justify-center rounded-lg bg-slate-400 text-center text-white hover:scale-110">
            <div class="flex size-full justify-center items-center rounded-l-lg bg-primary p-1">
              <span class="font-extrabold text-white text-xs">
                {{ Math.ceil(Math.random() * 20) }}
              </span>
            </div>
            <div class="space-y-1">
              <div class="rounded-tr-lg border-b bg-white px-2 font-bold text-black text-xs">
                {{ division.short }}
              </div>
              <span class="text-xs">${{ Math.ceil(Math.random() * 200) }} M</span>
            </div>
          </div>
        </div>
      </div>
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
          <p class="font-semibold text-primary">{{ divisions[0].name }}</p>
          <span class="rounded-lg bg-emerald-500 p-1 text-xs text-white font-medium">
            ${{ Math.ceil(Math.random() * 200) }} M
          </span>
        </div>
        <div class="grid grid-flow-col">
          <div v-for="profile in profiles" :key="profile.tooltip" class="flex-none overflow-x-auto w-8 m-1">
            <img v-tooltip="profile.tooltip" :src="profile.url"
              class="rounded-full border-2 border-green-500 object-cover size-8" :alt="profile.tooltip">
          </div>
        </div>
      </template>
    </ListBox>
  </OverlayPanel>
</template>
