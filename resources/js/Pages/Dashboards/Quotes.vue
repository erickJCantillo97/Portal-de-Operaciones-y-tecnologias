<script setup>
import GraphicMadurity from '@/Pages/Dashboards/Quotes/GraphicMadurity.vue'
import CustomInput from '@/Components/CustomInput.vue';
import { ref } from 'vue';
import TableQuotes from '@/Pages/Dashboards/Quotes/TableQuotes.vue';
import GraphicStatus from '@/Pages/Dashboards/Quotes/GraphicStatus.vue';
import DataEstimatorCard from '@/Pages/Dashboards/Quotes/DataEstimatorCard.vue';
import GraphicMadurityTime from './Quotes/GraphicMadurityTime.vue';

const prueba = ref()
const prueba2 = ref()
</script>


<template>
    <div class="p-2 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3 space-y-4">
        <TableQuotes class="shadow-md xl:col-span-3 " />
        <GraphicMadurity class="shadow-md" />
        <GraphicStatus class="shadow-md" />
        <GraphicMadurityTime class="shadow-md" />
        <DataEstimatorCard class="shadow-md" />
    </div>
</template>
