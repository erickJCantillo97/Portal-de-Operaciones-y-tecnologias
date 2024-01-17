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
        text: 'Estimaciones por estado',
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
const getQuotesMadurity = () => {
    loading.value = true
    axios.get(route("get.quotes.status.week")).then((res) => {
        const data = Object.values(res.data)
        if (data.length == 0) {
            empty.value = true
        } else {

            series.value = [{
                'name': 'Cantidad',
                'data': data.map(quote => (parseInt(quote.value)))
            }]
            chartOptions.value.xaxis.categories = Object.values(res.data).map(status => status.status + '(s)');
            empty.value = false
        }
        loading.value = false
    });
}
getQuotesMadurity()


</script>
<template>
    <Loading v-if="loading"></loading>
    <Empty v-else-if="empty" message="Aun sin estados que mostrar"></Empty>
    <VueApexCharts v-else type="bar" :options="chartOptions" :series="series" height="300" />
</template>
