<script setup>
import { ref, onMounted } from 'vue';
import '/resources/sass/dataTableCustomized.scss';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { FilterMatchMode, FilterOperator } from 'primevue/api';
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline';
import Button from '@/Components/Button.vue';

const loading = ref(false);
const groups = ref([])
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
})



onMounted(() => {
    initFilters();
    loading.value = true;
    axios.get(route('baseActivities.index')).then(
        (res) => {
            groups.value = res.data[0]
            loading.value = false;
        }
    );
})

const initFilters = () => {
    filters.value = {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS },
        name: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
    }
};

const clearFilter = () => {
    initFilters();
};

</script>

<template>
    <div class="px-auto  w-full">
        <DataTable id="tabla" stripedRows class="p-datatable-sm" :value="groups" scroll-height="300px"
            v-model:filters="filters" dataKey="id" filterDisplay="menu" :loading="loading"
            :globalFilterFields="['name', 'descripcion']" currentPageReportTemplate=" {first} al {last} de {totalRecords}"
            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
            :paginator="true" :rows="10" :rowsPerPageOptions="[10, 25, 50, 100]">

            <template #header>
                <div class="flex justify-between w-full h-8 mb-2">
                    <div class="flex space-x-4">
                        <div class="w-8">
                            <Button @click="clearFilter()" type="button" severity="primary" class="hover:bg-primary ">
                                <i class="pi pi-filter-slash" style="color: 'var(--primary-color)'"></i>
                            </Button>
                        </div>

                        <div class="relative flex rounded-md shadow-sm">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <MagnifyingGlassIcon class="h-4 w-5  text-gray-400" aria-hidden="true" />
                            </div>
                            <input type="search"
                                class="block w-10/12 rounded-md border-0 py-4 pl-10 text-gray-900 ring-1  ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                v-model="filters.global.value" placeholder="Buscar..." />
                        </div>
                    </div>
                </div>
            </template>

            <!--COLUMNAS-->
            <Column field="name" header="Codigo"></Column>
            <Column field="descripcion" header="Descripción"></Column>

        </DataTable>
    </div>
</template>
