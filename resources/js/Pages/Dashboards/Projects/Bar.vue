<script setup>
import { ref } from 'vue'
import VChart from 'vue-echarts'
import "echarts"

const option = ref({
  tooltip: {
    trigger: 'axis',
    axisPointer: {
      type: 'shadow'
    }
  },
  label: {
    show: true,
    position: 'right',
    valueAnimation: true,
    formatter: function (val) {
      return new Intl.NumberFormat().format(Number(val.value).toFixed(2)) + '%';
    }
  },
  xAxis: {
    type: 'value',
  },
  yAxis: {
    type: 'category',
    data: ['Avance']
  },
  series: [
    {
      name: 'Planeado',
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
  width: 100%;
}
</style>
<template>
  <v-chart class="chart" :option="option" />
</template>
