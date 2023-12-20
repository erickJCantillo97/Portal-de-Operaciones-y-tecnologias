<script setup>
import { ref } from 'vue';
import { ArrowDownIcon, ArrowUpIcon } from '@heroicons/vue/20/solid'
import OverlayPanel from 'primevue/overlaypanel';
import VueApexCharts from 'vue3-apexcharts';
const rateT = ref()
const rateY = ref()
const change = ref()
const rate = ref(0)
var hist
const date = new Date().toLocaleDateString()
if (date == localStorage.getItem('date')) {
    rateT.value = JSON.parse(localStorage.getItem('rateT'))
    rateY.value = JSON.parse(localStorage.getItem('rateY'))
    change.value = localStorage.getItem('change')
    hist = JSON.parse(localStorage.getItem('hist'))
    rate.value++
} else {
    axios.get('https://www.datos.gov.co/resource/mcec-87by.json?$limit=5').then((res) => {
        hist = res.data
        localStorage.setItem('hist', JSON.stringify(hist))
        rateT.value = res.data[0].valor
        localStorage.setItem('rateT', JSON.stringify(rateT.value))
        rateY.value = res.data[1].valor
        localStorage.setItem('rateY', JSON.stringify(rateY.value))
        change.value = (rateT.value - rateY.value) / 100
        localStorage.setItem('change', change.value)
        localStorage.setItem('date', date)
        rate.value++
    })
};
console.log(hist)
const op = ref();
const toggle = (event) => {
    op.value.toggle(event);
}

const chartOptions = {
    chart: {
        height: 50,
        type: 'line',
        toolbar: {
            show: false
        }
    },
    colors: ['#77B6EA', '#545454'],
    dataLabels: {
        enabled: true,
    },
    stroke: {
        curve: 'smooth'
    },
    title: {
        text: 'Historial USD',
        align: 'center'
    },
    grid: {
        borderColor: '#e7e7e7',
        row: {
            colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
            opacity: 0.5
        },
    },
    xaxis: {
        categories: hist.map(obj => { return new Date(obj['vigenciahasta']).toLocaleDateString() }).reverse(),
        labels: {
            style: {
                colors: [],
                fontSize: '8px',
                fontFamily: 'Helvetica, Arial, sans-serif',
                fontWeight: 200,
                cssClass: 'apexcharts-xaxis-label',
            }
        }
    },
    yaxis: {
        labels: {
            style: {
                colors: [],
                fontSize: '8px',
                fontFamily: 'Helvetica, Arial, sans-serif',
                fontWeight: 200,
                cssClass: 'apexcharts-xaxis-label',
            }
        }
    },
}

const series = [
    {
        name: "Pesos",
        data: hist.map(obj => { return obj['valor'] }).reverse()
    },
]

</script>
<template>
    <div v-if="rate > 0" :key="rate" @click="toggle"
        :class="[change > 0 ? 'bg-green-100 text-primary' : 'bg-red-100 text-red-800', 'cursor-pointer inline-flex items-center rounded-full']">
        <div class="flex flex-col items-center justify-center pl-3">
            <p class="text-xs leading-4 text-gray-900">TRM Hoy</p>
            <p class="text-sm font-semibold leading-4 text-primary">${{ rateT }} </p>
        </div>
        <div>
            <ArrowUpIcon v-if="change > 0" class="self-center flex-shrink-0 w-5 h-5 text-green-500" aria-hidden="true" />
            <ArrowDownIcon v-else class="self-center flex-shrink-0 w-5 h-5 text-red-500" aria-hidden="true" />
        </div>
    </div>
    <OverlayPanel ref="op" :pt="{
        content: '!p-1'
    }">
        <VueApexCharts type="line" height="350" :options="chartOptions" :series="series"></VueApexCharts>
    </OverlayPanel>
</template>
