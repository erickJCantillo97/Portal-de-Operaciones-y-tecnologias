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
            text: 'Grafica De Control.',
            subtext: 'Variaciación porcentual costo directo estimado vs ejecutado'
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
          xAxis: [
            {
              show: false,
              data: this.projects,
            },
            {
              type: 'category',
              position: 'bottom',
              axisTick: {
                alignWithLabel: true
              },
              axisPointer: {
                label: {
                  formatter: function (params) {
                    return (
                      'Precipitation  ' +
                      params.value +
                      (params.seriesData.length ? ':' + params.seriesData[0].data : '')
                    );
                  }
                }
              },
              data: this.years
            }

          ],
          yAxis: {
            formatter: '{b}: {c}%',
            axisLabel: {
              formatter: '{value} %'
            },
          },
          toolbox: {
            right: 1,
            feature: {
              dataZoom: {
                yAxisIndex: 'none'
              },
              restore: {},
              saveAsImage: {}
            }
          },
          dataZoom: [
            {
              start: '2015-04-01',
              end: '2015-04-01',
              startValue: '2015-04-01',
              endValue: '2022-04-01',
            }
          ],
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
              position: 'bottom',
              formatter: '{b}',
              fontSize: 10,
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
    projectWithYears() {
      return this.projects.map(project => {
        if (project.includes('DISEÑO Y CONST EMPUJADOR DE RÍO COTECMAR')) {

          return `${project} \n2015`;
        } else if (
          project.includes('CTO 128/18 PART BARCAZA TANQUERA 1') ||
          project.includes('CTO 129/18 PART BARCAZA TANQUERA 2') ||
          project.includes('CTO 130/18 PART BARCAZA TANQUERA 3')
        ) {

          return `${project} \n2018`;
        } else if (project.includes('CTO 0064-ARC-CBN6-219 BOTES SAFE') ||
          project.includes('CTO 290-SUBAFIN-2019 BB')) {

          return `${project} \n2019`;
        } else if (project.includes('CTO 007-ARC-JOLA-2020 BDA URR') ||
          project.includes('CTO 0084-ARC-CBN6-2020 EMBAR. ARAUCA') ||
          project.includes('CTO INT 171-GINRED-2020 BOYAS DIMAR') ||
          project.includes('CTO INT 0158-ARC-CBN6-2020 BOTES SAFE') ||
          project.includes('CTO 004-ARC-JOLA-2020 BOTES BRF')) {

          return `${project} \n2020`;
        } else if (project.includes('CTO 003/21 JOLA BOTE APOSTLE 41') ||
          project.includes('CTO 9677/21 FNGRD BOTE APOSTLE 41') ||
          project.includes('CTO 003/21 JOLA BOTE ARCÁNGEL') ||
          project.includes('CTO 369/21 FONDO PAZ BOTE TIPO C')) {

          return `${project} \n2021`;
        } else if (project.includes('CTO 1104/22 BCFBC FONSECON') ||
          project.includes('BCFBC FONSECON II')) {

          return `${project} \n2022`;
        } else {
          return project;
        }
      });
    }
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
      variations: [19, 13, 13, 13, 2, 16, 4, 0, 0, 1, 8, 1, 0, -4, 4, 2, 23, 13, 5],
      years: [
        '2015',
        '2018',
        '2019',
        '2020',
        '2021',
        '2022'
      ]
    }
  }
}
</script>

<template>
  <div id="main" style="height: 400px" :series="series"></div>
</template>
