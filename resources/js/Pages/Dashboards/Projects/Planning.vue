<script setup>
import { ref, onMounted } from 'vue'
import AdvancedBarChart from '@/Pages/Dashboards/Projects/AdvancedBarChart.vue'
import GaugeGradeChart from '@/Pages/Dashboards/Projects/GaugeGradeChart.vue'
import SimpleScatterChart from '@/Pages/Dashboards/Projects/SimpleScatterChart.vue'
import MiniCardInfo from '@/Components/MiniCardInfo.vue'

const props = defineProps({

})

const projects = ref()
const projectsName = ref()

onMounted(() => {
  renderBarChart()
  getProjects()
})

const getProjects = () => {
  axios.get(route('projects.index')).then((res) => {
    projects.value = res.data.projects
    console.log(res.data.projects);
    projectsName.value = Object.values(res.data.projects.map(project => project.name))
  })
}

const showLineChart = ref(0)
const series = ref([])

showLineChart.value++

const renderBarChart = () => {
  series.value.push(
    {
      name: 'Planeado',
      type: 'bar',
      data: [18203, 12343],
      showBackground: true,
    },
    {
      name: 'Ejecutado',
      type: 'bar',
      data: [19325, 12343],
      showBackground: true,
    })
}
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
        <AdvancedBarChart :key="showLineChart" title="Avance Proyectos en Ejecución" :series="series"
          :yAxisData="projectsName" />
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
