<script setup>
import { ref, onMounted } from 'vue'
import AdvancedBarChart from '@/Pages/Dashboards/Projects/AdvancedBarChart.vue'
import GaugeGradeChart from '@/Pages/Dashboards/Projects/GaugeGradeChart.vue'
import SimpleScatterChart from '@/Pages/Dashboards/Projects/SimpleScatterChart.vue'
import BeijingAQIChart from '../Quotes/BeijingAQIChart.vue';

onMounted(() => {
  getData()
})

const projects = ref([])
const scatterSeries = ref([])

const getData = () => {
  axios.get(route('progressProjectWeek.get.data.week')).then((res) => {
    projects.value = res.data.indicators.map(p => p.project)
    series.value.push(
      {
        name: 'Planeado',
        type: 'bar',
        data: res.data.indicators.map(p => p.planned_progress),
        showBackground: true,
        color: ['#4A4B51'],

      },
      {
        name: 'real',
        type: 'bar',
        color: ['#2E3092'],
        data: res.data.indicators.map(p => p.real_progress),
        showBackground: true,
      },
    )
    for (var i of res.data.indicators) {
      scatterSeries.value.push({
        name: i.project,
        symbolSize: 20,
        data: [
          [i.indicators.CPI, i.indicators.SPI],
        ],
        type: 'scatter',
      })
    }
    showLineChart.value++
  })
}

const showLineChart = ref(0)
const series = ref([])
</script>
<template>
  <main class="h-screen">
    <div class="grid grid-cols-4 gap-2 mb-8">
      <div class="col-span-2 border border-gray-200 rounded-lg shadow-sm">
        <div class="flex justify-center items-center p-1 rounded-t-lg bg-blue-800 text-white">
          <h2 class="text-md font-semibold">PROMEDIO</h2>
        </div>
        <div class="flex justify-center size-full">
          <GaugeGradeChart :key="showLineChart" title="CPI" :value="1.005" />
          <GaugeGradeChart :key="showLineChart" title="SPI" :value="0.985" />
        </div>
      </div>
      <div class="col-span-2 border border-gray-200 rounded-lg shadow-sm">
        <div class="flex justify-center items-center p-1 rounded-t-lg bg-blue-800 text-white">
          <h2 class="text-md font-semibold">PONDERADO</h2>
        </div>
        <div class="flex justify-center size-full">
          <GaugeGradeChart :key="showLineChart" title="CPI" :value="1.019" />
          <GaugeGradeChart :key="showLineChart" title="SPI" :value="0.994" />
        </div>
      </div>
    </div>
    <div class="grid grid-cols-2 gap-2 p-4">
      <div class="col-span-1">
        <AdvancedBarChart :key="showLineChart" title="Avance Proyectos en Ejecución " :series="series"
          :yAxisData="projects" />
      </div>
      <div class="col-span-1">
        <!-- <AdvancedBar :key="showLineChart" title="Avance Proyectos en Ejecución" :series="series" /> -->
        <SimpleScatterChart :key="showLineChart" title="Proyectos" :series="scatterSeries" />

      </div>
      <BeijingAQIChart class="shadow-md col-span-1 md:col-span-3 mt-4" />
    </div>
  </main>
</template>
