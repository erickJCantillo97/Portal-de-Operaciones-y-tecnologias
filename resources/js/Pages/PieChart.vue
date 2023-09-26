<script setup>
import { use } from 'echarts/core';
import { CanvasRenderer } from 'echarts/renderers';
import { PieChart } from 'echarts/charts';
import {
    TitleComponent,
    TooltipComponent,
    LegendComponent,
} from 'echarts/components';
import VChart from 'vue-echarts';
import { ref, provide } from 'vue';

defineProps({
    title: {
        type: String,
        default: null
    },
    value: {
        type: String,
        default: 'text'
    },
    label: {
        type: String,
        default: 'text'
    },
    enabled: {
        type: Boolean,
        default: true
    }
});

use([
    TitleComponent,
    TooltipComponent,
    PieChart,
    CanvasRenderer,
]);

const option = {
    title: {
        text: 'Referer of a Website',
        subtext: 'Fake Data',
        left: 'center'
    },
    tooltip: {
        trigger: 'item',
        formatter: '{b}: ({d}%)'
    },
    series: [
        {
            type: 'pie',
            data: [
                {
                    value: 335,
                    name: 'Apple',
                    label: {
                        show: true,
                        position: 'middle',
                        formatter: '{b}: ({d}%)'
                    }
                },
                {
                    value: 310,
                    name: 'Grapes',
                    label: {
                        show: true,
                        position: 'middle',
                        formatter: '{b}: ({d}%)'
                    }
                },
                {
                    value: 234,
                    name: 'Pineapples',
                    label: {
                        show: true,
                        position: 'middle',
                        formatter: '{b}: ({d}%)'
                    }
                },
                {
                    value: 135,
                    name: 'Oranges',
                    label: {
                        show: true,
                        position: 'middle',
                        formatter: '{b}: ({d}%)'
                    }
                },
                {
                    value: 1548,
                    name: 'Bananas',
                    label: {
                        show: true,
                        position: 'middle',
                        formatter: '{b}: ({d}%)'
                    }
                }
            ]
        }
    ]
};
</script>
<style scoped>
.chart {
    height: 100vh;
}
</style>
<template>
    <div>
        <v-chart class="chart" :option="option" autoresize />
    </div>
</template>
