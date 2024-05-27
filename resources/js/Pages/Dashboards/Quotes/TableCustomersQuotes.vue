<script setup>
import { ref, onMounted } from 'vue';
import CustomDataTable from '@/Components/CustomDataTable.vue'

const customersQuotes = ref([])
const loading = ref(true)

onMounted(() => {
    getCustomersQuotes()
})

const columnas = [
    { field: 'cliente', header: 'Nombre Cliente' },
    { field: 'estimaciones', header: 'Cantidad' }
]

const getCustomersQuotes = () => {
    loading.value = true

    try {
        axios.get(route('get.quotes.customer'))
            .then((res) => {
                customersQuotes.value = res.data.values
                loading.value = false
            })
    } catch (error) {
        console.error(error)
    }
}
</script>
<template>
    <CustomDataTable :data="customersQuotes" title="Estimaciones por Clientes" :loading="loading" :showColumns="false"
        :paginator="false" :columnas="columnas" :filter="false">
    </CustomDataTable>
</template>
