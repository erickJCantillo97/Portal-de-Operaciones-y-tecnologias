<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import '../../../sass/dataTableCustomized.scss';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { FilterMatchMode, FilterOperator } from 'primevue/api';
import { MagnifyingGlassIcon, PencilIcon, TrashIcon } from '@heroicons/vue/24/outline';
import Button from '../../Components/Button.vue';

const props = defineProps({
    personal: Array
})

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
})

const formatCurrency = (value) => {
    return parseFloat(value).toLocaleString('es-CO', {
        style: 'currency',
        currency: 'COP',
    });
};

function formatDate(date) {
    // Extraer año, mes y día
    var anho = date.slice(0, 4);
    var mes = date.slice(4, 6);
    var dia = date.slice(6, 8);

    // Formato de salida: dd/mm/aaaa
    return `${dia}/${mes}/${anho}`;
}

</script>

<template>
    <AppLayout>
        <div class="w-full overflow-y-auto custom-scroll p-2">
            <div class="flex items-center mx-2 mb-2">
                <div class="flex-auto">
                    <h1 class="text-xl font-semibold leading-6 capitalize text-primary">
                        Personal Activo
                    </h1>
                </div>
            </div>
            <!--DATATABLE-->
            <DataTable id="tabla" stripedRows class="p-datatable-sm" :value="personal" v-model:filters="filters"
                dataKey="id" filterDisplay="menu" :globalFilterFields="['Nombres_Apellidos', 'name', 'type', 'email']"
                currentPageReportTemplate=" {first} al {last} de {totalRecords}"
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                :paginator="true" :rows="10" :rowsPerPageOptions="[10, 25, 50, 100]">

                <template #header>
                    <div class="flex justify-between w-full h-8 mb-2">
                        <div class="flex space-x-4">
                            <div class="w-8" title="Filtrar Clientes">
                                <Button @click="clearFilter()" type="button" severity="primary" class="hover:bg-primary ">
                                    <i class="pi pi-filter-slash" style="color: 'var(--primary-color);'"></i>
                                </Button>
                            </div>

                            <div class="relative flex rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <MagnifyingGlassIcon class="w-5 h-4 text-gray-400" aria-hidden="true" />
                                </div>
                                <input type="search" title="Buscar Clientes"
                                    class="block w-10/12 py-4 pl-10 text-gray-900 border-0 rounded-md ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                    v-model="filters.global.value" placeholder="Buscar..." />
                            </div>

                        </div>
                        <div class="text-base italic mt-1 font-mono">
                            <strong>{{ personal.length }}</strong> Personas En Total
                        </div>
                    </div>
                </template>

                <!--COLUMNAS-->
                <Column field="Nombres_Apellidos" header="Nombre" sortable></Column>
                <Column field="Cargo" header="Cargo" sortable></Column>
                <Column field="Oficina" header="Departamento" sortable></Column>
                <Column field="Fecha_Final" header="Fin Contrato" sortable>
                    <template #body="slotProps">
                        {{ formatDate(slotProps.data.Fecha_Final) }}
                    </template>
                </Column>
                <Column field="Costo_Hora" header="Costo Hora" sortable>
                    <template #body="slotProps">
                        {{ formatCurrency(slotProps.data.Costo_Hora) }}
                    </template>
                </Column>
                <Column field="Costo_Mes" header="Costo Mes" sortable>
                    <template #body="slotProps">
                        {{ formatCurrency(slotProps.data.Costo_Mes) }}
                    </template>
                </Column>

            </DataTable>
        </div>
    </AppLayout>
</template>