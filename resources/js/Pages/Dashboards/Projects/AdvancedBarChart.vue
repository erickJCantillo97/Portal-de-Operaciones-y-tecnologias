<script setup>
import "echarts"
import { ref } from 'vue'
import VChart from 'vue-echarts'

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
  legend: {
    orient: 'horizontal',
    height: '100%',
  },
  tooltip: {
    axisPointer: {
      type: 'shadow'
    },
    trigger: 'axis',
    formatter: (params => {
      let tooltip = '<p style="font-weight: bold; font-size: 0.8rem">' + params[0].name + '</p>'
      params.map(param => {
        tooltip += param.marker + ' ' + param.seriesName + ': '
          + Intl.NumberFormat().format(Number(param.value).toFixed(2)) + '%<br>'
      })
      return tooltip
    })
  },
  label: {
    show: true,
    // position: 'insdeLeft',
    verticalAlign: 'center',
    align: 'top',
    valueAnimation: true,
    formatter: (val => {
      return new Intl.NumberFormat().format(Number(val.value).toFixed(2)) + '%'
    })
  },
  grid: {
    left: '0%',
    right: '1%',
    bottom: '3%',
    containLabel: true
  },
  xAxis: {
    type: 'value',
    max: 100,
    min: 0,
    position: 'bottom',
    splitLine: {
      lineStyle: {
        type: 'dashed'
      }
    },
  },
  yAxis: {
    type: 'category',
    data: props.yAxisData,
    axisLabel: {
      fontWeight: 'bold',
      fontSize: 10
    },
  },
  series:
    props.series

})
</script>
<style scoped>
.chart {
  height: 40vh;
  width: 100%;
}
</style>
<template>
  <v-chart class="chart" :option="option" />
</template>
