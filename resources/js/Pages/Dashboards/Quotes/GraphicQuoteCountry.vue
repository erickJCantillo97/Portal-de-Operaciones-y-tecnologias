<script setup>
import { ref } from 'vue';
import { GChart } from 'vue-google-charts'


const loading = ref(false)
const empty = ref(false)
const getData = () => {
    loading.value = true
    axios.get(route("get.quotes.country")).then((res) => {
        if (res.data.values.length == 0) {
            empty.value = true
        } else {
            for (var values of res.data.values)
                chartData.value.push([values[0], parseInt(values[1])])
            console.log(chartData.value)
            empty.value = false
        }
        loading.value = false
    });
}
getData()

const chartData = ref([
    ['country', 'Ofertas'],
])

const chartOptions = ref({
    chart: {
        title: 'Company Performance',
        subtitle: 'Sales, Expenses, and Profit: 2014-2017',
    },
    height: "300"

})

</script>

<template>
    <GChart :settings="{
        packages: ['geochart']
    }" type="GeoChart" :data="chartData" :options="chartOptions" />
</template>
