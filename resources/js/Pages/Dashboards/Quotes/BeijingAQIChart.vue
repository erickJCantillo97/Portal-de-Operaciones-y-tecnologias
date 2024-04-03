<script>
import * as echarts from 'echarts/core'
import {
  TitleComponent,
  ToolboxComponent,
  TooltipComponent,
  GridComponent,
  VisualMapComponent,
  DataZoomComponent,
  MarkLineComponent,
} from 'echarts/components'
import { CanvasRenderer } from 'echarts/renderers'
import { LineChart } from 'echarts/charts'
import { UniversalTransition } from 'echarts/features'

echarts.use([
  TitleComponent,
  ToolboxComponent,
  TooltipComponent,
  GridComponent,
  VisualMapComponent,
  DataZoomComponent,
  MarkLineComponent,
  LineChart,
  CanvasRenderer,
  UniversalTransition,
])

export default {
  mounted() {
    this.initChart()
  },
  props: {
    series: Array
  },
  methods: {
    initChart() {
      const chartDom = document.getElementById('main')
      const myChart = echarts.init(chartDom)

      myChart.setOption(
        ({
          title: {
            text: 'Gráfica de Control (%)',
            left: '1%'
          },
          tooltip: {
            trigger: 'axis',
            formatter: '{b}: {c}%',
          },
          grid: {
            left: '5%',
            right: '15%',
            bottom: '10%'
          },
          xAxis: {
            data: this.projects,
          },
          yAxis: {
            label: {

            },
            formatter: '{b}: {c}%',
          },
          toolbox: {
            right: 10,
            feature: {
              dataZoom: {
                yAxisIndex: 'none'
              },
              restore: {},
              saveAsImage: {}
            }
          },
          visualMap: {
            top: 50,
            right: 10,
            pieces: [
              {
                gt: 0,
                lte: 5,
                color: '#00ab55' //light green
              },
              {
                gt: 15,
                lte: 100,
                color: '#DC2626' //red
              },
              {
                gt: -15,
                lte: -1,
                color: '#FFB32D' //orange
              },
              {
                gt: 5,
                lte: 15,
                color: '#FFB32D' //orange
              },
            ],
            outOfRange: {
              color: '#00ab55'
            }
          },
          series: [{
            label: {
              show: true,
              position: 'top',
              formatter: '{b}'
            },
            name: 'Gráfica de Control',
            type: 'line',
            data: this.variations,

            markLine: {
              silent: true,
              lineStyle: {
                color: '#333'
              },
              data: [
                {
                  yAxis: 50
                },
                {
                  yAxis: 100
                },
                {
                  yAxis: 150
                },
                {
                  yAxis: 200
                },
                {
                  yAxis: 300
                }
              ]
            }
          }]
        })
      )
    },
  },
  data() {
    return {
      projects: [
        'EMPUJADOR DE RÍO COTECMAR',
        'BARCAZA TANQUERA 1',
        'BARCAZA TANQUERA 2',
        'BARCAZA TANQUERA 3',
        'ARC BOTES SAFE 2019',
        'SUBAFIN 2019',
        'JOLA BDA URR',
        'EMBARCADOR ARAUCA',
        'BOYAS DIMAR',
        'ARC BOTES SAFE 2020',
        'BOTES BRF',
        'JOLA BOTE APOSTLE 41',
        'LANCHA DE PASAJEROS Y BOTE DE ACCIÓN INT',
        'FNGRD BOTE APOSTLE 41',
        'JOLA BOTE ARCÁNGEL',
        'FONDO PAZ BOTE TIPO C',
        'BCFBC FONSECON',
        'BCFBC FONSECON II',
        'FONSECON LANCHA POLICÍA'
      ],
      variations: [19, 13, 13, 13, 2, 16, 4, 0, 0, 1, 8, 1, 0, -4, 4, 2, 23, 13, 5]
    }
  }
}
</script>

<template>
  <div id="main" style="height: 400px" :series="series"></div>
</template>
