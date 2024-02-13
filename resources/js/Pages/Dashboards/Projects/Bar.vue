<script setup>

import { ref } from 'vue'
import VChart from 'vue-echarts'
import "echarts";


const option = ref({
  title: {
    text: 'World Population'
  },
  tooltip: {
    trigger: 'axis',
    axisPointer: {
      type: 'shadow'
    }
  },

  legend: {},
  xAxis: {
    type: 'value',
    boundaryGap: [0, 0.01]
  },
  yAxis: {
    type: 'category',
    data: ['Brazil', 'Indonesia',]
  },
  series: [
    {
      name: '2011',
      type: 'bar',
      data: [18203, 289]
    },
    {
      name: '2012',
      type: 'bar',
      data: [19325, 23]
    }
  ]
})
</script>
<style scoped>
.chart {
  height: 300px;
  background-color: #000;
}
</style>
<template>
  <v-chart class="chart" style="width: 500px;" :option="option" />
</template>
