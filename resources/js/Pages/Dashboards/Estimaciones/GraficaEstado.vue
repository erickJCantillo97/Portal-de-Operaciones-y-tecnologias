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
        formatter: function (val) {
            return val + "%";
        },
        offsetY: -20,
        style: {
            fontSize: '12px',
            colors: ["#304758"]
        }
    },
    xaxis: {
        position: 'top',
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
        tooltip: {
            enabled: true,
        }
    },
    yaxis: {
        axisBorder: {
            show: false
        },
        axisTicks: {
            show: false,
        },
        labels: {
            show: false,
            formatter: function (val) {
                return val + "%";
            }
        }

    }
})
const getQuotesMadurity = () => {
    loading.value = true
    axios.get(route("get.quotes.manurity")).then((res) => {
        series.value.name = 'Estimaciones'
        series.value.data = res.data.maturities.map(maturity => (parseInt(maturity.value)));
        chartOptions.value.xaxis.categories = res.data.maturities.map(maturity => maturity.name);
        loading.value = false
    });
}
getQuotesMadurity()


</script>
<template>
    <VueApexCharts type="bar" v-if="!loading" :options="chartOptions" :series="series" width="380"
        class="flex justify-center" />
</template>
