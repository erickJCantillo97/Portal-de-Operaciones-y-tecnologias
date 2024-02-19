<script setup>
import { ref } from 'vue'
import Bar from '@/Pages/Dashboards/Projects/Bar.vue'
import GaugeGradeChart from '@/Pages/Dashboards/Projects/GaugeGradeChart.vue'
import S_Curve from '@/Pages/Dashboards/Projects/S_Curve.vue'
import MiniCardInfo from '@/Components/MiniCardInfo.vue'

const showLineChart = ref(0)

</script>
<template>
  <main>
    <div class="grid grid-cols-4 gap-2 mb-2">
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
  </main>
  <Bar :key="showLineChart" />
  <!-- <S_Curve :project="project.id" :key="showLineChart" /> -->
  <button type="button" @click="showLineChart++" class="border border-blue-500 rounded-lg p-2 hover:bg-blue-200">
    Actualizar Gráficos
  </button>
</template>
