<script setup>
import { onMounted, ref } from 'vue';
import { ArrowDownIcon, ArrowUpIcon } from '@heroicons/vue/20/solid'
import OverlayPanel from 'primevue/overlaypanel';
import VueApexCharts from 'vue3-apexcharts';
const rateT = ref()
const rateY = ref()
const change = ref()
const rate = ref(0)
const hist = ref(0)
const date = new Date().toLocaleDateString()
var categories = []
var data = []
var chartOptions
var series

const op = ref();
const toggle = (event) => {
    series = [{
        name: "Pesos",
        data: data
    }]
    chartOptions = {
        chart: {
            height: 60,
            type: 'area',
            toolbar: {
                show: false
            },
        },
        colors: ['#77B6EA', '#545454'],
        dataLabels: {
            enabled: false,
        },
        stroke: {
            width: 2,
            curve: 'straight'
        },
        title: {
            text: 'Historial USD',
            align: 'center'
        },
        grid: {
            show: false
        },
        xaxis: {
            categories: categories,
            type: 'datetime',
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
                show: false,
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
    hist.value++
    op.value.toggle(event);
}
const consultaTRM = async () => {
    if (date == localStorage.getItem('date')) {
        var datos
        rateT.value = JSON.parse(localStorage.getItem('rateT'))
        rateY.value = JSON.parse(localStorage.getItem('rateY'))
        change.value = localStorage.getItem('change')
        datos = JSON.parse(localStorage.getItem('hist'))
        categories = datos.map(obj => { return new Date(obj['vigenciahasta']).toDateString() }).reverse()
        data = datos.map(obj => { return obj['valor'] }).reverse()
        rate.value++
    } else {
        await axios.get('https://www.datos.gov.co/resource/mcec-87by.json?$limit=90').then((res) => {
            categories = res.data.map(obj => { return new Date(obj['vigenciahasta']).toDateString() }).reverse()
            data = res.data.map(obj => { return obj['valor'] }).reverse()
            localStorage.setItem('hist', JSON.stringify(res.data))
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
}


onMounted(() => {
    try {
        consultaTRM()
    } catch (error) {
        consultaTRM()
        console.log(error)
    }
    // var elemento = document.getElementById("trmdolar");
    // elemento.addEventListener("mouseenter", function () {
    //     // Simular un clic izquierdo sobre el elemento
    //     elemento.click();
    // });
})


</script>
<template>
    <div v-if="rate > 0" :key="rate" @click="toggle" id="trmdolar"
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
        content: '!p-0'
    }">
        <VueApexCharts :key="hist" type="area" height="180" :options="chartOptions" :series="series">
        </VueApexCharts>
    </OverlayPanel>
</template>
