<script setup>
import { ref } from 'vue';
import { GChart } from 'vue-google-charts'


const chartData = ref([
    ['Pais', 'Ofertas'],
    // ['Germany', 2],
    // ['United States', 3],
    // ['Brazil', 4],
    // ['Canada', 5],
    // ['COLOMBIA', 6],
    ['Andorra', 7],
])

const chartOptions = ref({
    chart: {
        title: 'Company Performance',
        subtitle: 'Sales, Expenses, and Profit: 2014-2017',
    },

})

</script>

<template>
    <GChart :settings="{
        packages: ['geochart'], language: 'es'
    }" type="GeoChart" :data="chartData" :options="chartOptions" />
</template>
