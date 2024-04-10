<script setup>
import CustomDataTable from '@/Components/CustomDataTable.vue';
import Loading from '@/Components/Loading.vue';
import OverlayPanel from 'primevue/overlaypanel';
import { ref } from 'vue';



const loading = ref(false)
const categories = ref([])
const totalByStatues = ref([])
const loadingStatus = ref(false)
const getToolsCategories = () => {
    loading.value = true
    axios.get(route("get.tool.cateories")).then((res) => {
        categories.value = Object.values(res.data.tools);
        loading.value = false
    });
}

const op = ref();
const showStatus = (event, data) => {
    op.value.toggle(event);
    loadingStatus.value = true
    axios.get(route("get.total.status.categories", data.category_id)).then((res) => {
        totalByStatues.value = Object.values(res.data.tools);
        loadingStatus.value = false
    });
}

const columnas = [
    { field: 'name', header: 'Nombre', filter: true },
    // { field: 'value', header: 'Cantidad Total' },
    { field: 'value', header: 'Cantidad Tototal', class: 'w-24', rowclass: "underline", sortable: true, filter: true, type: 'button', event: 'showStatus', severity: 'info', text: true },
]

getToolsCategories()

</script>

<template>
    <div class="h-[35vh]">
        <CustomDataTable :data="categories" title="Inventario" :loading="loading" :showColumns="false" :paginator="true"
            :columnas="columnas" :rowsDefault="5" @showStatus="showStatus">
        </CustomDataTable>
    </div>

    <!--OVERLAYPANEL-->
    <OverlayPanel ref="op">
        <ul class="list-none p-0 m-0 flex flex-col gap-2 w-40 max-h-52 overflow-y-auto">
            <Loading v-if="loadingStatus" />
            <div v-else>
                <div v-for="t in totalByStatues"
                    class="flex w-full justify-between align-middle items-center border p-2 rounded-md">
                    <div class="text-sm font-medium text-gray-900">{{ t.estado }}S</div>
                    <div class="text-sm bg-primary p-1 px-2 text-white font-semibold rounded-md">{{ t.value }}</div>
                </div>
            </div>

        </ul>
    </OverlayPanel>
</template>