<template>
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
    <div class="grid grid-cols-6 w-full items-center" v-for="division in    divisions   " :key="division.name">
      <div class="text-center  border-b p-5 border-gray-600 h-[9.4vh]">
        <p>{{ division.name }}</p>
        <p>{{ division.short }}</p>
      </div>
      <!-- Iterar los días de la semana -->
      <div class="border-l border-b border-gray-600 text-center h-[9.4vh]" v-for="day in dates"
        :class="tasks[day.date][division.short].length == 0 ? 'bg-gray-300' : 'bg-white'">
        <div class="text-sm space-y-1 " v-if="tasks[day.date]">
          <ul v-for="(task, index) in  tasks[day.date][division.short]" class="block text-left truncate ">
            <li class="flex items-center space-x-2">
              <i class="fa-solid fa-caret-right"></i>

              <span>
                {{ task.name }}</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, onUnmounted, ref } from 'vue'

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


onMounted(() => {
  for (var day of daysOfWeek.value) {
    dates.value.push({
      name: day,
      date: getFormattedDate(day)
    })
    tasks.value[getFormattedDate(day)] = []
    for (var d of divisions.value) {
      tasks.value[getFormattedDate(day)][d.short] = [];
      getTask(getFormattedDate(day), d)
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
</script>
