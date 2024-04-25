<template>
    <div v-tooltip.bottom="label">
        <div
            class="w-[60px] h-[50px]  sm:w-[60px] sm:h-[40px] shadow-md rounded-lg border border-[#e0e6ed] dark:border-[#1b2e4b] flex justify-center flex-col">
            <h4 class="text-[#3b3f5c] text-xs sm:text-sm   text-center dark:text-white-dark font-bold truncate">{{ label
                }}</h4>
            <count-up class="text-primary text-xl sm:text-2xl text-center" :startVal="0" :end-val="value"
                :duration="3"></count-up>
        </div>
    </div>
</template>

<script setup>
import CountUp from 'vue-countup-v3';

defineProps({
    value: {
        type: Number,
        default: 0,
    },
    label: {
        type: String,
        default: '',
    }
})
</script>