<script setup>

import { ref } from 'vue'
import VChart from 'vue-echarts'
import "echarts";


const option = ref({
  tooltip: {
    trigger: 'axis',
    axisPointer: {
      type: 'shadow'
    }
  },
  xAxis: {
    type: 'value',
  },
  yAxis: {
    type: 'category',
    data: ['avance']
  },
  series: [
    {
      name: 'planeado',
      type: 'bar',
      data: [40]
    },
    {
      name: 'Ejecutado',
      type: 'bar',
      data: [45]
    }
  ]
})
</script>
<style scoped>
.chart {
  height: 100px;
  width: 450px;
}
</style>
<template>
  <v-chart class="chart" :option="option" />
</template>
