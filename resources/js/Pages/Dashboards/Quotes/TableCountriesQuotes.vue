<script setup>
import { ref, onMounted } from 'vue'
import CustomDataTable from '@/Components/CustomDataTable.vue'

const countriesQuotes = ref([])
const loading = ref(true)

onMounted(() => {
    getCountriesQuotes()
})

const columnas = [
    { field: 'country', header: 'País' },
    { field: 'value', header: 'Cantidad' }
]

const getCountriesQuotes = () => {
    loading.value = true

    try {
        axios.get(route('get.quotes.country'))
            .then((res) => {
                countriesQuotes.value = res.data.values.map((value) => ({
                    country: value[0],
                    value: value[1]
                }))
                loading.value = false
            })
    } catch (error) {
        console.error(error)
    }
}
</script>
<template>
    <CustomDataTable :data="countriesQuotes" title="Estimaciones por Países" :loading="loading" :showColumns="false"
        :paginator="false" :columnas="columnas" :filter="false">
    </CustomDataTable>
</template>
