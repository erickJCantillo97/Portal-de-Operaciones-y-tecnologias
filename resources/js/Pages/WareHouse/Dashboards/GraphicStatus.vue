<script setup>
import { ref, onMounted } from 'vue';
import Empty from '@/Components/Empty.vue';
import Loading from '@/Components/Loading.vue';
import VueApexCharts from 'vue3-apexcharts';

const series = ref([])
const loading = ref(true)
const empty = ref()

const chartOptions = ref({
    title: {
        text: 'Herramientas por estado',
        align: 'center',
        margin: 0
    },
    dataLabels: {
        enabled: true,
        offsetY: -20,
        style: {
            fontSize: '12px',
            colors: ["#304758"]
        }
    },
    yaxis: {
        labels: {
            show: false,
        }
    },
    xaxis: {
        position: 'bottom',
        axisBorder: {
            show: false
        },
        axisTicks: {
            show: false
        },
        crosshairs: {
            fill: {
                type: 'gradient',
                gradient: {
                    colorFrom: '#D8E3F0',
                    colorTo: '#BED1E6',
                    stops: [0, 100],
                    opacityFrom: 0.4,
                    opacityTo: 0.5,
                }
            }
        },
    },
    tooltip: {
        enabled: true,
        y: {
            formatter: function (value) {
                return parseInt(value)
            },
        }
    }
})
const getToolsStatues = () => {
    loading.value = true
    axios.get(route("get.tool.status")).then((res) => {
        const data = Object.values(res.data.statues)
        if (data.length == 0) {
            empty.value = true
        } else {
            series.value = [{
                'name': 'Cantidad',
                'data': data.map(tool => (parseInt(tool.value)))
            }]
            chartOptions.value.xaxis.categories = Object.values(res.data.statues).map(status => status.status);
            empty.value = false
        }
        loading.value = false
    });
}

onMounted(() => {
    getToolsStatues()
})
</script>
<template>
    <Loading v-if="loading"></loading>
    <Empty v-else-if="empty" message="Aun sin estados que mostrar"></Empty>
    <VueApexCharts v-else type="bar" :options="chartOptions" :series="series" height="300" />
</template>
