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
  title: String,
  value: Number,
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
      radius: '100%',
      min: 0,
      max: 2,
      splitNumber: 4,
      axisLine: {
        lineStyle: {
          width: 10,
          color: [
            [0.25, '#FF6E76'],
            [0.49, '#FDDD60'],
            [1, '#7CFFB2']
          ]
        }
      },
      pointer: {
        icon: 'path://M12.8,0.7l12,40.1H0.7L12.8,0.7z',
        length: '20%',
        width: 16,
        offsetCenter: [0, '-30%'],
        itemStyle: {
          color: 'inherit'
        }
      },
      axisTick: {
        length: 3,
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
      data: [
        {
          value: props.value,
          name: props.title
        }
      ]
    }
  ]
})
</script>
<style scoped>
.chart {
  width: 100%;
}
</style>
<template>
  <div class="chart">
    <v-chart :option="option"></v-chart>
  </div>
</template>
