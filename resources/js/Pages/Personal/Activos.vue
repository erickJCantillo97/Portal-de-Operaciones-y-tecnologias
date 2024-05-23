<script setup>
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import CustomDataTable from '@/Components/CustomDataTable.vue';

const props = defineProps({
    personal: Array
})

const columnas = [
    { field: 'Nombres_Apellidos', header: 'Nombre', filter: true },
    { field: 'Cargo', header: 'cargo' },
    { field: 'Oficina', header: 'Departamento' },
    { field: 'Fecha_Final', header: 'Fin Contrato', type: 'date' },
    { field: 'Costo_Hora', header: 'Costo hora', type: 'currency' },
    { field: 'Costo_Mes', header: 'Costo Mes', type: 'currency' },
]

const url = [
    {
        ruta: 'personal.activos',
        label: 'Personal Activos',
        active: true
    }
]
</script>
<template>
    <AppLayout :href="url">
        <div class="w-full h-full">
            <CustomDataTable class="w-full" exportRute="export.personal" :data="personal" :columnas :rows-default="100" />
        </div>
    </AppLayout>
</template>
