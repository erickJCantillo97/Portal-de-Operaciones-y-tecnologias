<script setup>
import DataEstimatorCard from '@/Pages/Dashboards/Quotes/DataEstimatorCard.vue';
import GraphicMadurity from '@/Pages/Dashboards/Quotes/GraphicMadurity.vue'
import GraphicMadurityTime from './Quotes/GraphicMadurityTime.vue';
import GraphicQuoteCountry from './Quotes/GraphicQuoteCountry.vue';
import GraphicStatus from '@/Pages/Dashboards/Quotes/GraphicStatus.vue';
import TableQuotes from '@/Pages/Dashboards/Quotes/TableQuotes.vue';
import TableCustomersQuotes from '@/Pages/Dashboards/Quotes/TableCustomersQuotes.vue';
import TableCountriesQuotes from '@/Pages/Dashboards/Quotes/TableCountriesQuotes.vue';
import BeijingAQIChart from './Quotes/BeijingAQIChart.vue';
</script>

<template>
    <div class="p-2 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3 space-y-4">
        <GraphicQuoteCountry class="col-span-1  h-full" v-if="true" />
        <GraphicMadurity class="" />
        <GraphicStatus class="" />
        <GraphicMadurityTime class="" />
        <DataEstimatorCard class="" />
        <TableQuotes />
        <TableCustomersQuotes class="shadow-lg col-span-1 md:col-span-2 mt-4" />
        <TableCountriesQuotes class="shadow-lg col-span-1 md:col-span-2 mt-4" />
        <BeijingAQIChart class="w-full col-span-3 shadow-md" />

        <!-- <BeijingAQIChart class="w-full col-span-3 shadow-md"></BeijingAQIChart> -->


    </div>
</template>
