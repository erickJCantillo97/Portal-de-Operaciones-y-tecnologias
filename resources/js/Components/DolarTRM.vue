<script setup>
import { ref } from 'vue';
import { ArrowDownIcon, ArrowUpIcon } from '@heroicons/vue/20/solid'
const rateT = ref()
const rateY = ref()
const change = ref()
const rate = ref(0)
axios.get('https://www.datos.gov.co/resource/mcec-87by.json?$limit=2').then((res) => {
    rateT.value = res.data[0]
    rateY.value = res.data[1]
    change.value = (rateT.value.valor - rateY.value.valor) / 100
    rate.value++
});

</script>
<template>
    <div v-if="rate > 0">
        <div :key="rate">
            <p class="text-xs whitespace-nowrap font-normal text-gray-900">TRM Hoy</p>
            <div class="block items-baseline text-xl font-semibold text-primary">
                <div class="flex whitespace-nowrap space-x-1">
                <p>${{ rateT.valor }} </p>
                <div
                    :class="[change > 0 ? 'bg-green-100 text-primary' : 'bg-red-100 text-red-800', 'inline-flex px-1 items-center rounded-full text-xs font-medium']">
                    <ArrowUpIcon v-if="change > 0" class="h-3 w-3 flex-shrink-0 self-center text-green-500"
                        aria-hidden="true" />
                    <ArrowDownIcon v-else class="h-3 w-3 flex-shrink-0 self-center text-red-500" aria-hidden="true" />
                    %{{ change.toFixed(2) }}
                </div>
            </div>
            <div>
                <!-- <p class="text-xs font-medium text-gray-500">Anterior ${{ rateY.valor }}</p> -->
            </div>
        </div>
    </div>
</div></template>
