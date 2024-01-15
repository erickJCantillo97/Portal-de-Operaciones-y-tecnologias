<script setup>
import TablaEstimaciones from '@/Pages/Dashboards/Estimaciones/TablaEstimaciones.vue'
import GraficaMadurez from '@/Pages/Dashboards/Estimaciones/GraficaMadurez.vue'
import GraficaEstado from '@/Pages/Dashboards/Estimaciones/GraficaEstado.vue'
</script>


<template>
    <div class="grid grid-cols-2">
        <TablaEstimaciones class="" />
        <GraficaMadurez class="" />
        <GraficaEstado />
    </div>
</template>
