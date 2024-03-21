<script setup>
import "echarts"
import { ref } from 'vue'
import VChart from 'vue-echarts'

const props = defineProps({
  series: {
    type: Array,
    default: null
  },
  title: {
    type: String,
    required: true
  }
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
  title: {
    text: props.title,
  },
  legend: {
    orient: 'horizontal',
    height: '5%',
  },
  grid: {
    left: '0%',
    containLabel: true
  },
  label: {
    show: true,
    position: 'right',
    valueAnimation: true,
    formatter: (val => {
      return new Intl.NumberFormat().format(Number(val.value).toFixed(2)) + '%'
    })
  },
  xAxis: {
    type: 'value',
    max: 100,
    position: 'bottom',
    splitLine: {
      lineStyle: {
        type: 'dashed'
      }
    },
  },
  yAxis: {
    show: false,
    type: 'category',

    data: ['Avance']
  },
  series:
    props.series
  // {
  //   name: 'Planeado',
  //   type: 'bar',
  //   data: [props.planeado],
  //   showBackground: true,
  // },
  // {
  //   name: 'Ejecutado',
  //   type: 'bar',
  //   showBackground: true,
  //   data: [props.real]
  // }

})
</script>
<style scoped>
.chart {
  height: 32vh;
  width: 100%;
}
</style>
<template>
  <v-chart class="chart" :option="option" />
</template>
