<script setup>
import { ref, onMounted } from 'vue';
import GraphicMadurity from '@/Pages/Dashboards/Quotes/GraphicMadurity.vue'
import CustomInput from '@/Components/CustomInput.vue';
import TableQuotes from '@/Pages/Dashboards/Quotes/TableQuotes.vue';
import GraphicStatus from '@/Pages/Dashboards/Quotes/GraphicStatus.vue';
import DataEstimatorCard from '@/Pages/Dashboards/Quotes/DataEstimatorCard.vue';
import BeijingAQIChart from '@/Pages/Dashboards/Quotes/BeijingAQIChart.vue';
import GraphicMadurityTime from './Quotes/GraphicMadurityTime.vue';
import GraphicQuoteCountry from './Quotes/GraphicQuoteCountry.vue';

const prueba2 = ref()
</script>

<template>
    <div class="p-2 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3 space-y-4">
        <GraphicQuoteCountry class="shadow-md col-span-1 md:col-span-2 h-full" />
        <GraphicMadurity class="shadow-md" />
        <GraphicStatus class="shadow-md" />
        <GraphicMadurityTime class="shadow-md" />
        <!-- <DataEstimatorCard class="shadow-md" /> -->
        <DataEstimatorCard class="shadow-md rounded-b-md" />
        <TableQuotes class="shadow-md col-span-1 md:col-span-2 mt-4" />
        <BeijingAQIChart class="shadow-md col-span-1 md:col-span-2 mt-4" />
    </div>
</template>
