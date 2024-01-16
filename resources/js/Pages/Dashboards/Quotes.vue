<script setup>
import GraphicMadurity from '@/Pages/Dashboards/Quotes/GraphicMadurity.vue'
import CustomInput from '@/Components/CustomInput.vue';
import { ref } from 'vue';
import TableQuotes from '@/Pages/Dashboards/Quotes/TableQuotes.vue';
import GraphicStatus from '@/Pages/Dashboards/Quotes/GraphicStatus.vue';
import DataEstimatorCard from '@/Pages/Dashboards/Quotes/DataEstimatorCard.vue';
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
        <DataEstimatorCard />
    </div>
    <!-- <div>
        <CustomInput v-model:input="prueba" floatLabel placeholder="Hola" :floatLabel="false" minFractionDigits="2"
            maxFractionDigits="4" type="number" identify="Prueba1" label="Prueba 1" />
        {{ prueba }}
        <CustomInput v-model:input="prueba2" help="Esto es una prueba" invalid errorMessage="Esto es una prueba de error"
            :floatLabel="false" type="text" identify="Prueba2" label="Prueba 2" />
        {{ prueba2 }}
    </div> -->
</template>
