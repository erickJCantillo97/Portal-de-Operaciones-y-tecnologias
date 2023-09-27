<script setup>
import { use } from 'echarts/core';
import { CanvasRenderer } from 'echarts/renderers';
import { PieChart } from 'echarts/charts';
import {
    TitleComponent,
    TooltipComponent,
    LegendComponent,
    ToolboxComponent
} from 'echarts/components';
import VChart from 'vue-echarts';

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
});

use([
    TitleComponent,
    TooltipComponent,
    PieChart,
    CanvasRenderer,
    LegendComponent,
    ToolboxComponent
]);

const option = {
    title: props.title,
    tooltip: {
        trigger: 'item',
        formatter: '{b}: ({d}%)'
    },
    series: props.series,
    legend: {
        bottom: 10,
        left: 'center',
        data: props.legend
    },
    toolbox: {
    show: true,
    feature: {
      mark: { show: true },
      dataView: { show: true, readOnly: false },
      restore: { show: true },
      saveAsImage: { show: true }
    }
  },
};


</script>
<style scoped>
.chart {
    height: 50vh;
}
</style>
<template>
    <div>
        <v-chart class="chart" :option="option" />
    </div>
</template>
