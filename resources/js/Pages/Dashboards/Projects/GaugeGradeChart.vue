<script setup>
import * as echarts from 'echarts/core'
import { ref } from 'vue'
import { GaugeChart } from 'echarts/charts'
import { CanvasRenderer } from 'echarts/renderers'
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

echarts.use([GaugeChart, CanvasRenderer])

const option = ref({
  width: 1,
  height: 1,
  series: [
    {
      type: 'gauge',
      startAngle: 180,
      endAngle: 0,
      center: ['50%', '75%'],
      radius: '90%',
      min: 0,
      max: 2,
      splitNumber: 4,
      axisLine: {
        lineStyle: {
          width: 6,
          color: [
            [0.25, '#FF6E76'],
            [0.49, '#FDDD60'],
            [1, '#7CFFB2']
          ]
        }
      },
      pointer: {
        icon: 'path://M12.8,0.7l12,40.1H0.7L12.8,0.7z',
        length: '12%',
        width: 10,
        offsetCenter: [0, '-30%'],
        itemStyle: {
          color: 'inherit'
        }
      },
      axisTick: {
        length: 2,
        lineStyle: {
          color: 'inherit',
          width: 2
        }
      },
      splitLine: {
        length: 10,
        lineStyle: {
          color: 'inherit',
          width: 2
        }
      },
      axisLabel: {
        color: '#464646',
        fontSize: 12,
        distance: -30,
        rotate: 'tangential',

      },
      title: {
        offsetCenter: [0, '30%'],
        fontSize: 15
      },
      detail: {
        fontSize: 14,
        offsetCenter: [0, 0],
        formatter: function (value) {
          return new Intl.NumberFormat().format(Number(value).toFixed(2))
        },
        valueAnimation: true,
        color: 'inherit'
      },
      toolbox: {
        show: true,
        showTitle: false,
        feature: {
          saveAsImage: {
            show: true,
            title: 'Save As Image'
          },
          dataView: {
            show: true,
            title: 'Data View'
          }
        }
      },
      data: [
        {
          value: 0.8,
          name: 'CPI'
        }
      ]
    }
  ]
})
</script>
<style scoped>
.chart {
  height: 150px;
  width: 100%;
}
</style>
<template>
  <v-chart class="chart" :option="option"></v-chart>
</template>