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
    type: 'scroll',
    orient: 'horizontal',
    right: 10,
    bottom: 1,
  },
  tooltip: {
    trigger: 'axis',
    formatter: (params => {
      let tooltip = ''
      params.map(param => {
        tooltip += '<hr style="margin-top:6px">' + param.marker + ' ' + param.seriesName
          + '<br /> CPI: ' + Intl.NumberFormat().format(Number(param.value[0]).toFixed(2))
          + '<br /> SPI: ' + Intl.NumberFormat().format(Number(param.value[1]).toFixed(2))
      })
      return tooltip
    }),
    axisPointer: {
      show: true,
      type: 'cross',
      lineStyle: {
        type: 'dashed',
        width: 1
      }
    }
  },
  brush: {},
  toolbox: {
    feature: {
      dataZoom: {},
      brush: {
        type: ['rect', 'polygon', 'clear']
      }
    }
  },
  xAxis: {
    name: 'CPI'
    // splitLine: {
    //   lineStyle: {
    //     type: 'dashed'
    //   }
    // },
    // min: 0,
    // max: 1
  },
  label: {
    backgroundColor: '#F6F8FC',
    borderColor: '#8C8D8E',
    borderWidth: 1,
    borderRadius: 4,
    rich: {
      a: {
        color: '#6E7079',
        lineHeight: 22,
        align: 'center'
      },

      b: {
        align: 'center',
        color: '#6E7079',
        fontSize: 10,
        fontWeight: 'bold',
        lineHeight: 15
      },
      c: {
        fontWeight: 'bold',
      },
      per: {
        color: '#fff',
        backgroundColor: '#4C5058',
        padding: [3, 4],
        borderRadius: 4
      }
    }
  },
  yAxis: {
    // min: 0.970,
    name: 'SPI',
  },
  series: props.series,
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
