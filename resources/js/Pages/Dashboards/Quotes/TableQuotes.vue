<script setup>
import CustomDataTable from '@/Components/CustomDataTable.vue';
import Button from 'primevue/button';
import { ref } from 'vue';
import axios from "axios";
import Dropdown from 'primevue/dropdown';
import Card from 'primevue/card';
const columnas = [
    { field: 'consecutive', header: 'Consecutivo' },
    { field: 'name', header: 'Nombre' },
    { field: 'estimador', header: 'Estimador' },
    { field: 'total_cost', header: 'Precio', type: 'currency' },
]
const filters = [
    'Proceso', 'Entregada', 'Firmada', 'Pendiente por Firma'
]
const filter = ref('Proceso')
const quotes = ref([])
const loading = ref(true)
const getQuotes = () => {
    loading.value = true
    axios.get(route("get.quotes.status", { status: filter.value })).then((res) => {
        quotes.value = Object.values(res.data.quotes);
        loading.value = false
    });
}
getQuotes()

</script>
<template>
    <CustomDataTable :data="quotes" title="Estimaciones" :loading="loading" :showColumns="false" :paginator="false"
        :columnas="columnas" :filter="false">
        <template #buttonHeader>
            <Dropdown v-model="filter" class="h-8" @change="getQuotes()" :options="filters" :pt="{
                root: '!ring-0 !border-0',
                token: '!p-0',
                item: ' !p-2',
                input: '!p-0'
            }">
                <template #value="slotProps">
                    <Button :label="slotProps.value" text></Button>
                </template>
            </Dropdown>
        </template>
    </CustomDataTable>
</template>
