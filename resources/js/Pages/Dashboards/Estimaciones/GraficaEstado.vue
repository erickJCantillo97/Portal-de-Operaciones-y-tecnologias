<script setup>
import { ref } from 'vue';
import axios from "axios";
import VueApexCharts from 'vue3-apexcharts';

const series = ref([])
const loading = ref(true)
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
    theme: {
        palette: 'palette1' // upto palette10
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
        series.value = [{
            'name': 'Cantidad',
            'data': data.map(quote => (parseInt(quote.value)))
        }]
        chartOptions.value.xaxis.categories = Object.values(res.data).map(status => status.status + '(s)');
        loading.value = false
    });
}
getQuotesMadurity()


</script>
<template>
    <VueApexCharts type="bar" v-if="!loading" :options="chartOptions" :series="series" width="380"
        class="flex justify-center" />
</template>
