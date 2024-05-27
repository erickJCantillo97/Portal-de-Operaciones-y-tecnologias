<script setup>
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import Button from 'primevue/button';
import CustomDataTable from '@/Components/CustomDataTable.vue';
import Dropdown from 'primevue/dropdown';

const columnas = [
    { field: 'consecutive', header: 'Consecutivo' },
    { field: 'name', header: 'Nombre' },
    { field: 'estimador', header: 'Estimador' },
    { field: 'total_cost', header: 'Precio', type: 'currency' },
]

const filters = [
    'Proceso',
    'Firmada',
    'Pendiente por Firma',
    'Contratada'
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

onMounted(() => {
    getQuotes()
})

const rowClic = (event) => {
    router.get(route('quotes.index'), { quote_id: event.data.id })
}
</script>
<template>
    <div class="shadow-md rounded-sm">
        <CustomDataTable @rowClic="rowClic" :data="quotes" title="Estimaciones" :loading="loading" :showColumns="false"
            :paginator="false" :columnas="columnas" :filter="false">
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
    </div>

</template>
