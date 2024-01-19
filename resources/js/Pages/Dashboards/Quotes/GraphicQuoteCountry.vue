<script setup>
import { ref } from 'vue';
import { GChart } from 'vue-google-charts'


const loading = ref(false)
const empty = ref(false)
const chartData = ref([
    ['country', 'Ofertas'],
])
const getData = () => {
    loading.value = true
    axios.get(route("get.quotes.country")).then((res) => {
        if (res.data.values.length == 0) {
            empty.value = true
        } else {
            for (var values of res.data.values)
                chartData.value.push([values[0], parseInt(values[1])])
            empty.value = false
        }
        loading.value = false
    });
}
getData()


const chartOptions = ref({
    height: "300",
    colorAxis: { colors: ['rgb(234,241,255)', 'rgb(46, 48, 146)'] }
})

</script>

<template>
    <GChart :settings="{
        packages: ['geochart']
    }" type="GeoChart" :data="chartData" :options="chartOptions" />
</template>
