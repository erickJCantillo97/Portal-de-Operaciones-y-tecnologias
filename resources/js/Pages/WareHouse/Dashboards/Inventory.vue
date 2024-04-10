<script setup>
import CustomDataTable from '@/Components/CustomDataTable.vue';
import { ref } from 'vue';



const loading = ref(false)
const categories = ref([])

const getToolsCategories = () => {
    loading.value = true
    axios.get(route("get.tool.cateories")).then((res) => {
        categories.value = Object.values(res.data.tools);
        loading.value = false
    });
}

const columnas = [
    { field: 'name', header: 'Nombre', filter: true },
    { field: 'value', header: 'Cantidad' },
]

getToolsCategories()

</script>

<template>
    <div class="h-[35vh]">
        <CustomDataTable :data="categories" title="Inventario" :loading="loading" :showColumns="false" :paginator="true"
            :columnas="columnas" :rowsDefault="5">
        </CustomDataTable>
    </div>
</template>