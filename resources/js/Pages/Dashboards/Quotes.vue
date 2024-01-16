<script setup>
import GraphicMadurity from '@/Pages/Dashboards/Quotes/GraphicMadurity.vue'
import CustomInput from '@/Components/CustomInput.vue';
import { ref } from 'vue';
import TableQuotes from '@/Pages/Dashboards/Quotes/TableQuotes.vue';
import GraphicStatus from '@/Pages/Dashboards/Quotes/GraphicStatus.vue';
import GraphicPersonsVsMadurity from './Quotes/GraphicPersonsVsMadurity.vue';

const prueba = ref()
const prueba2 = ref()
</script>


<template>
    <div class="grid grid-cols-2">
        <TableQuotes class="" />
        <GraphicMadurity class="" />
        <GraphicStatus />
        <GraphicPersonsVsMadurity />
    </div>
</template>
