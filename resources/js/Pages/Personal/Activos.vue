<script setup>
import { useCommonUtilities } from '@/composable/useCommonUtilities';
import '../../../sass/dataTableCustomized.scss';
import { FilterMatchMode } from 'primevue/api';
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline';
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Button from '../../Components/Button.vue';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import UserTable from '@/Components/UserTable.vue';
import CustomDataTable from '@/Components/CustomDataTable.vue';

const { currencyFormat, formatDate } = useCommonUtilities()

const props = defineProps({
    personal: Array
})

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
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
        <CustomDataTable class="w-full" exportRute="export.personal" :data="personal" :columnas :rows-default="100" />

    </AppLayout>
</template>
