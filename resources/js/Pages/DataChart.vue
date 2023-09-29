<script setup>
import { ref, onMounted } from 'vue'
import { router, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { FilterMatchMode, FilterOperator } from 'primevue/api';
import { MagnifyingGlassIcon, PencilIcon, TrashIcon } from '@heroicons/vue/20/solid';
import Button from '@/Components/Button.vue';
import PieChart from './PieChart.vue';
import '../../sass/dataTableCustomized.scss';

onMounted(() => {
    initFilters();
    getContracts();
    // contractsList();
})

//Obtener Contratos por API Routes
const getContracts = () => {
    try {
        axios.get(route('getContracts')).then((res) => {
            contracts.value = res.data.contracts
            selectedContracts.value = res.data.contracts
            contractsList();
        })
    } catch (error) {
        console.error('Error al obtener contratos:', error);
    }
}

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});

const initFilters = () => {
    filters.value = {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS },
        name: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
    }
};

const clearFilter = () => {
    initFilters();
};

const onRowSelect = (event) => {
    datos.value = []
    series.value = []
    // Agregar el contrato seleccionado a selectedContracts si no está presente
    if (!selectedContracts.value.find((contract) => contract.id === event.data.id)) {
        selectedContracts.value.push(event.data)
    }
    contractsList()
};

const onRowUnselect = (event) => {
    datos.value = []
    series.value = []
    // console.log("Seleccionado " + event.data.id)
    selectedContracts.value = selectedContracts.value.filter((contract) => contract.id !== event.data.id)
    // console.log(selectedContracts.value)
    contractsList()
    // console.log(datos.value.length)
}

const formatCurrency = (value) => {
    return parseFloat(value).toLocaleString('es-CO', {
        style: 'currency',
        currency: 'COP',

    });
};

const title = ref({
    text: 'Contratos',
    subtext: '',
    left: 'center'
})

//Variables Reactivas
const contracts = ref()
const selectedContracts = ref([]);

//Carga datos
const showGraph = ref(0)
const datos = ref([])
const series = ref([])

const contractsList = () => {
    showGraph.value++;
    selectedContracts.value.forEach((contract) => {
        let a = {
            value: contract.cost / 1000000,
            name: contract.name,
            label: {
                formatter: '{b|{b}}{abg|}\n{hr|}\n  ${c}M  {per|{d}%}  ',
                backgroundColor: '#F6F8FC',
                borderColor: '#8C8D8E',
                borderWidth: 1,
                borderRadius: 4,
                rich: {
                    a: {
                        color: '#6E7079',
                        lineHeight: 22,
                        align: 'center'
                    },
                    hr: {
                        borderColor: '#8C8D8E',
                        width: '100%',
                        borderWidth: 1,
                        height: 0
                    },
                    b: {
                        align: 'center',
                        color: '#6E7079',
                        fontSize: 10,
                        fontWeight: 'bold',
                        lineHeight: 15
                    },
                    c: {
                        fontWeight: 'bold',
                    },
                    per: {
                        color: '#fff',
                        backgroundColor: '#4C5058',
                        padding: [3, 4],
                        borderRadius: 4
                    }
                }
            },
        }
        datos.value.push(a)
        // legends.value.push(contracts.name)
    })
    series.value.push({
        type: 'pie',
        radius: '65%',
        center: ['50%', '50%'],
        selectedMode: 'single',
        data: datos.value
    })
}
</script>
<template>
    <!--DATATABLE CONTRATOS-->
    <div class="grid grid-cols-1 p-3 m-1 border-2 border-blue-100 sm:grid-cols-1 md:grid-cols-2 rounded-xl">
        <DataTable id="tabla" stripedRows class="p-datatable-sm" @rowUnselect="onRowUnselect" @rowSelect="onRowSelect"
            :value="contracts" v-model:selection="selectedContracts" v-model:filters="filters" dataKey="id"
            filterDisplay="menu" :loading="loading" :selectAll="false"
            :globalFilterFields="['name', 'gerencia', 'start_date', 'end_date', 'hoursPerDay', 'daysPerWeek', 'daysPerMonth']"
            currentPageReportTemplate=" {first} al {last} de {totalRecords}"
            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
            :paginator="true" :rows="10" :rowsPerPageOptions="[10, 25, 50, 100]">

            <template #header>
                <div class="flex justify-between w-full h-8 mb-2 ">
                    <div class="flex space-x-4">
                        <div class="w-8" title="Filtrar Contratos">
                            <Button @click="clearFilter()" type="button" severity="primary" class="hover:bg-primary ">
                                <i class="pi pi-filter-slash" style="color: 'var(--primary-color)'"></i>
                            </Button>
                        </div>

                        <div class="relative flex rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <MagnifyingGlassIcon class="w-5 h-4 text-gray-400" aria-hidden="true" />
                            </div>
                            <input type="search" title="Buscar Contrato"
                                class="block w-10/12 py-4 pl-10 text-gray-900 border-0 rounded-md ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                v-model="filters.global.value" placeholder="Buscar..." />
                        </div>
                    </div>
                </div>
            </template>

            <!--COLUMNAS-->
            <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
            <Column field="name" header="Contrato"></Column>
            <Column field="cost" header="Costo">
                <template #body="slotProps">
                    {{ formatCurrency(slotProps.data.cost) }}
                </template>
            </Column>
            <Column field="cost" header="Porcentaje"></Column>
            <Column field="end_date" header="Fecha Finalización"></Column>
        </DataTable>
        <div class="ml-1">
            <div class="max-w-full p-3 m-1 border-2 border-blue-100 rounded-xl md:max-w-full md:border-2 md:rounded-xl">
                <!--:key="showGraph" permite Re renderizar un componente hijo-->
                <PieChart :title="title" :series="series" :key="showGraph"></PieChart>
                {{ showGraph }}
            </div>
        </div>
    </div>
</template>