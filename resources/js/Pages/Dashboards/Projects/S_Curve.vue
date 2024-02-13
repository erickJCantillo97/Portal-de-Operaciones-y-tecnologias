<script setup>
import * as echarts from 'echarts/core'
import { ref } from 'vue'
import { use } from 'echarts/core'
import { CanvasRenderer } from 'echarts/renderers'
import { UniversalTransition } from 'echarts/features'
import { LineChart } from 'echarts/charts'
import { GridComponent } from 'echarts/components'
import VChart from 'vue-echarts'

const props = defineProps({
  title: {
    type: Object,
    default: null
  },
  series: {
    type: Array,
    default: null
  },
  legend: {
    type: Array,
    default: null
  }
})

echarts.use([
  GridComponent,
  LineChart,
  CanvasRenderer,
  UniversalTransition
])

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
  title: props.title,
  xAxis: {
    type: 'category',
    data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
  },
  yAxis: {
    type: 'value'
  },
  series: [
    {
      data: [150, 230, 224, 218, 135, 147, 260],
      type: 'line'
    }
  ]
})
</script>
<style scoped>
.chart {
  width: 18rem;
  height: 16.8rem;
  background-color: aqua;
  /* El gr4fico ocupa todo el espacio disponible */
}
</style>
<template>
  <v-chart height="300" width="300" :option="option" />
</template>
