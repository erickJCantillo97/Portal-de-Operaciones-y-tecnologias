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
    <div v-if="rate > 0" :key="rate"
        :class="[change > 0 ? 'bg-green-100 text-primary' : 'bg-red-100 text-red-800', 'inline-flex items-center cursor-default rounded-full']">
        <div class="flex flex-col items-center justify-center pl-3">
            <p class="text-xs leading-4 text-gray-900">TRM Hoy</p>
            <p class="text-sm font-semibold leading-4 text-primary">${{ rateT.valor }} </p>
        </div>
        <div>
            <ArrowUpIcon v-if="change > 0" class="self-center flex-shrink-0 w-5 h-5 text-green-500" aria-hidden="true" />
            <ArrowDownIcon v-else class="self-center flex-shrink-0 w-5 h-5 text-red-500" aria-hidden="true" />
        </div>
    </div>
</template>
