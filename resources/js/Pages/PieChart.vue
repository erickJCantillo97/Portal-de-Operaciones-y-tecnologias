<script setup>
import { ref } from 'vue'
import { use } from 'echarts/core'
import { CanvasRenderer } from 'echarts/renderers'
import { PieChart } from 'echarts/charts'
import {
    TitleComponent,
    TooltipComponent,
    LegendComponent,
    ToolboxComponent
} from 'echarts/components'
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

use([
    TitleComponent,
    TooltipComponent,
    PieChart,
    CanvasRenderer,
    LegendComponent,
    ToolboxComponent
])

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
    title: props.title,
    // tooltip: {
    //     trigger: 'item',
    //     formatter: '{b}: (${c} M)'
    // },
    series: props.series,
    // legend: {
    //     bottom: 10,
    //     left: 'center',
    //     data: props.legend
    // },
    toolbox: {
        show: true,
        feature: {
            mark: { show: true },
            dataView: { show: false, readOnly: false },
            restore: { show: false },
            saveAsImage: { show: true }
        }
    },
})
</script>
<style scoped>
.chart {
    height: 50vh
    /* El gráfico ocupa todo el espacio disponible */
}
</style>
<template>
    <div>
        <v-chart class="chart" :option="option" />
    </div>
</template>
