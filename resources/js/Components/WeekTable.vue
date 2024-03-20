<template>
  <div>
    <div class="grid grid-cols-8 w-full">
      <div class="text-center border border-gray-600">
        <p>División</p>
      </div>
      <!-- Iterar los días de la semana -->
      <div class="border border-gray-600 text-center" v-for="(day, index) in daysOfWeek" :key="index">
        <div class="block">
          <p>{{ day }}</p>
          <p class="text-xs italic text-slate-600">
            {{ getFormattedDate(day) }}
          </p>
        </div>
      </div>
    </div>
    <!-- Iterar las divisiones -->
    <div class="grid grid-cols-8 w-full" v-for="division in divisions" :key="division.name">
      <div class="text-center border border-gray-600">
        <p>{{ division.name }}</p>
        <p>{{ division.short }}</p>
      </div>
      <!-- Iterar los días de la semana -->
      <div class="border border-gray-600 text-center" v-for="day in daysOfWeek">
        <div class="block" :key="getTask(getFormattedDate(day), division)" >
          <div v-if="tasks[getFormattedDate(day)]">
          {{ tasks[getFormattedDate(day)][division.name] }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const daysOfWeek = ref(
  [
    'Lunes',

  ])

const divisions = ref([
  {
    name: 'PINTURA',
    short: 'DVPIN'
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

  return `${day}/${month}/${year}`
}

const tasks = ref([])
const ninjaJutsu = ref(0)
const getTask = async (day, division) => {
  console.log(tasks.value[day][division.name] )

  await axios.get(route('get.task.date.division'), {
    date: day,
    division: division.name
  }).then(res => {
      console.log(tasks.value[day][division.name] )
      console.log(tasks.value[day][division.name] )
      tasks.value[day][division.name] = res.data
    })

  return day
}
</script>
