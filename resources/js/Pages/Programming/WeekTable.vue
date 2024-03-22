<script setup>
import { ref, onMounted } from 'vue'
import { useCommonUtilities } from '@/composable/useCommonUtilities'
import OverlayPanel from 'primevue/overlaypanel'
import Loading from '@/Components/Loading.vue'
import axios from 'axios';

const { truncateString } = useCommonUtilities()

const props = defineProps({
  project: Number
})

const daysOfWeek = ref(
  [
    'Lunes',
    'Martes',
    'Miercoles',
    'Jueves',
    'Viernes',
  ])

const dates = ref([]);
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

const getFormattedDate = (day) => {
  const today = new Date()
  const dayIndex = daysOfWeek.value.indexOf(day)
  const monday = new Date(today)

  monday.setDate(today.getDate() - today.getDay() + 1) // lunes de la semana actual
  const targetDay = new Date(monday)

  targetDay.setDate(monday.getDate() + dayIndex)
  return formatDate(targetDay)
}

const formatDate = (date) => {
  const day = date.getDate().toString().padStart(2, '0')
  const month = (date.getMonth() + 1).toString().padStart(2, '0')
  const year = date.getFullYear()

  return `${year}-${month}-${day}`
}

const loadingWeekTable = ref(false)
onMounted(async () => {
  for (const day of daysOfWeek.value) {
    const formattedDate = getFormattedDate(day);
    dates.value.push({
      name: day,
      date: formattedDate
    });
    tasks.value[formattedDate] = {}
    for (const division of divisions.value) {
      tasks.value[formattedDate][division.short] = []
      await getTask(formattedDate, division);
    }
  }
})

const tasks = ref([])
const getTask = async (day, division) => {
  await axios.get(route('get.task.date.division', {
    project: props.project,
    date: day,
    division: division.short
  })).then(res => {
    tasks.value[day][division.short] = res.data
  })

  return day
}

//#region OverlayPanel
const op = ref()
const task = ref({})
const personals = ref()
const toggle = (event, data, fecha) => {
  console.log(fecha)
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
  <!-- <Loading v-if="loadingWeekTable"
    :message="`Por favor espere un momento mientras se cargan las actividades. \n Puede tardar unos minutos`" /> -->
  <div>
    <div class="grid grid-cols-6 w-full">
      <div class="text-center border border-gray-600 bg-blue-300 font-extrabold">
        <p>División</p>
      </div>
      <!-- Iterar los días de la semana -->
      <div class="border border-gray-600 text-center bg-blue-300 font-extrabold" v-for="(day, index) in dates"
        :key="index">
        <div class="block">
          <p>{{ day.name }}</p>
          <p class="text-xs italic text-slate-600">
            {{ day.date }}
          </p>
        </div>
      </div>
    </div>
    <!-- Iterar las divisiones -->
    <div class="grid grid-cols-6 w-full items-center" v-for="division in divisions" :key="division.name">
      <div class="text-center  border-b p-5 border-gray-600 h-[9.4vh]">
        <p>{{ division.name }}</p>
        <p>{{ division.short }}</p>
      </div>
      <!-- Iterar los días de la semana -->
      <div v-for="day in dates" class="border-l border-b border-gray-600 text-center h-[9.4vh] ">
        <div v-if="tasks[day.date][division.short] != undefined" class="size-full"
          :class="tasks[day.date][division.short].length == 0 ? 'bg-gray-300' : 'bg-white'">
          <div class="text-sm space-y-1 " v-if="tasks[day.date]"
            :class="$page.props.auth.user.oficina == division.short ? 'bg-blue-500' : ''">
            <ul v-for="(task, index) in tasks[day.date][division.short]" class="block text-left truncate ">
              <li class="flex items-center space-x-2">
                <i class="fa-solid fa-caret-right"></i>
                <button class="cursor-pointer text-blue-600 underline" @click="toggle($event, task, day.date)"
                  v-tooltip.left="`${task.name} \n (Click para ver detalles)`">
                  {{ truncateString(task.name, 40) }}
                </button>
              </li>
            </ul>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!--OverlayPanel-->
  <OverlayPanel ref="op" class="size-72" :pt="{
        content: '!p-0'
      }">
    <div class="p-4">
      <div class="text-primary font-bold">
        {{ task.process }}
      </div>
      <div class="text-primary">
        {{ task.name }}
      </div>
      Avance {{ parseFloat(task.percentDone).toFixed(2) }} %
      <div class="grid grid-cols-5 gap-2 ">

        <img :src="personal.photo" v-for="personal in personals" class="size-12 border rounded-full" alt=""
          v-tooltip.bottom="personal.name">
      </div>
      <!-- {{ truncateString(task.name, 40) }} -->
    </div>
  </OverlayPanel>
</template>
