<script setup>
import { ref } from 'vue'
import VChart from 'vue-echarts'
import "echarts"

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  series: {
    type: Array,
    default: null
  },
  yAxisData: {
    type: Array,
    default: null
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
  legend: {},
  tooltip: {
    axisPointer: {
      type: 'shadow'
    },
    trigger: 'axis',
    formatter: (params => {
      let tooltip = ''
      console.log(params)
      params.map(param => {
        tooltip += param.marker + ' ' + param.seriesName
          + '<br /> CPI: ' + Intl.NumberFormat().format(Number(param.value[0]).toFixed(2))
          + '<br /> SPI: ' + Intl.NumberFormat().format(Number(param.value[1]).toFixed(2))
      })
      return tooltip
    })
  },
  xAxis: {
    max: 4,
    // splitLine: {
    //   lineStyle: {
    //     type: 'dashed'
    //   }
    // },
    // min: 0,
    // max: 1
  },
  yAxis: {
    // min: 0.970,
    max: 4
  },
  series: [
    {
      name: 'proyecto 1',
      symbolSize: 20,
      data: [
        [1.01, 1],
      ],
      type: 'scatter',

    },
    {
      name: 'proyecto 2',
      symbolSize: 20,
      data: [
        [2, 3.2],
      ],
      type: 'scatter'
    },
  ],
})
</script>
<style scoped>
.chart {
  height: 16rem;
  width: 100%;
}
</style>
<template>
  <v-chart class="chart" :option="option" />
</template>
