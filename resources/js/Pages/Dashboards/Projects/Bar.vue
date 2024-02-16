<script setup>
import { ref } from 'vue'
import VChart from 'vue-echarts'
import "echarts"

const props = defineProps({
  planeado: Number,
  real: Number
})

/**
 * @rgutierrez
 * String template

Valores formatter:

{a}: series name.
{b}: the name of a data item.
{c}: the value of a data item.
{@xxx}: the value of a dimension named 'xxx', for example, {@product} refers the value of 'product' dimension.
{@[n]}: the value of a dimension at the index of n, for example, {@[3]} refers the value at dimensions[3].
*/
const option = ref({
  tooltip: {
    // trigger: 'axis',
    axisPointer: {
      type: 'shadow'
    },
    trigger: 'axis',
    formatter: ((params) => {
      let tooltip = ''
      params.map((param) => {
        tooltip += param.name + '<br>' + param.marker + ' ' + param.seriesName + ': ' + Intl.NumberFormat().format(Number(param.value).toFixed(2)) + '%<br>'
      })
      return tooltip
    })
  },
  label: {
    show: true,
    position: 'right',
    valueAnimation: true,
    formatter: ((val) => {
      return new Intl.NumberFormat().format(Number(val.value).toFixed(2)) + '%'
    })
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
