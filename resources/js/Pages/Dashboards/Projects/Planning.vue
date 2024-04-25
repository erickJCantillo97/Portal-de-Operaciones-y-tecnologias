<script setup>
import { ref, onMounted } from 'vue'
import AdvancedBarChart from '@/Pages/Dashboards/Projects/AdvancedBarChart.vue'
import GaugeGradeChart from '@/Pages/Dashboards/Projects/GaugeGradeChart.vue'
import SimpleScatterChart from '@/Pages/Dashboards/Projects/SimpleScatterChart.vue'
import BeijingAQIChart from '../Quotes/BeijingAQIChart.vue';

onMounted(() => {
  getData()
  getPromedio()
})

const projects = ref([])
const promedios = ref({
  cpi: 0,
  spi: 0
})
const ponderados = ref({
  cpi: 0,
  spi: 0
})
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

const getPromedio = () => {
  axios.get(route('get.cpispi.promedio')).then((res) => {
    promedios.value.cpi = res.data.cpi
    promedios.value.spi = res.data.spi
    ponderados.value.spi = res.data.spiPonderado
    ponderados.value.cpi = res.data.cpiPonderado
    showLineChart.value++
  })
}
</script>
<template>
  <main>
    <div class="grid grid-cols-4 gap-2 mb-8">
      <div class="col-span-2 border border-gray-200 rounded-lg shadow-sm">
        <div class="flex justify-center items-center p-1 rounded-t-lg bg-blue-800 text-white">
          <h2 class="text-md font-semibold">PROMEDIO</h2>
        </div>
        <div class="flex justify-center size-full">
          <GaugeGradeChart :key="showLineChart" title="CPI" :value="promedios.cpi" />
          <GaugeGradeChart :key="showLineChart" title="SPI" :value="promedios.spi" />
        </div>
      </div>
      <div class="col-span-2 border border-gray-200 rounded-lg shadow-sm">
        <div class="flex justify-center items-center p-1 rounded-t-lg bg-blue-800 text-white">
          <h2 class="text-md font-semibold">PONDERADO</h2>
        </div>
        <div class="flex justify-center size-full">
          <GaugeGradeChart :key="showLineChart" title="CPI" :value="ponderados.cpi" />
          <GaugeGradeChart :key="showLineChart" title="SPI" :value="promedios.spi" />
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
      <div class="shadow-md col-span-1 md:col-span-2 m-4">
        <BeijingAQIChart />
      </div>
    </div>
  </main>
</template>
