<script setup>
import { ref, onMounted } from 'vue'
import CustomDataTable from '@/Components/CustomDataTable.vue';

const gerencias = ref([])
const loading = ref(true)

const getManagments = async () => {
    await axios.get(route('gerencias.index'))
        .then((res) => {
            gerencias.value = res.data[0]
            loading.value = false
        })
}

onMounted(() => {
    getManagments()
})

const columnsGerencias = [
    { field: 'name', header: 'Siglas', filter: true, sortable: true, },
    { field: 'descripcion', header: 'Nombre', filter: true, sortable: true, },
]
</script>
<template>
    <CustomDataTable :loading :rowsDefault="10" :data="gerencias" :columnas="columnsGerencias" title="Gerencias"
        :filter="false" :show-columns="false" />
</template>
