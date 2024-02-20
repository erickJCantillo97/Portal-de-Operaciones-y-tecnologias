<script setup>
import { ref, onMounted } from 'vue'
import AdvancedBarChart from '@/Pages/Dashboards/Projects/AdvancedBarChart.vue'
import GaugeGradeChart from '@/Pages/Dashboards/Projects/GaugeGradeChart.vue'
import SimpleScatterChart from '@/Pages/Dashboards/Projects/SimpleScatterChart.vue'
import MiniCardInfo from '@/Components/MiniCardInfo.vue'

const props = defineProps({

})


onMounted(() => {
  getData()
})
const projects = ref()

const getData = () => {
  axios.get(route('progressProjectWeek.get.data.week')).then((res) => {
    projects.value = res.data.idicators.map(p => p.project)

    series.value.push(
      {
        name: 'Planeado',
        type: 'bar',
        data: res.data.idicators.map(p => p.planned_progress),
        showBackground: true,
      },
      {
        name: 'real',
        type: 'bar',
        data: res.data.idicators.map(p => p.real_progress),
        showBackground: true,
      },
    )

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
          <GaugeGradeChart :key="showLineChart" title="CPI" :value="0.2" />
          <GaugeGradeChart :key="showLineChart" title="SPI" :value="0.5" />
        </div>
      </div>
      <div class="col-span-2 border border-gray-200 rounded-lg shadow-sm">
        <div class="flex justify-center items-center p-1 rounded-t-lg bg-blue-800 text-white">
          <h2 class="text-md font-semibold">PONDERADO</h2>
        </div>
        <div class="flex justify-center size-full">
          <GaugeGradeChart :key="showLineChart" title="CPI" :value="0.7" />
          <GaugeGradeChart :key="showLineChart" title="SPI" :value="1" />
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
        <SimpleScatterChart :key="showLineChart" title="Proyectos" />
      </div>
    </div>
  </main>
  <button type="button" @click="showLineChart++" class="border border-blue-500 rounded-lg p-2 hover:bg-blue-200">
    Actualizar Gráficos
  </button>
</template>
