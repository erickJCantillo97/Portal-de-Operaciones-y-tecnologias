<script setup>
const { byteSizeFormatter, currencyFormat } = useCommonUtilities()
import { useCommonUtilities } from '@/composable/useCommonUtilities';
import { FilterMatchMode } from 'primevue/api'
import { onMounted, ref } from 'vue';
import ApplicationLogo from './ApplicationLogo.vue';
import ButtonGroup from 'primevue/buttongroup';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import Dropdown from 'primevue/dropdown';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputNumber from 'primevue/inputnumber';
import InputText from 'primevue/inputtext';
import Loading from './Loading.vue';
import MultiSelect from 'primevue/multiselect';
import Swal from 'sweetalert2';
import Tag from 'primevue/tag';
import InputSwitch from 'primevue/inputswitch';
import ConfirmDialog from 'primevue/confirmdialog';
import { useConfirm } from "primevue/useconfirm";
import { useToast } from "primevue/usetoast";
import Toast from 'primevue/toast';
import CustomModal from './CustomModal.vue';
import CustomInput from './CustomInput.vue';
import Sidebar from 'primevue/sidebar';

const confirm = useConfirm();
const toast = useToast();
const dt = ref()
const totalItems = ref();

const props = defineProps({
    data: {
        type: Array,
        default: []
    },
    routes: {
        type: Object,
        required: false
    },
    parameterData: {
        default: null
    },
    requestData: {
        type: Object,
        default: {}
    },
    changeRows: {
        type: Boolean,
        default: true
    },
    cacheName: {
        type: String,
        default: null
    },
    columnas: {
        type: Array,
        default: []
    },
    exportRute: {
        type: String,
        default: ''
    },
    actions: {
        type: Array,
        default: []
    },
    showAdd: {
        type: Boolean,
        default: false
    },
    filter: {
        type: Boolean,
        default: true
    },
    filterButtons: {
        type: Array,
        default: null
    },
    title: {
        type: String,
        default: ''
    },
    showColumns: {
        type: Boolean,
        default: true
    },
    paginator: {
        type: Boolean,
        default: true
    },
    loading: {
        type: Boolean,
        default: false
    },
    rowsDefault: {
        type: Number,
        defaul: 100
    },
    showHeader: {
        type: Boolean,
        default: true
    },
    selectionMode: {
        type: String,
        default: 'single'
    },
    showItem: {
        default: false,
        type: Boolean
    },
})

const dataResponse = defineModel('dataResponse', {
    required: false,
    type: Array,
    default: []
})
const selectAll = defineModel('selectAll')

defineEmits(['rowClick', 'buttonRowClick','addClick'])

const dataLoading = ref(false)

async function getData() {
    dataLoading.value = true
    await axios.get(route(props.routes.get, props.parameterData))
    .then((res) => {
        dataResponse.value = res.data
    })
    dataLoading.value = false
}
const selectedElement = ref([]);

onMounted(() => {
    rows.value = props.rowsDefault
    if (props.routes) {
        getData()
    }
})

//#region Filtros de tabla y visor columnas
const rows = ref()
const filters = ref({});
const globalFilterFields = ref([])
const columnasSelect = ref()
const filterOK = ref(false)

if (props.columnas.length > 7) {
    columnasSelect.value = props.columnas.slice(0, 7)
} else {
    columnasSelect.value = props.columnas
}

const initFilters = async () => {
    globalFilterFields.value = ['id']
    filters.value.global = { value: null, matchMode: FilterMatchMode.CONTAINS }
    for await (var columna of props.columnas) {
        if (columna.filter) {
            filters.value[columna.field] = { value: null, matchMode: FilterMatchMode[columna.filtertype ? columna.filtertype : 'CONTAINS'] }
            globalFilterFields.value.push(columna.field)
        }
    }
    filterOK.value = true
};

initFilters()

onMounted(() => {
    initFilters()
})

const getTotalStatus = (field, data) => {
    if (dataResponse) {
        return dataResponse.value.filter(obj => obj[field] == data).length
    }
    return props.data.filter(obj => obj[field] == data).length
}

