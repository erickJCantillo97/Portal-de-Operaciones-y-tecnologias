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
<template>
    <div v-tooltip.bottom="label">
        <div
            class="flex h-[50px] w-[60px] flex-col justify-center rounded-lg border border-[#e0e6ed] px-2 hover:bg-slate-100 hover:font-semibold sm:h-[40px] sm:w-[80px] dark:border-[#1b2e4b]">
            <h4
                class="truncate text-center text-xs font-bold text-[#3b3f5c] hover:font-bold sm:text-sm dark:text-white-dark">
                {{ label }}
            </h4>
            <count-up class="text-primary text-xl sm:text-2xl text-center" :startVal="0" :end-val="value" :duration="3">
            </count-up>
        </div>
    </div>
</template>
