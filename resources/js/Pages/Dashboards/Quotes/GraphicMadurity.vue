<script setup>
import { ref } from 'vue';
import axios from "axios";
import VueApexCharts from 'vue3-apexcharts';
import Empty from '@/Components/Empty.vue';
import Loading from '@/Components/Loading.vue';

const series = ref([])
const loading = ref(true)
const empty = ref()
const chartOptions = ref({
    title: {
        text: 'Estimaciones por madurez',
        align: 'center',
        margin: 0
    },
    dataLabels: {
        enabled: true,
        formatter: function (val) {
            return parseInt(val) + " %"
        },
    },
    legend: {
        floating: true,
        position: 'right',
        horizontalAlign: 'right',
        fontSize: '14px',
        fontWeight: 400,
    },
})
const getQuotesMadurity = () => {
    loading.value = true
    axios.get(route("get.quotes.manurity")).then((res) => {
        if (res.data.maturities.length == 0) {
            empty.value = true
        } else {
            series.value = res.data.maturities.map(maturity => parseInt(maturity.value));
            chartOptions.value.labels = res.data.maturities.map(maturity => maturity.name);
            empty.value = false
        }
        loading.value = false
    });
}
getQuotesMadurity()


</script>
<template>
    <Loading v-if="loading"></loading>
    <Empty v-else-if="empty" message="Aun sin cantidades que mostrar"></Empty>
    <VueApexCharts type="pie" v-else :options="chartOptions" height="300" :series="series" />
</template>
