<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'
import Button from 'primevue/button'
import CustomDataTable from '@/Components/CustomDataTable.vue'
import CustomSlideOver from '@/Components/CustomSlideOver.vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    quotes: Array,
})

const openSlideOver = ref(false)
const quote = ref({})

//#region columnas de CustomDataTable
const columnas = [
    // { field: 'id', header: 'Id', frozen: true, filter: true, sortable: true },
    { field: 'consecutive', header: 'Consecutivo', filter: true, sortable: true },
    { field: 'name', header: 'Nombre', filter: true, sortable: true },
    { field: 'clases', header: 'Clases', filter: true, sortable: true },
    { field: 'customer', header: 'Cliente', filter: true, sortable: true },
    {
        field: 'get_status', header: 'Estado', filter: true, sortable: true, type: 'customTag', severitys: [
            { text: 'Proceso', class: 'bg-primary text-white' },
            { text: 'Entregada', class: '' },
            { text: 'Pendiente por Firma', class: '' },
            { text: 'Firmada', class: 'bg-success text-white' },
            { text: 'No Firmada', class: 'bg-danger  text-white' },
            { text: 'Contratada', class: '' }
        ]
    },
    { field: 'expeted_answer_date', header: 'Fecha maxima respuesta', type: 'date', sortable: true, filter: true, },
    { field: 'estimador_anaswer_date', header: 'Fecha respuesta', type: 'date', sortable: true, filter: true },
    { field: 'route', header: 'Ruta', filter: false },
    { field: 'file', header: 'Documento', filter: false },
    { field: 'observation', header: 'Observacion', filter: false },
    { field: 'created_at', header: 'Fecha creacion', filter: false },
    { field: 'gerencia', header: 'Gerencia', filter: true, sortable: true },
]
//#endregion

//#region Botones de CustomDatatable
const buttons = [
    { event: 'showClic', severity: 'success', class: '', icon: 'fa-solid fa-eye', text: true, outlined: false, rounded: false },
    // { event: 'deleteClic', severity: 'danger', icon: 'fa-solid fa-trash', class: '!h-8', text: true, outlined: false, rounded: false },
]
//#endregion

//#region Botones de filtro de CustomDatatable
const filterButtons = [
    { field: 'status', label: 'En proceso', data: 'Proceso' },
    { field: 'status', label: 'Entregadas', data: 'Entregada' },
]
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
        <CustomDataTable :data="quotes" :columnas="columnas" :actions="buttons" :filterButtons="filterButtons"
            title="Estimaciones" @showClic="showClic" @deleteClic="deleteClic">
            <template #buttonHeader>
                <Link :href="route('quotes.create')">
                <Button title="Agregar Estimación" severity="success" label="Agregar" outlined icon="fa-solid fa-plus"
                    class="!h-8" />
                </Link>
            </template>
        </CustomDataTable>

        <CustomSlideOver :quote="quote" :show="openSlideOver" @closeSlideOver="openSlideOver = false" />
    </AppLayout>
</template>
