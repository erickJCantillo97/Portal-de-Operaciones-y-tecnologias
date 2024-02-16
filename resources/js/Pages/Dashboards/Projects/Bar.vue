<script setup>
import { ref } from 'vue'
import VChart from 'vue-echarts'
import "echarts"
const props = defineProps({
  planeado: Number,
  real: Number
})


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
    max: 100,
    position: 'top',
    splitLine: {
      lineStyle: {
        type: 'dashed'
      }
    },

  },
  yAxis: {
    type: 'category',
    data: ['Avance']
  },
  series: [
    {

      name: 'Planeado',
      type: 'bar',
      data: [props.planeado],
      showBackground: true,
    },
    {
      name: 'Ejecutado',
      type: 'bar',
      showBackground: true,
      data: [props.real]
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
