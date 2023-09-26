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
        text: 'Proyectos',
        subtext: 'Cotecmar 2023',
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
                    value: 27,
                    name: 'MN ORINOCO II',
                    label: {
                        show: true,
                        position: 'middle',
                        formatter: '{b}: ({d}%)'
                    }
                },
                {
                    value: 2,
                    name: 'ARC 20 DE JULIO',
                    label: {
                        show: true,
                        position: 'middle',
                        formatter: '{b}: ({d}%)'
                    }
                },
                {
                    value: 83,
                    name: 'RM PUNTA BORINQUEN',
                    label: {
                        show: true,
                        position: 'middle',
                        formatter: '{b}: ({d}%)'
                    }
                },
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
        <v-chart class="chart" :option="option" :title="title" :tooltip="tooltip" :series="series" autoresize />
    </div>
</template>
