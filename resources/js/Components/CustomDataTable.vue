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

const props = defineProps({
    data: {
        type: Array,
        required: true
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
        defaul: 10
    },
    showHeader: {
        type: Boolean,
        default: true
    }
})

//#region Filtros de tabla y visor columnas
const rows = ref(props.rowsDefault)
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
</script>

<template>
    <DataTable id="tabla" :value="data" :paginator="data.length > 0 && paginator" :rows="rows" selectionMode="single"
        tableStyle="" sortMode="multiple" scrollable scrollHeight="flex" :loading="loading"
        currentPageReportTemplate="{first} al {last} de un total de {totalRecords}" removableSort
        v-model:filters="filters" stripedRows filterDisplay="menu" class="p-datatable-sm text-xs p-1 rounded-md"
        stateStorage="session" :stateKey="cacheName ? 'dt-' + cacheName + '-state-session' : null"
        :globalFilterFields="globalFilterFields" @row-click="$emit('rowClic', $event)"
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
            <div class="space-y-1">
                <span class="flex justify-between ">
                    <p class="text-lg h-ful flex items-center font-semibold leading-6 capitalize text-primary">
                        {{ title }}
                    </p>
                    <span class="space-x-1">
                        <slot name="buttonHeader" />
                        <Button v-if="showAdd" v-tooltip.left="'Añadir'" @click="$emit('addClick', $event)"
                            severity="success" icon="fa-solid fa-plus" label="Agregar" outlined />
                    </span>
                </span>
                <div class="flex items-center " :class="filter ? 'justify-between' : 'justify-end'" v-if="showHeader">
                    <div class="grid gap-2 sm:grid-cols-2" v-if="filter">
                        <div class="flex gap-2">
                            <Button v-tooltip.top="'Quitar filtros'" @click="clearFilter()" outlined
                                icon="fa-solid fa-filter-circle-xmark" />
                            <IconField iconPosition="left">
                                <InputIcon>
                                    <i class="fa-solid fa-magnifying-glass" />
                                </InputIcon>
                                <InputText v-model="filters.global.value" type="search" size="small"
                                    placeholder="Buscar" />
                            </IconField>
                        </div>
                        <div class="w-full overflow-x-auto">
                            <ButtonGroup v-if="props.filterButtons && filterOK" class="flex">
                                <Button v-for="button in props.filterButtons" class="font-bold truncate"
                                    :label="button.label + ': ' + getTotalStatus(button.field, button.data)"
                                    :severity=button.severity
                                    @click="filters[button.field].value == button.data ? filters[button.field].value = null : filters[button.field].value = button.data"
                                    :outlined="filters[button.field].value != button.data" icon="" />
                            </ButtonGroup>
                        </div>
                    </div>
                    <div class="space-x-2 hidden sm:block">
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
                <ApplicationLogo />
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
            <div class="flex items-center">
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
        <span v-for="col, index  in columnasSelect">
            <Column v-if="col.visible == null || col.visible == true" :field="col.field" :filterField="col.field"
                :class="col.class" :sortable="col.sortable" :show-filter-match-modes="false"
                :filterMenuStyle="{ width: '16rem' }" :frozen="col.frozen" :pt="{
        headerContent: { class: '!h-8' },
        headerCell: { class: '!p-0.5' }
    }
        ">
                <template #header>
                    <p class="text-sm text-primary uppercase font-bold truncate">{{ col.header }}</p>
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
                            :rounded="col.rounded" @click="$emit(col.event, $event, data)">
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
                    <p v-else class="">
                        {{ data[col.field] }}
                    </p>
                </template>
            </Column>
        </span>
        <Column frozen alignFrozen="right" class="w-[8%]" v-if="props.actions.length > 0">
            <template #body="{ data }">
                <div class="flex items-center justify-center w-full bg-white rounded-md shadow-sm">
                    <span v-for="button in props.actions">
                        <Button @click="$emit(button.event, $event, data)" :text="button.text"
                            :severity="button.severity" :outlined="button.outlined" :rounded="button.rounded"
                            :icon="button.icon" :title="button.label" :class="button.class"
                            v-if="button.show == null ? true : button.show" />
                    </span>
                </div>
            </template>
        </Column>
        <!-- #endregion -->
    </DataTable>
</template>