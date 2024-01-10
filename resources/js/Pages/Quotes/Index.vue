<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'
import CustomDataTable from '@/Components/CustomDataTable.vue'
import CustomSlideOver from '@/Components/CustomSlideOver.vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    quotes: Array,
})

const openSlideOver = ref(false)
const quote = ref({})

//#region columnas de CustomDataTable

class Columms {
    /**
 * @gbuelvas
 * Construcctor de columnas

Valores Columms:
@param {string} field El nombre de la columna de la tabla.
@param {string} header El titulo que se mostrara en la cabecera de la columna.
*/
    constructor(field, header, filter, sortable) {
        this.field = field
        this.header = header
        this.filter = filter
        this.sortable = sortable
    }
}

const columnas = [
    {
        field: 'consecutive', header: 'Consecutivo', filter: true, sortable: true
    },
    { field: 'name', header: 'Nombre', filter: true, sortable: true },
    { field: 'clases', header: 'Clases', filter: true, sortable: true },
    { field: 'customer', header: 'Cliente', filter: true, sortable: true },
    { field: 'estimador', header: 'Estimador', filter: true, sortable: true },
    {
        field: 'get_status', header: 'Estado', filter: true, sortable: true, type: 'tag', filtertype: 'EQUALS',
        severitys: [
            { text: 'Proceso', severity: 'primary', class: 'bg-primary text-white' },
            { text: 'Entregada', severity: 'warning', class: '' },
            { text: 'Pendiente por Firma', severity: 'warning', class: '' },
            { text: 'Firmada', severity: 'success', class: 'bg-success text-white' },
            { text: 'No Firmada', severity: 'danger', class: 'bg-danger  text-white' },
            { text: 'Contratada', severity: 'success', class: '' }
        ]
    },
    { field: 'total_cost', header: 'Precio', type: 'currency', sortable: true, filter: true, },
    { field: 'expeted_answer_date', header: 'Fecha esperada', type: 'date', sortable: true, filter: true, },
    { field: 'estimador_anaswer_date', header: 'Fecha respuesta', type: 'date', sortable: true, filter: true },
    { field: 'route', header: 'Ruta', filter: false },
    { field: 'file', header: 'Documento', filter: false },
    { field: 'observation', header: 'Observacion', filter: false },
    { field: 'created_at', header: 'Fecha creacion', filter: false },
    { field: 'gerencia', header: 'Gerencia', filter: true, sortable: true },
]
//#endregion

//#region Botones de CustomDatatable
// const buttons = [
//     { event: 'showClic', severity: 'success', class: '', icon: 'fa-solid fa-eye', text: true, outlined: false, rounded: false },
//     // { event: 'deleteClic', severity: 'danger', icon: 'fa-solid fa-trash', class: '!h-8', text: true, outlined: false, rounded: false },
// ]
//#endregion

//#region Botones de filtro de CustomDatatable
const filterButtons = [
    { field: 'get_status', label: 'En proceso', data: 'Proceso', severity: 'primary' },
    { field: 'get_status', label: 'Entregadas', data: 'Entregada', severity: 'warning' },
    { field: 'get_status', label: 'Firmadas', data: 'Firmada', severity: 'success' },
    { field: 'get_status', label: 'Pendiente', data: 'Pendiente por Firma', severity: 'warning' },
]
//#endregion

const showClic = (event) => {
    quote.value = event.data;
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
        <div class="h-[85vh] overflow-y-auto">
            <CustomDataTable :data="quotes" :columnas="columnas" :filterButtons="filterButtons" title="Estimaciones"
                @rowClic="showClic">
                <template #buttonHeader>
                    <Link :href="route('quotes.create')">
                    <Button title="Agregar Estimación" severity="success" label="Agregar" outlined icon="fa-solid fa-plus"
                        class="!h-8" />
                    </Link>
                </template>
            </CustomDataTable>
            <CustomSlideOver :quote="quote" :show="openSlideOver" @closeSlideOver="openSlideOver = false" />
        </div>
    </AppLayout>
</template>
