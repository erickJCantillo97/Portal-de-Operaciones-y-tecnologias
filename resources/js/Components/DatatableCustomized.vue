<script setup>
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import DownloadExcelIcon from '@/Components/DownloadExcelIcon.vue';
import '../../sass/dataTableCustomized.scss';
import { FilterMatchMode, FilterOperator } from 'primevue/api';
import { ref, onMounted } from 'vue'

defineProps({
    user: Array,
    modelName: String,

    columns: {
        required: true,
        type: Array,
    },

    height: {
        default: '500px',
        type: String
    },
    editItem: {
        type: Boolean,
        default: false
    },
    deleteItem: {
        type: Boolean,
        default: false
    },

    loading: {
        default: false,
        type: Boolean
    },
    globalSearch: {
        default: true,
        type: Boolean
    }
})

const statuses = ref(['unqualified', 'qualified', 'new', 'negotiation', 'renewal', 'proposal']);
const loading = ref(false);
const usuarios = ref()
const first = ref(0)
const filters = ref()

const initFilters = () => {
    filters.value = {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS },
        name: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
    }
};

initFilters();

const clearFilter = () => {
    initFilters();
};

const formatDate = (value) => {
    return value.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};

const exportarExcel = () => {
    //console.log(dt.value)
    // Acquire Data (reference to the HTML table)
    var table_elt = document.getElementById("tabla");

    var workbook = XLSX.utils.table_to_book(table_elt);

    var ws = workbook.Sheets["Sheet1"];
    XLSX.utils.sheet_add_aoa(ws, [["Creado " + new Date().toISOString()]], { origin: -1 });

    // Package and Release Data (`writeFile` tries to write and save an XLSB file)
    XLSX.writeFile(workbook, props.modelName + reunion.fechaInicio + ".xlsb");
};
</script>

<template>
    <div class="px-2">
        <DataTable id="tabla" stripedRows class="p-datatable-sm" :value="user" tableStyle="min-width: 50rem"
            v-model:filters="filters" dataKey="id" filterDisplay="menu" :loading="$props.loading"
            :globalFilterFields="['id', 'name', 'email']" currentPageReportTemplate=" {first} al {last} de {totalRecords}"
            paginatorTemplate=" CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
            :paginator="true" :rows="10" :rowsPerPageOptions="[10, 25, 50, 100]">
            <!-- <template #header>
                    DatatableCustom
                </template> -->

            <template #header>
                <div class="flex justify-between w-full">
                    <div class="flex space-x-4">
                        <Button icon="pi pi-filter-slash" size="small" outlined raised @click="clearFilter()" />
                        <span class="p-input-icon-left" v-if="globalSearch">
                            <i class="pi pi-search" />
                            <InputText v-model="filters['global'].value" placeholder="Busqueda..." size="small" />
                        </span>
                    </div>
                    <div class="w-auto justify-content-end space-between">
                        <Button label="Crear" icon="pi pi-plus" severity="success" raised rounded size="small" />
                        <Button severity="success" size="small" outlined @click="exportarExcel()">
                            <span class="mx-2 font-semibold">Exportar</span>
                            <DownloadExcelIcon />
                        </Button>
                        <!-- <Button icon="pi pi-download" severity="success" raised rounded size="small" @click="exportarExcel()" /> -->
                    </div>
                </div>
            </template>

            <!--COLUMNAS-->
            <Column field="id" header="ID"></Column>
            <Column field="name" header="Nombre"></Column>
            <Column field="email" header="Email"></Column>

            <!--ACCIONES-->
            <Column header="Acciones" class="space-x-3" v-if="editItem || deleteItem">
                <template #body="slotProps">
                    <!--BOTÓN EDITAR-->
                    <Button v-if="editItem" icon="pi pi-pencil" outlined rounded severity="primary" size="small" />

                    <!--BOTÓN ELIMINAR REUNIÓN-->
                    <Button v-if="deleteItem" icon="pi pi-trash" outlined rounded severity="danger" size="small" />

                </template>
            </Column>
        </DataTable>
    </div>
</template>
