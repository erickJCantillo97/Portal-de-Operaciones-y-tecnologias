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
  series: [
    {
      type: 'gauge',
      startAngle: 180,
      endAngle: 0,
      center: ['50%', '75%'],
      radius: '90%',
      min: 0,
      max: 1,
      splitNumber: 8,
      axisLine: {
        lineStyle: {
          width: 6,
          color: [
            [0.25, '#FF6E76'],
            [0.5, '#FDDD60'],
            [0.75, '#58D9F9'],
            [1, '#7CFFB2']
          ]
        }
      },
      pointer: {
        icon: 'path://M12.8,0.7l12,40.1H0.7L12.8,0.7z',
        length: '12%',
        width: 20,
        offsetCenter: [0, '-60%'],
        itemStyle: {
          color: 'auto'
        }
      },
      axisTick: {
        length: 12,
        lineStyle: {
          color: 'auto',
          width: 2
        }
      },
      splitLine: {
        length: 20,
        lineStyle: {
          color: 'auto',
          width: 5
        }
      },
      axisLabel: {
        color: '#464646',
        fontSize: 20,
        distance: -60,
        rotate: 'tangential',
        formatter: function (value) {
          if (value === 0.875) {
            return '0'
          } else if (value === 0.625) {
            return '0.8'
          } else if (value === 0.375) {
            return '0.8'
          } else if (value === 0.625) {
            return '0.8'
          } else if (value === 0.375) {
            return '0.8'
          } else if (value === 0.625) {
            return '0.8'
          } else if (value === 0.375) {
            return '0.4'
          } else if (value === 0.125) {
            return '0'
          }
          return ''
        }
      },
      title: {
        offsetCenter: [0, '-10%'],
        fontSize: 20
      },
      detail: {
        fontSize: 30,
        offsetCenter: [0, '-35%'],
        valueAnimation: true,
        formatter: function (value) {
          return Math.round(value * 100) + ''
        },
        color: 'inherit'
      },
      data: [
        {
          value: 0.01,
          name: 'SPI'
        }
      ]
    }
  ]
})
</script>
<style scoped>
.chart {
  width: 300px;
}
</style>
<template>
  <v-chart class="chart" :option="option"></v-chart>
</template>