const clearFilter = () => {
    initFilters();
};
//#endregion

//#region exportar
const exportar = () => {
    var fileLink = document.createElement('a');
    fileLink.href = route(props.exportRute);
    document.body.appendChild(fileLink);
    fileLink.click();
    Swal.fire({
        title: 'Exportando!',
        text: 'Se esta generando el archivo, se descargara pronto. No actualice la pagina',
        icon: 'success',
        showConfirmButton: false,
        timer: 4000,
    })
};
//#endregion

//#region formato de campo
const formatDate = (date) => {
    if (date == undefined || date == null) {
        return 'Sin definir'
    } else {
        var fecha = new Date(date).toLocaleString('es-CO',
            { day: '2-digit', month: '2-digit', year: 'numeric' })
        return fecha == '30/11/2' ? 'INDEFINIDO' : fecha;
    }
}
//#endregion

// #region crud



const modal = ref(false)
const visibleSidebar = ref(false)
const item = ref({
    type: null,
    data: {}
})
function deleteItem(event, data) {
    confirm.require({
        target: event.currentTarget,
        message: '¿Esta seguro de eliminar el registro?',
        icon: 'pi pi-exclamation-triangle text-danger',
        rejectClass: 'p-button-secondary p-button-outlined p-button-sm',
        acceptClass: 'p-button-sm p-button-danger',
        rejectLabel: 'Cancelar',
        acceptLabel: 'Eliminar',
        accept: () => {
            console.log(data)
            axios.delete(route(props.routes.delete, data.id))
                .then(async (res) => {
                    await getData()
                    toast.add({ severity: 'error', icon: 'fa-solid fa-trash-can', summary: '¡Accion realizada!', detail: 'Se elimino con exito', group: 'customTooltipDataTable', life: 5000 });
                })
                .catch((error) => {
                    console.log(error)
                    toast.add({ severity: 'error', summary: 'Error', detail: 'Error al eliminar', life: 3000 })
                })
        },
        reject: () => {
            // toast.add({ severity: 'error', summary: 'Rejected', detail: 'You have rejected', life: 3000 });
        }
    });
}
function open(event, data, type) {
    item.value.data.id = data.id
    props.columnas.forEach((column) => {
        item.value.data[column.field] = data[column.field]
    })
    item.value.type = type
    if (type == 'show') {
        visibleSidebar.value = true
    } else {
        modal.value = true
    }
}

async function editItem(item) {
    dataLoading.value = true
    await axios.post(route(props.routes.update, item.id), item)
        .then(async () => {
            await getData()
            modal.value = false
            toast.add({ severity: 'success', icon: 'fa-solid fa-floppy-disk', summary: '¡Accion realizada!', detail: 'Se guardaron los cambios', group: 'customTooltipDataTable', life: 5000 });
        })
        .catch((error) => {
            console.log(error)
            toast.add({ severity: 'error', summary: 'Error', detail: 'Error al guardar', group: 'customTooltipDataTable', life: 3000 })
        })
    dataLoading.value = false
}

async function addItem(item) {
    dataLoading.value = true
    await axios.post(route(props.routes.store), item)
        .then(async () => {
            await getData()
            modal.value = false
            toast.add({ severity: 'success', icon: 'fa-solid fa-floppy-disk', summary: '¡Accion realizada!', detail: 'Se agrego un registro', group: 'customTooltipDataTable', life: 5000 });
        })
        .catch((error) => {
            console.log(error)
            toast.add({ severity: 'error', summary: 'Error', detail: 'Error al guardar', group: 'customTooltipDataTable', life: 3000 })
        })
    dataLoading.value = false
}
// #endregion

</script>

