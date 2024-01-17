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
    colors: ['rgb(46, 48, 146)', '#546E7A', '#13d8aa', '#A5978B'],
    dataLabels: {
        enabled: true,
        textAnchor: 'start',
        style: {
            colors: ['#fff']
        },
        formatter: function (val, opt) {
            console.log(opt.w.globals);
            return opt.w.globals.labels[opt.dataPointIndex] + ":  " + val
        },
        offsetY: 0,
        offsetX: 0,
        dropShadow: {
            enabled: true
        },
        dataLabels: {
            position: 'bottom'
        },
    },
    yaxis: {
        labels: {
            show: false,
        }
    },
    plotOptions: {
        bar: {
            borderRadius: 8,
            horizontal: true,
            distributed: true,
            barHeight: '40%',
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

    },
    tooltip: {
        enabled: false,

    }
})
const getMaduriTime = () => {
    loading.value = true
    axios.get(route("get.avg.manurities")).then((res) => {
        if (res.data.values.length == 0) {
            empty.value = true
        } else {


            // series.value.data = [400, 430, 448, 470, 540, 580, 690, 1100, 1200, 1380]
            series.value = [{
                'data': res.data.values
            }
            ]
            chartOptions.value.xaxis.categories = res.data.maturities
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
    <VueApexCharts v-else type="bar" :options="chartOptions" height="300" :series="series" />
</template>
