<script setup>
import { CanvasRenderer } from 'echarts/renderers'
import { GridComponent } from 'echarts/components'
import { LineChart } from 'echarts/charts'
import { ref, onMounted } from 'vue'
import { UniversalTransition } from 'echarts/features'
import { use } from 'echarts/core'
import * as echarts from 'echarts/core'
import VChart from 'vue-echarts'

const props = defineProps({
  project: Number
})

const avance = ref([])
const planeado = ref([])
const labels = ref([])

onMounted(() => {
  axios.get(route('progressProjectWeek.get.data', {
    project: props.project
  })).then((res) => {
    labels.value = res.data.labels.map(x => "WK " + x)
    avance.value = res.data.ejecutado.map(x => x == 0 ? null : Number.parseFloat(x).toFixed(2))
    planeado.value = res.data.planeado.map(x => x == 0 ? null : Number.parseFloat(x).toFixed(2))
  })
})

echarts.use([
  GridComponent,
  LineChart,
  CanvasRenderer,
  UniversalTransition
])

const formatNumber = (value) => {
  const formattedValue = Number(value).toFixed(2)
  return isNaN(formattedValue) ? '-' : Intl.NumberFormat().format(formattedValue) + '%<br>'
}

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
    text: 'Curva S'
  },
  toolbox: {
    show: true,
    showTitle: true,
    feature: {
      saveAsImage: {
        how: true,
        title: 'Guardar como imagen'
      },
      dataZoom: {
        yAxisIndex: 'none'
      }
    }
  },
  tooltip: {
    trigger: 'axis',
    formatter: (params => {
      let tooltip = params[0].name + '<br>'

      params.map(param => {
        let setValue = formatNumber(param.value)
        tooltip += param.marker + ' ' + param.seriesName + ': ' + setValue
      })
      return tooltip
    })
  },
  xAxis: {
    type: 'category',
    boundaryGap: false,
    data: labels
  },
  yAxis: {
    show: false,
    type: 'value'
  },
  series: [
    {
      showSymbol: false,
      name: 'Planeado',
      data: planeado,
      type: 'line'
    },
    {
      showSymbol: false,
      areaStyle: {},
      name: 'Ejecutado',
      data: avance,
      type: 'line'
    }
  ],
  width: 300,
  height: 100,
})
</script>
<style scoped>
.chart {
  height: 12rem;
  width: 100%;
  /* El gr4fico ocupa todo el espacio disponible */
}
</style>
<template>
  <div class="chart">
    <v-chart :option="option" />
  </div>
</template>