<template>
    <DataTable ref="dt" id="tabla" :value="props.routes == null ? props.data : dataResponse"
        v-model:selection="selectedElement" :selectAll="selectAll"
        :paginator="(dataResponse.length > 0 || data.length > 0) && paginator" :rows :selectionMode sortMode="multiple"
        scrollable scrollHeight="flex" :loading="props.routes == null ? props.loading : dataLoading"
        currentPageReportTemplate="{first} al {last} de un total de {totalRecords}" removableSort
        v-model:filters="filters" stripedRows filterDisplay="menu" class="p-datatable-sm  p-1 rounded-md"
        stateStorage="session" :stateKey="cacheName ? 'dt-' + cacheName + '-state-session' : null"
        :globalFilterFields="globalFilterFields" @row-click="$emit('rowClick', $event)"
        paginatorTemplate="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink" :pt="{
        paginator: {
            paginatorWrapper: '!p-0',
            current: 'text-sm font-bold cursor-default !h-8 hidden sm:flex item-center',
            pagebutton: {
                class: '!font-bold !h-8 !rounded-md !w-6',
            },
            firstPageButton: '!h-8 !rounded-md',
            previousPageButton: '!h-8 !rounded-md',
            nextPageButton: '!h-8 !rounded-md',
            lastPageButton: '!h-8 !rounded-md'
        },
        loadingOverlay: '!bg-white'
    }
        ">
        <template #header>
            <div class="space-y-1 w-full">
                <span class="flex justify-between ">
                    <div class="flex space-x-3">
                        <p v-if="title"
                            class="text-xl h-ful flex items-center font-extrabold leading-6 mb-2 capitalize text-primary">
                            {{ title }}
                        </p>
                        <slot name="title" />
                    </div>
                    <span class="space-x-1">
                        <slot name="buttonHeader" />
                        <Button v-if="showAdd || routes?.store" v-tooltip.left="'Añadir'"
                            @click="routes?.store ? open($event, {}, 'new') : $emit('addClick', $event)"
                            severity="success" icon="fa-solid fa-plus" label="Agregar" outlined />
                    </span>
                </span>
                <div class="flex items-center w-full " :class="filter ? 'justify-between' : 'justify-end'"
                    v-if="showHeader">
                    <div class="h-8 flex gap-2 w-full" v-if="filter">
                        <div class="flex gap-2 w-min">
                            <Button v-tooltip.top="'Quitar filtros'" @click="clearFilter()" outlined
                                icon="fa-solid fa-filter-circle-xmark" />
                            <IconField iconPosition="left">
                                <InputIcon class="fa-solid fa-magnifying-glass" />
                                <InputText v-model="filters.global.value" type="search" size="small"
                                    placeholder="Buscar" />
                            </IconField>
                            <slot name="filterSpace" />
                        </div>
                        <ButtonGroup v-if="props.filterButtons && filterOK">
                            <Button v-for="button in props.filterButtons"
                                :label="button.label + ': ' + getTotalStatus(button.field, button.data)"
                                :severity=button.severity
                                @click="filters[button.field].value == button.data ? filters[button.field].value = null : filters[button.field].value = button.data"
                                :outlined="filters[button.field].value != button.data" icon="" />
                        </ButtonGroup>
                    </div>
                    <div class="space-x-2 hidden sm:flex items-center">
                        <Button v-if="selectionMode == 'multiple' && selectedElement.length > 0" v-tooltip.left="''"
                            @click="$emit('selectionAction', $event, selectedElement)" severity="primary"
                            label="Gestionar" />

                        <Button v-if="exportRute != ''" text @click="exportar" icon="fa-solid fa-download" :pt="{
        root: '!border-0 !ring-0',
        trigger: '!hidden',
        labelContainer: '!p-0 ',
        label: '!p-0 text-center',
        token: '!p-0', item: ' !p-2',
        header: '!p-2'
    }" class="w-8 h-8" />
                        <MultiSelect v-if="showColumns" v-model="columnasSelect" display="chip"
                            :options="props.columnas" optionLabel="header" placeholder="Selecciona columnas a mostrar"
                            class="w-min h-8" :pt="{
        root: '!border-0 !ring-0',
        trigger: '!hidden',
        labelContainer: '!p-0 ',
        label: '!p-0 text-center',
        token: '!p-0',
        item: ' !p-2',
        header: '!p-2'
    }
        ">
                            <template #value>
                                <Button v-tooltip="'Ver u ocultar columnas'" icon="fa-solid fa-eye" text class="!w-8" />
                            </template>
                        </MultiSelect>
                    </div>
                </div>
            </div>
        </template>

        <!-- #region ajustes de tabla -->
        <template #empty>
            <div class="flex flex-col items-center space-y-1">
                <ApplicationLogo class="w-32" />
                <p class="text-center font-bold italic">
                    No hay registros
                </p>
            </div>
        </template>
        <template #loading>
            <div class="flex justify-center">
                <Loading />
            </div>
        </template>
        <template #paginatorstart>
            <div class="flex items-center" v-if="changeRows">
                <Dropdown v-model="rows" :options="[1, 5, 10, 20, 50, 100]" :pt="{
        root: '!h-8 !border-0 !ring-0',
        input: '!py-0 !flex !items-center',
        item: '!p-1 w-full text-center'
    }
        " />
            </div>
        </template>
        <template #paginatorfirstpagelinkicon>
            <i class="fa-solid fa-angles-left"></i>
        </template>
        <template #paginatorprevpagelinkicon>
            <i class="fa-solid fa-angle-left"></i>
        </template>
        <template #paginatornextpagelinkicon>
            <i class="fa-solid fa-angle-right"></i>
        </template>
        <template #paginatorlastpagelinkicon>
            <i class="fa-solid fa-angles-right"></i>
        </template>
        <template #paginatorrowsperpagedropdownicon>
            <i class="fa-solid fa-angle-down"></i>
        </template>
        <!-- #endregion -->

        <!-- #region Columnas -->
        <Column :selectionMode v-if="selectionMode == 'multiple'" headerStyle="width: 3rem"></Column>
        <span v-for="col, index  in columnasSelect">
            <Column v-if="col.visible == null || col.visible == true" :field="col.field" :filterField="col.field"
                :class="col.class" :sortable="col.sortable" :show-filter-match-modes="false"
                :filterMenuStyle="{ width: '16rem' }" :frozen="col.frozen" :pt="{
        headerContent: { class: '!h-8' },
        headerCell: { class: '!p-0.5' }
    }
        ">
                <template #header>
                    <p class="text-sm text-primary uppercase font-extrabold truncate">{{ col.header }}</p>
                </template>
                <template #filtericon>
                    <i class="fa-solid fa-filter"></i>
                </template>
                <template #sorticon="{ sortOrder, sorted }">
                    <i :class="sorted ? sortOrder == 1 ? 'fa-solid fa-arrow-up-1-9' : 'fa-solid fa-arrow-up-9-1' : 'fa-solid fa-sort'"
                        class="text-gray-500 flex justify-center items-center ml-1 h-5 w-5"></i>
                </template>

                <template #filter="{ filterModel }" v-if="col.filter">
                    <input v-if="col.type == 'date'" class="w-full rounded-md p-column-filter" type="date"
                        v-model="filterModel.value" dateFormat="mm/dd/yy" placeholder="mm/dd/yyyy" mask="99/99/9999" />
                    <InputNumber v-else-if="col.type == 'number'" v-model="filterModel.value" class="p-column-filter"
                        placeholder="Numero a buscar" />
                    <Dropdown v-else-if="col.type == 'tag' || col.type == 'customtag'" v-model="filterModel.value"
                        :options="col.severitys.map(option => option.text)" placeholder="Selecciona una opcion"
                        class="p-column-filter w-full md:w-14rem" showClear />
                    <Dropdown v-else-if="col.filterType == 'dropdown'" :option-label="col.filterLabel"
                        :option-value="col.filterValue" v-model="filterModel.value" :options="col.filterOptions"
                        placeholder="Selecciona una opcion" class="p-column-filter w-full md:w-14rem" showClear />
                    <InputText v-else v-model="filterModel.value" type="text" class="p-column-filter"
                        placeholder="Escriba algo para buscar" />
                </template>

                <template #body="{ data }" v-if="col.field.indexOf('.') == -1">
                    <p v-if="col.type == 'date'" class="text-left">
                        {{ formatDate(data[col.field]) }}
                    </p>
                    <p v-else-if="col.type == 'currency'" class="text-right">
                        {{
        currencyFormat(data[col.field], !Array.isArray(data[col.field]) ? 'COP'
            : data[col.field][1]).trim()
    }}
                    </p>
                    <p v-else-if="col.type == 'customtag'"
                        :class="col.severitys.find((severity) => severity.text == data[col.field]).class"
                        class="text-center rounded-lg px-2 py-1">
                        {{ data[col.field] }}
                    </p>
                    <Tag v-else-if="col.type == 'tag'" class="w-full truncate" :title="data[col.field]"
                        :class="col.severitys.find((severity) => severity.text == data[col.field]).class"
                        :severity="col.severitys.find((severity) => severity.text == data[col.field]).severity"
                        :value="data[col.field]" />
                    <span v-else-if="col.type == 'object'" class="flex items-center space-x-2 w-full">
                        <img v-if="col.objectRows.photo"
                            :src="data[col.objectRows.photo.field] == null ? '/' : data[col.objectRows.photo.field]"
                            alt="Image" onerror="this.src='/svg/cotecmar-logo.svg'"
                            class="min-w-16 py-0.5 rounded-lg sm:h-12 sm:w-16 object-cover" draggable="false" />
                        <div>
                            <p class="font-bold text-sm" v-if="col.objectRows.primary">
                                {{ col.objectRows.primary.subfield ?
        data[col.objectRows.primary.field][col.objectRows.primary.subfield] :
        data[col.objectRows.primary.field] }}
                            </p>
                            <p class="text-xs italic" v-if="col.objectRows.secundary">
                                {{ col.objectRows.secundary.subfield ?
        data[col.objectRows.secundary.field][col.objectRows.secundary.subfield] :
        data[col.objectRows.secundary.field] }}
                            </p>
                        </div>
                    </span>
                    <span v-else-if="col.type == 'button'" class="w-full">
                        <Button :label="String(data[col.field])" class="w-full truncate" :class="col.rowClass"
                            :icon="col.icon" :outlined="col.outlined" :text="col.text" :severity="col.severity"
                            :rounded="col.rounded" @click="$emit('buttonRowClick', $event, data)">
                        </Button>
                    </span>
                    <span v-else-if="col.type == 'array'" class="w-full flex space-x-1">
                        <p v-for="item, index in data[col.field]"
                            :class="col.itemsClass.find((itemClass) => itemClass.text == item)?.class ?? col.itemClass">
                            {{ item }}
                        </p>
                    </span>
                    <span v-else-if="col.type == 'fileSize'">
                        {{ byteSizeFormatter(data[col.field]) }}
                    </span>
                    <span v-else-if="col.type == 'boolean'" class="flex items-center justify-center">
                        <InputSwitch v-model="data[col.field]" />
                    </span>
                    <p v-else class="">
                        {{ data[col.field] }}
                    </p>
                </template>
            </Column>
        </span>
        <Column frozen alignFrozen="right" class="w-[8%]"
            v-if="props.actions.length > 0 || routes?.update || routes?.delete || showItem">
            <template #body="{ data }">
                <div class="flex items-center justify-center bg-white rounded-md shadow-sm">
                    <span v-for="button in props.actions">
                        <Button @click="$emit(button.event, $event, data)" :text="button.text"
                            :severity="button.severity" :outlined="button.outlined" :rounded="button.rounded"
                            :icon="button.icon" :title="button.label" :class="button.class"
                            v-if="button.show == null ? true : button.show" />
                    </span>
                    <Button v-if="showItem" @click="open($event, data, 'show')" text icon="fa-solid fa-eye"
                        severity="success"></Button>
                    <Button v-if="routes?.update" @click="open($event, data, 'edit')" text icon="fa-solid fa-pencil"
                        severity="warning"></Button>
                    <Button v-if="routes?.delete" @click="deleteItem($event, data)" text icon="fa-solid fa-trash-can"
                        severity="danger"></Button>

                </div>
            </template>
        </Column>
        <!-- #endregion -->
    </DataTable>
    <ConfirmDialog group="customDeleteDataTable">
        <template #container="{ message, acceptCallback, rejectCallback }">
            <div class="flex flex-col items-center p-5 rounded-full">
                <i :class="message.icon" class="text-5xl p-4 rounded-full -mt-16 shadow-2xl"></i>
                <span class="font-bold text-3xl block mb-2 mt-4">{{ message.header }}</span>
                <p class="text-2xl">{{ message.message }}</p>
                <div class="flex items-center gap-4 mt-4">
                    <Button :icon="message.rejectIcon" :label="message.rejectLabel" @click="rejectCallback"
                        :class="message.rejectClass" />
                    <Button :icon="message.acceptIcon" :label="message.acceptLabel" @click="acceptCallback"
                        :class="message.acceptClass" />
                </div>
            </div>
        </template>
    </ConfirmDialog>
    <Toast group="customTooltipDataTable">
        <template #message="{ message }">
            <div class="flex items-center gap-3" style="flex: 1">
                <i :class="message.icon" class="text-4xl"></i>
                <div class="flex flex-col">
                    <span class="font-bold">{{ message.summary }}</span>
                    <div class="font-medium text-lg">{{ message.detail }}</div>
                </div>
            </div>
        </template>
    </Toast>
    <CustomModal v-model:visible="modal" icon="fa-solid fa-pencil"
        :titulo="item.type == 'edit' ? 'Editando registro' : 'Nuevo registro'">
        <template #body>
            <span v-if="!$slots.modal">
                <!-- <p class="rounded bg-warning text-white text-center">En pruebas</p> -->
                <div class="grid grid-cols-4 w-full gap-x-2">
                    <span v-for="col in columnas" :class="[col.input == false ? 'hidden' : 'w-full', col.input?.class]">
                        <CustomInput :disabled="dataLoading" v-if="col.input != false" :label="col.header"
                            v-model:input="item.data[col.field]" :acceptFile="col.input?.acceptFile"
                            :type="col.input?.type ?? 'text'" :multiple="col.input?.multiple"
                            :options="col.input?.options" :mode="col.input?.mode" :suffix="col.input?.suffix" />
                    </span>
                </div>
            </span>
            <slot name="modal" :item="item" />
            <slot name="modalAdd" :item="item" />
            <!-- {{ item }} -->
        </template>
        <template #footer>
            <Button severity="danger" :disabled="dataLoading" label="Cancelar" icon="fa-regular fa-circle-xmark"
                @click="modal = false" />
            <Button severity="success" :loading="dataLoading" label="Guardar" icon="fa-solid fa-floppy-disk"
                @click="item.type == 'edit' ? editItem(item.data) : addItem(item.data)" />
        </template>

    </CustomModal>
    <Sidebar v-model:visible="visibleSidebar" :showCloseIcon="false" position="right">
        <div v-if="!$slots.sidebar" class="flex flex-col gap-2">
            <span v-for="col in columnas" :class="item.data[col.field] ? 'block' : 'hidden'">
                <span v-if="col.input?.acceptFile?.includes('image')">
                    <div class="flex flex-col items-center border rounded-md p-2 gap-2">
                        <p class="font-bold text-center" :for="col.field"> {{ col.header }}</p>
                        <img :src="item.data[col.field]" alt="ImageShip" onerror="this.src='/svg/cotecmar-logo.svg'"
                            class="min-w-32 py-0.5  sm:w-16 object-cover" draggable="false" />
                    </div>
                </span>
                <span v-else>
                    <div class="border grid items-center grid-cols-2 rounded-md p-1">
                        <p class="font-bold" :for="col.field"> {{ col.header }}</p>
                        <p>{{item.data[col.field]}} <span>{{ col.input?.suffix }}</span></p>
                    </div>
                </span>
            </span>
        </div>
        <div>
            <slot name="sidebar" :item="item" />
            <slot name="sidebarAdd" :item="item" />
        </div>
    </Sidebar>
</template>
