<script setup>
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { onMounted, ref } from 'vue';
import { FilterMatchMode } from 'primevue/api'
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Dropdown from 'primevue/dropdown';
import MultiSelect from 'primevue/multiselect';
import Swal from 'sweetalert2';
import ProgressBar from 'primevue/progressbar';
import Tag from 'primevue/tag';
import InputNumber from 'primevue/inputnumber';

const props = defineProps({
    data: {
        type: Array,
        required: true
    },
    cacheName: {
        type: String,
        default: ''
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
})

//#region Filtros de tabla y visor columnas
const rows = ref(5)
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});
const globalFilterFields = ref([])
const columnasSelect = ref()
if (props.columnas.length > 7) {
    columnasSelect.value = props.columnas.slice(0, 7)
} else {
    columnasSelect.value = props.columnas
}
const initFilters = () => {
    globalFilterFields.value = ['id']
    for (var columna of props.columnas) {
        if (columna.filter) {
            filters.value[columna.field] = { value: null, matchMode: FilterMatchMode.CONTAINS }
            globalFilterFields.value.push(columna.field)
        }
    }
};
onMounted(() => {
    initFilters()
})
const clearFilter = () => {
    initFilters();
};
//#endregion

//#region exportar
const exportar = () => {
    var fileLink = document.createElement('a');
    fileLink.href = route(exportRute);
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

</script>

<template>
    <DataTable id="tabla" :value="data" paginator :rows="rows" selectionMode="single" tableStyle="min-width: 70rem"
        currentPageReportTemplate="{first} al {last} de un total de {totalRecords}" v-model:filters="filters"
        scrollHeight="flex" stripedRows filterDisplay="menu" scrollable class="p-datatable-sm" :removableSort="true"
        stateStorage="session" :stateKey="'dt-' + cacheName + '-state-session'" :globalFilterFields="globalFilterFields"
        paginatorTemplate="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink" :pt="{
            paginator: {
                paginatorWrapper: '!p-0',
                current: 'text-sm font-bold cursor-default !h-8 flex item-center',
                pagebutton: {
                    class: '!font-bold !h-8 !rounded-md !w-6',
                },
                firstPageButton: '!h-8 !rounded-md',
                previousPageButton: '!h-8 !rounded-md',
                nextPageButton: '!h-8 !rounded-md',
                lastPageButton: '!h-8 !rounded-md'
            }
        }">
        <template #header>
            <div class="space-y-3">
                <span class="flex justify-between">
                    <slot name="header" />
                </span>
                <div class="flex items-center justify-between">
                    <div class="space-x-2">
                        <Button label="Quitar filtros" @click="clearFilter()" outlined class="!h-8"
                            icon="fa-solid fa-filter-circle-xmark" />
                        <span class="p-input-icon-left">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <InputText v-model="filters.global.value" type="search" size="small" placeholder="Buscar" :pt="{
                                root: { class: '!h-8' }
                            }" />
                        </span>
                    </div>
                    <div class="space-x-2">
                        <Button v-if="exportRute != ''" @click="exportar" icon="fa-solid fa-file-excel" class="!h-8 !w-8" />
                        <MultiSelect v-model="columnasSelect" display="chip" :options="props.columnas" optionLabel="header"
                            placeholder="Selecciona columnas a mostrar" class="md:w-20rem w-min h-8" :pt="{
                                root: '!border-0 !ring-0',
                                trigger: '!hidden',
                                labelContainer: '!p-0 ',
                                label: '!p-0 text-center',
                                token: '!p-0',
                                item: ' !p-2',
                                header: '!p-2'
                            }">
                            <template #value>
                                <Button icon="fa-solid fa-eye" text class="!h-8 !w-8" />
                            </template>
                        </MultiSelect>
                    </div>
                </div>
            </div>
        </template>
        <!-- #region ajustes de tabla -->
        <template #empty>
            <div class="flex justify-center">
                No hay registros
            </div>
        </template>
        <template #loading>
            <div class="flex justify-center">
                Cargando...
            </div>
        </template>
        <template #paginatorstart>
            <div class="flex items-center">
                <Dropdown v-model="rows" :options="[1, 5, 10, 20, 50, 100]" :pt="{
                    root: '!h-8 !border-0 !ring-0',
                    input: '!py-0 !flex !items-center',
                    item: '!p-1 w-full text-center'
                }" />
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

        <Column v-for="col, index in columnasSelect" :field="col.field" :filterField="col.field" :sortable="col.sortable"
            :show-filter-match-modes="false" :filterMenuStyle="{ width: '16rem' }" :frozen="col.frozen" :pt="{
                headerContent: { class: '!h-8' },
                headerCell: { class: '!py-0 !px-1' },
            }">
            <template #header>
                <p class="text-sm">{{ col.header }}</p>
            </template>
            <template #filtericon>
                <i class="fa-solid fa-filter"></i>
            </template>
            <template #sorticon="{ sortOrder, sorted }">
                <i :class="sorted ? sortOrder == 1 ? 'fa-solid fa-arrow-up-1-9' : 'fa-solid fa-arrow-up-9-1' : 'fa-solid fa-sort'"
                    class="text-gray-500 flex justify-center items-center ml-1 h-5 w-5"></i>
            </template>

            <template #filter="{ filterModel }" v-if="col.filter">
                <input v-if="col.type == 'date'" class="w-full rounded-md" type="date" v-model="filterModel.value"
                    dateFormat="mm/dd/yy" placeholder="mm/dd/yyyy" mask="99/99/9999" />
                <InputNumber v-else v-if="col.type == 'number'" v-model="filterModel.value" class="p-column-filter"
                    placeholder="Numero a buscar" />
                <InputText v-else v-model="filterModel.value" type="text" class="p-column-filter"
                    placeholder="Escriba algo para buscar" />
            </template>

            <template #body="{ data }">
                {{ data[col.field] }}
            </template>

        </Column>

        <Column frozen alignFrozen="right" style="width:8%" v-if="props.actions.length>0">
            <template #body="{ data }">
                <div class="flex items-center justify-center w-full">
                    <Button v-for="button in props.actions" @click="$emit(button.event, $event, data)" :severity="button.severity"
                        :text="button.text" :outlined="button.outlined" :rounded="button.rounded" :icon="button.icon" :label="button.label"
                        :class="button.class" />
                </div>
            </template>
        </Column>
        <!--
        <Column sortable frozen :filterMenuStyle="{ width: '16rem' }" :show-filter-match-modes="false" field="id" :pt="{
            headerContent: { class: '!h-8' },
            headerCell: { class: '!py-0 !px-1' },
        }">
            <template #body="{ data }">
                <div class="text-center">
                    {{ data.id }}
                </div>
            </template>
            <template #filter="{ filterModel }">
                <InputText v-model="filterModel.value" type="text" class="p-column-filter" placeholder="Numero de caso" />
            </template>
            <template #filtericon>
                <i class="fa-solid fa-filter"></i>
            </template>
            <template #sorticon="{ sortOrder, sorted }">
                <i :class="sorted ? sortOrder == 1 ? 'fa-solid fa-arrow-up-1-9' : 'fa-solid fa-arrow-up-9-1' : 'fa-solid fa-sort'"
                    class="text-gray-500 flex justify-center items-center ml-1 h-5 w-5"></i>
            </template>
            <template #header>
                <p class="mr-1">Id</p>
            </template>
        </Column>
        <Column v-for="col, index in columnasSelect"
            :sortable="!['investigador', 'encargado'].some(field => col.field.includes(field))" :field="col.field"
            :filterField="['investigador', 'encargado'].some(field => col.field.includes(field)) ? col.field + '.name' : col.field"
            :show-filter-match-modes="false" :pt="{
                headerCell: { class: '!p-1' },
                filterButtonbar: { class: 'space-x-1' },
                filterMenuButton: { class: '!h-8 !w-8' }
            }">
            <template #body="{ data }">
                <div class="text-center">
                    <Tag v-if="col.field == 'estado'" :value="data.estado" :severity="getSeverity(data.estado)" />
                    <span
                        v-if="['sede', 'gerencia', 'nombre', 'correo', 'celular', 'dependencia'].some(field => col.field.includes(field))">
                        {{ data[col.field] }}
                    </span>
                    <Tag v-if="col.field == 'solicita_respuesta'" :value="data.solicita_respuesta == '0' ? 'No' : 'Si'"
                        :severity="data.solicita_respuesta == '0' ? 'primary' : 'danger'" />
                    <span v-if="['investigador', 'encargado'].some(field => col.field.includes(field))">
                        <Tag v-if="data[col.field].name == 'Sin asignar'" :value="data[col.field].name" severity="danger" />
                        <span class="flex items-center" v-else>
                            <img alt="Imagen Perfil" class="w-10 h-10 border-2 border-white rounded-full"
                                :src="data[col.field].photo != null ? data[col.field].photo : 'https://ui-avatars.com/api/?name=' + data[col.field].APELLIDOS_NOMBRES" />
                            <p class="text-sm italic">
                                {{ data[col.field].short_name }}
                            </p>
                        </span>
                    </span>
                    <span v-if="col.field == 'avance'">
                        <div v-if="!isNaN(data.avance)" class="">
                            <ProgressBar :value="data.avance" style="border-radius: 9999px; height: 12px">{{
                                data.avance }}%
                            </ProgressBar>
                        </div>
                        <div v-else class="flex justify-center">
                            <Tag :value=data.avance :severity="getSeverity(data.avance)" />
                        </div>
                    </span>
                    <span v-if="col.field == 'created_at'">
                        <div class="text-xs italic text-gray-800">
                            <span> {{ formatDate(data.created_at) }}</span>
                        </div>
                    </span>

                </div>
            </template>
            <template #filter="{ filterModel }">

            </template>
            <template #filter="{ filterModel }"
                v-if="['estado', 'sede', 'solicita_respuesta', 'investigador', 'encargado', 'gerencia'].some(field => col.field.includes(field))">
                <Dropdown v-if="col.field == 'estado'" v-model="filterModel.value" :options="estados"
                    placeholder="Selecciona el estado" class="p-column-filter" showClear>
                    <template #option="slotProps">
                        <Tag :value="slotProps.option" :severity="getSeverity(slotProps.option)" />
                    </template>
                </Dropdown>
                <Dropdown v-if="col.field == 'sede'" v-model="filterModel.value" :options="props.sedes"
                    placeholder="Selecciona la sede" class="p-column-filter" showClear>
                    <template #option="slotProps">
                        <span>{{ slotProps.option }}</span>
                    </template>
                </Dropdown>

                <Dropdown v-if="col.field == 'solicita_respuesta'" v-model="filterModel.value" :options=respuesta
                    placeholder="Selecciona" class="p-column-filter" showClear>
                    <template #value="slotProps">
                        <p>{{ slotProps.value == '0' ? 'No' : 'Si' }}</p>
                    </template>
                    <template #option="slotProps">
                        <p>{{ slotProps.option == '0' ? 'No' : 'Si' }}</p>
                    </template>
                </Dropdown>
                <InputText v-if="['investigador', 'encargado', 'gerencia'].some(field => col.field.includes(field))"
                    v-model="filterModel.value" type="text" class="p-column-filter" placeholder="Nombre" />
            </template>
            <template #filtericon>
                <i class="fa-solid fa-filter"></i>
            </template>
            <template #sorticon="{ sortOrder, sorted }">
                <i :class="sorted ? sortOrder == 1 ? 'fa-solid fa-arrow-up-a-z' : 'fa-solid fa-arrow-up-z-a' : 'fa-solid fa-sort'"
                    class="text-gray-500 flex justify-center items-center h-5 w-5"></i>
            </template>
            <template #header>
                <p class="mr-1">{{ col.header }}</p>
            </template>
        </Column>
        <Column frozen alignFrozen="right">
            <template #body="{ data }">
                <div class="flex items-center justify-center w-full">
                    <Button @click="toggle($event, data)" severity="success" aria-haspopup="true"
                        aria-controls="overlay_menu" text outlined rounded icon="fa-solid fa-ellipsis-vertical"
                        class="!h-5 !w-5" />
                </div>
            </template>
        </Column> -->

        <!-- #endregion -->
    </DataTable>
</template>
