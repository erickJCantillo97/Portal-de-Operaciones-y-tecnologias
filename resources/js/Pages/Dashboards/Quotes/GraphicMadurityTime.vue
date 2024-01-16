<script setup>
import { ref } from 'vue';
import axios from "axios";
import VueApexCharts from 'vue3-apexcharts';
import Empty from '@/Components/Empty.vue';
import Loading from '@/Components/Loading.vue';
import Card from 'primevue/card';

const series = ref([])
const loading = ref(true)
const empty = ref()
const chartOptions = ref({
    title: {
        text: 'Tiempo promedio por madurez',
        align: 'center',
        margin: 0
    },
    dataLabels: {
        enabled: true,
        offsetY: -20,
        style: {
            fontSize: '12px',
            colors: ["#304758"]
        },
        formatter: function (val) {
            return val + " dias";
        },
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
                return parseInt(value) + ' dias'
            },
        }
    }
})
const getMaduriTime = () => {
    loading.value = true
    axios.get(route("get.avg.manurities")).then((res) => {
        if (res.data.values.length == 0) {
            empty.value = true
        } else {
            series.value = [{
                'name': 'Cantidad',
                'data': res.data.values
            }]
            chartOptions.value.xaxis.categories = res.data.maturities;
            empty.value = false
        }
        loading.value = false
    });
}
getMaduriTime()


</script>
<template>
    <Loading v-if="loading"></loading>
    <Empty v-else-if="empty" message="Aun sin tiempos promedios que mostrar"></Empty>

    <VueApexCharts v-else type="bar" :options="chartOptions" height="200" :series="series" />
</template>
