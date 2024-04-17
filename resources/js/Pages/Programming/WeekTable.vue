<script setup>
import { ref, onMounted } from 'vue'
import { useCommonUtilities } from '@/composable/useCommonUtilities'
import Loading from '@/Components/Loading.vue'
import OverlayPanel from 'primevue/overlaypanel'
import ProgressBar from 'primevue/progressbar'

const { truncateString } = useCommonUtilities()

const props = defineProps({
  project: Number
})

const daysOfWeek = ref(
  [
    'Lunes',
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

  return `${day}-${month}-${year}`
}

onMounted(() => {
  getTaskByDivision()
})

const tasks = ref([])
const getTask = (day, division) => {
  axios.get(route('get.task.date.division', {
    project: props.project,
    date: day,
    division: division.short
  })).then(res => {
    tasks.value[day][division.short]['tasks'] = res.data
    tasks.value[day][division.short]['loading'] = false
  })

  return day
}

const getTaskByDivision = async () => {
  for (const day of daysOfWeek.value) {
    const formattedDate = getFormattedDate(day);
    dates.value.push({
      name: day,
      date: formattedDate
    });
    tasks.value[formattedDate] = {}
    for (const division of divisions.value) {
      tasks.value[formattedDate][division.short] = []
      tasks.value[formattedDate][division.short]['loading'] = true
      tasks.value[formattedDate][division.short]['tasks'] = []
      getTask(formattedDate, division);
    }
  }
}

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
  <!-- <Loading v-if="loadingWeekTable"
    :message="`Por favor espere un momento mientras se cargan las actividades. \n Puede tardar unos minutos`" /> -->
  <div>
    <div class="grid grid-cols-6 w-full">
      <div class="text-center border border-gray-600 bg-blue-300 font-extrabold">
        <p>División</p>
      </div>
      <!-- Iterar los días de la semana -->
      <div v-for="(day, index) in dates" :key="index"
        class="border border-gray-600 text-center bg-blue-300 font-extrabold">
        <div class="block">
          <p>{{ day.name }}</p>
          <p class="text-xs italic text-slate-600">
            {{ day.date }}
          </p>
        </div>
      </div>
    </div>
    <!-- Iterar las divisiones -->
    <div v-for="division in divisions" :key="division.name" class="grid grid-cols-6 w-full items-center">
      <div class="text-center  border-b p-5 border-gray-600 h-[9.4vh]">
        <p>{{ division.name }}</p>
        <p>{{ division.short }}</p>
      </div>
      <!-- Iterar los días de la semana -->
      <div v-for="day in dates" class="border-l border-b border-gray-600 text-center h-[9.4vh] ">
        <div v-if="tasks[day.date][division.short] != undefined" class="size-full">
          <div v-if="tasks[day.date]" class="text-sm space-y-1"
            :class="$page.props.auth.user.oficina == division.short ? 'bg-blue-500' : ''">
            <div v-if="tasks[day.date][division.short]['loading']"
              class="border-gray-300 size-8 animate-spin rounded-full border-4 border-t-blue-600" />
            <div v-else>
              <ul v-for="(task, index) in tasks[day.date][division.short]['tasks']" class="block text-left truncate ">
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
  </div>

  <!--OverlayPanel-->
  <OverlayPanel ref="op" class="h-68 w-90" :pt="{
        content: '!p-0'
      }">
    <div class="rounded-b-lg bg-primary p-2 text-center">
      <span class="font-bold text-xs text-white uppercase">
        {{ task.name }}
      </span>
    </div>
    <div class="space-y-2 p-2">
      <div class="rounded-lg bg-orange-500 p-1 text-center">
        <span class="text-xs text-white uppercase underline">
          <strong>Proceso: </strong>{{ task.process }}
        </span>
      </div>
      <!-- Avance {{ parseFloat(task.percentDone).toFixed(2) }} % -->
      <div class="space-y-2 rounded-lg border border-gray-300 p-2 text-center">
        <!-- <div class="bg-blue-500 p-1 rounded-lg">
          <span class="text-xs text-white uppercase">Avance</span>
        </div> -->
        <ProgressBar :value="parseFloat(task.percentDone).toFixed(0)" :pt="{
        root: '!bg-slate-300 !text-center',
        value: '!bg-emerald-500'
      }" />
      </div>

      <div class="space-y-2 rounded-lg border border-gray-300 p-2 text-center">
        <div class="bg-blue-500 p-1 rounded-lg">
          <span class="text-xs text-white uppercase">
            Asignaciones
          </span>
        </div>

        <div class="grid grid-cols-5 gap-2">
          <img v-for="personal in personals" :src="personal.photo"
            class="border-full size-12 rounded-full border-2 border-green-500 object-cover" alt="personals-profile"
            v-tooltip.bottom="personal.name">
        </div>
      </div>
      <!-- {{ truncateString(task.name, 40) }} -->
    </div>
  </OverlayPanel>
</template>
