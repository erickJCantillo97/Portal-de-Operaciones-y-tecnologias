<script setup>
import { ref } from 'vue';
import axios from "axios";
import VueApexCharts from 'vue3-apexcharts';

const series = ref([])
const loading = ref(true)
const chartOptions = ref({
    title: {
        text: 'Estimaciones por madurez',
        align: 'center',
        margin: 0
    },
    dataLabels: {
        enabled: true,
        formatter: function (val) {
            return val + "%"
        },
    }
})
const getQuotesMadurity = () => {
    loading.value = true
    axios.get(route("get.quotes.manurity")).then((res) => {
        series.value = res.data.maturities.map(maturity => parseInt(maturity.value));
        chartOptions.value.labels = res.data.maturities.map(maturity => maturity.name);
        loading.value = false
    });
}
getQuotesMadurity()


</script>
<template>
    <VueApexCharts type="pie" v-if="!loading" :options="chartOptions" :series="series" width="380" class="flex justify-center" />
</template>
