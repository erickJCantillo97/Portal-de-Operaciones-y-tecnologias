<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'
import Button from 'primevue/button'
import CustomDataTable from '@/Components/CustomDataTable.vue'
import CustomSlideOver from '@/Components/CustomSlideOver.vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    quotes: Array,
    gerencias: Array,
    ships: Array
})

const openSlideOver = ref(false)
const quote = ref({})

//#region columnas de CustomDataTable
const columnas = ref([
    // { field: 'id', header: 'Id', frozen: true, filter: true, sortable: true },
    { field: 'gerencia', header: 'Gerencia', filter: true, sortable: true },
    { field: 'customer', header: 'Cliente' },
    { field: 'consecutive', header: 'Consecutivo' },
    { field: 'version', header: 'Version', filter: false },
    { field: 'expeted_answer_date', header: 'Fecha maxima respuesta', type: 'date', filter: true, },
    { field: 'estimador_anaswer_date', header: 'Fecha respuesta', type: 'date', filter: true },
    { field: 'route', header: 'Ruta', filter: false },
    { field: 'file', header: 'Documento', filter: false },
    { field: 'observation', header: 'Observacion', filter: false },
    { field: 'created_at', header: 'Fecha creacion', filter: false },
])
//#endregion

//#region Botones de CustomDatatable
const buttons = ref([
    { event: 'showClic', severity: 'success', class: '', icon: 'fa-solid fa-eye', text: true, outlined: false, rounded: false },
    { event: 'deleteClic', severity: 'danger', icon: 'fa-solid fa-trash', class: '!h-8', text: true, outlined: false, rounded: false },
])
//#endregion

const showClic = (event, data) => {
    quote.value = data;
    openSlideOver.value = true
}

const deleteClic = (event, data) => {
    console.log(event)
}

const slideOver = () => {
    openSlideOver.value = true
}

</script>

<template>
    <AppLayout>
        <CustomDataTable :data="quotes" :columnas="columnas" :actions="buttons" @showClic="showClic"
            @deleteClic="deleteClic">
            <template #header>
                <h1 class="text-xl font-semibold leading-6 capitalize text-primary">
                    Estimaciones
                </h1>
                <Link :href="route('quotes.create')">
                <Button title="Agregar Estimación" severity="success" label="Agregar" outlined icon="fa-solid fa-plus"
                    class="!h-8" />
                </Link>
                <Button @click="slideOver()" title="Agregar Estimación" severity="success" label="Ver" outlined icon="fa-solid fa-plus"
                    class="!h-8" />
            </template>
        </CustomDataTable>

        <CustomSlideOver :quote="quote" :openSlideOver="openSlideOver" @closeSlideOver="openSlideOver = false" />
    </AppLayout>
</template>
