<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { onMounted, ref } from 'vue';
import '@bryntum/gantt/locales/gantt.locale.Es.js';
import { BryntumGantt } from '@bryntum/gantt-vue-3';
import { DateHelper, List, LocaleManager, StringHelper, Widget, ColumnStore, Column, TaskModel } from '@bryntum/gantt';
import { useToast } from "primevue/usetoast";
import CustomToolbar from './Components/CustomToolbar.vue';
import ProgressBar from 'primevue/progressbar';
import { usePage } from '@inertiajs/vue3';

LocaleManager.applyLocale('Es');
const toast = useToast();
const props = defineProps({
    project: Object
})
const listCalendar = ref([]);
const ganttref = ref()
const loading = ref(false)
const error = ref(false)
const config=ref({
    full:false,
    readOnly:false,
    canRedo:false,
    canUndo:false,
})
const load=ref(true)

//#region funciones

if (!Widget.factoryable.registry.resourcelist) {
    class ResourceList extends List {

        // Factoryable type name
        static get type() {
            return 'resourcelist';
        }

        static get configurable() {
            return {
                title: 'Resources',
                cls: 'b-inline-list',
                items: [],
                itemTpl: resource => {
                    return StringHelper.xss`
                    <div class="b-resource-detail">
                        <div class="b-resource-name">${resource.name}</div>
                        <div class="b-resource-city">
                            ${resource.unit}
                        </div>
                        <div class="b-resource-city">
                            Costo hora: ${resource.costo_hora}
                            <i data-btip="Quitar recurso" class="b-icon b-icon-trash"></i>
                        </div>
                    </div>`;
                }
            };
        }

        // Called by the owning TaskEditor whenever a task is loaded
        loadEvent(taskRecord) {
            this.taskRecord = taskRecord;
            this.store.data = taskRecord.resources;
        }

        // Called on item click
        onItem({ event, record }) {
            if (event.target.matches('.b-icon-trash')) {
                // Unassign the clicked resource record from the currehtly loaded task
                this.taskRecord.unassign(record);

                // Update our store with the new assignment set
                this.store.data = this.taskRecord.resources;
            }
        }
    }
    ResourceList.initClass();
}

if (!Widget.factoryable.registry.TaskExecutorColumn) {
    class TaskExecutorColumn extends Column {
        // unique alias of the column
        static get type() {
            return 'executor';
        }

        // indicates that the column should be present in "Add New..." column
        static get isGanttColumn() {
            return true;
        }

        static get defaults() {
            return {
                field: 'executor',
                text: 'Ejecutor',
                align: 'center',
                editor: {
                    type: 'combo',
                    multiSelect: true,
                    items: ['GEMAM',
                        'GEBOC',
                        'DEEST',
                        'DEGPM',
                        'DEPRO',
                        'DVPCP',
                        'DVARD',
                        'DVSOL',
                        'DVMEC',
                        'DVPIN',
                        'DVELC',
                        'DVHAB',
                        'DVAIR',
                        'DVEAT',
                        'DVMOT',
                        'DVADQ',
                        'DEINE',
                        'DEMTO',
                        'CLIENTE'
                    ],
                    listItemTpl: ({ text }) => `<div class="text-xs">${text}</div>`,
                    picker: {
                        height: 300,
                        width: 100,
                        features: {
                            filterBar: {
                                compactMode: true,
                            },
                        },
                    }
                },
            };
        }
    }
    ColumnStore.registerColumnType(TaskExecutorColumn);
}
if (!Widget.factoryable.registry.TaskManagerColumn) {
    class TaskManagerColumn extends Column {
        // unique alias of the column
        static get type() {
            return 'manager';
        }

        // indicates that the column should be present in "Add New..." column
        static get isGanttColumn() {
            return true;
        }

        static get defaults() {
            return {
                // the column is mapped to "priority" field of the Task model
                field: 'manager',
                // the column title
                text: 'Responsable',
                align: 'center',
                editor: {
                    type: 'combo',
                    multiSelect: true,
                    items: ['GEMAM',
                        'GEBOC',
                        'JDEEST',
                        'JDEGPM',
                        'JDEPRO',
                        'JDVPCP',
                        'JDVARD',
                        'JDVSOL',
                        'JDVMEC',
                        'JDVPIN',
                        'JDVELC',
                        'JDVHAB',
                        'JDVAIR',
                        'JDVEAT',
                        'JDVMOT',
                        'JDVADQ',
                        'JDEINE',
                        'JDEMTO',
                        'CLIENTE',
                    ],
                    listItemTpl: ({ text }) => `<div class="text-xs">${text}</div>`,
                    listCls: 'padding=0px',
                    picker: {
                        height: 300,
                        width: 100,
                        features: {
                            filterBar: {
                                compactMode: true,
                            },
                        },
                    }
                },
            };
        }
    }
    ColumnStore.registerColumnType(TaskManagerColumn);
}

if (!Widget.factoryable.registry.TaskRowcolorColumn) {
    class TaskRowcolorColumn extends Column {
        // unique alias of the column
        static get type() {
            return 'rowcolor';
        }

        // indicates that the column should be present in "Add New..." column
        static get isGanttColumn() {
            return false;
        }

        static get defaults() {
            return {
                // the column is mapped to "priority" field of the Task model
                field: 'rowcolor',
                // the column title
                text: 'Color',
                align: 'center',
            };
        }
    }
    ColumnStore.registerColumnType(TaskRowcolorColumn);
}

class Task extends TaskModel {

    static $name = 'Task';

    get cls() {
        let obj = {}
        if (super.getData('rowcolor') != 'default') {
            obj[super.getData('rowcolor')] = true
            return Object.assign(super.cls, obj);
        }
        super.cls = {}
        return super.cls
    }
}
//#endregion

onMounted(() => {
    editMode()
})

const nonWorkingTime = ref(true)
const notes = ref({ load: true, data: [] })
async function getNotes() {
    notes.value.load = true
    await axios.get(route('get.notes', props.project.id))
        .then((res) => {
            notes.value.data = res.data
        })
        .catch((error) => {
            console.log(error)
        })
    notes.value.load = false
}

const editMode = () => {
    let gantt = ganttref.value.instance.value
    gantt.readOnly = !gantt.readOnly
    gantt.project.autoSync = !gantt.readOnly
    config.value.readOnly = gantt.readOnly
    if (config.value.readOnly == false) {
        gantt.project.stm.enable()
    }
}

//#region Features Gantt

const taskEdit = ref({
    items: {
        resourcesTab: {
            type: 'resourcelist',
            weight: 120,
            title: 'Recursos',
        },
    }
})

const timeRanges = ref({
    enableResizing: false,
    showCurrentTimeLine: true,
    showHeaderElements: true,
    headerRenderer({ timeRange }) {
        return `<span class="text-xs">${timeRange.id == 'currentTime' ? 'Hoy' : timeRange.name}</span>`
    }
})

const baselines = ref({
    // // Custom tooltip template for baselines
    // template(data) {
    //     const
    //         me = this,
    //         { baseline } = data,
    //         { task } = baseline,
    //         delayed = task.startDate > baseline.startDate,
    //         overrun = task.durationMS > baseline.durationMS;

    //     let { decimalPrecision } = me;

    //     if (decimalPrecision == null) {
    //         decimalPrecision = me.client.durationDisplayPrecision;
    //     }

    //     const
    //         multiplier = Math.pow(10, decimalPrecision),
    //         displayDuration = Math.round(baseline.duration * multiplier) / multiplier;

    //     return `
    //                 <div class="b-gantt-task-title">${StringHelper.encodeHtml(task.name)} (${me.L('Linea Base ')} ${baseline.parentIndex + 1})</div>
    //                 <table>
    //                 <tr><td>${me.L('Inicio')}:</td><td>${data.startClockHtml}</td></tr>
    //                 ${baseline.milestone ? '' : `
    //                     <tr><td>${me.L('Fin')}:</td><td>${data.endClockHtml}</td></tr>
    //                     <tr><td>${me.L('Duracion')}:</td><td class="b-right">${displayDuration + ' ' + DateHelper.getLocalizedNameOfUnit(baseline.durationUnit, baseline.duration !== 1)}</td></tr>
    //                 `}
    //                 </table>
    //                 ${delayed ? `
    //                     <h4 class="statusmessage b-baseline-delay"><i class="statusicon b-fa b-fa-exclamation-triangle"></i>${me.L('Inicio retrasado por')} ${DateHelper.formatDelta(task.startDate - baseline.startDate)}</h4>
    //                 ` : ''}
    //                 ${overrun ? `
    //                     <h4 class="statusmessage b-baseline-overrun"><i class="statusicon b-fa b-fa-exclamation-triangle"></i>${me.L('Atrasado por')} ${DateHelper.formatDelta(task.durationMS - baseline.durationMS)}</h4>
    //                 ` : ''}
    //                 `;
    // },

    // renderer: baselineRenderer
})

const cellEdit = ref({
    addNewAtEnd: false,
})

const taskTooltip = ref({
    textContent: false,
    template({ taskRecord }) {
        return `<span class="text-sm">
                    <div class="">
                        <p class="font-bold text-center w-full text-md" >${StringHelper.encodeHtml(taskRecord.name)}: ${StringHelper.encodeHtml(taskRecord.percentDone.toFixed(0))}% </p>
                    </div>
                    <div class="flex gap-2 justify-between">
                        <div class="">
                            <p class="font-bold">Inicio:</p>
                            <p>${DateHelper.format(taskRecord.startDate, 'DD MMM YYYY')}</p>
                        </div>
                        <div class="border"></div>
                        <div class="">
                            <p class="font-bold">Duracion:</p>
                            <p>${taskRecord.fullDuration}</p>
                        </div>
                    </div>
                </span>`;
    }
})
const criticalPaths = ref({
    disabled: true
})
//#endregion

//#region Config Gantt
const project = ref(
    {
        taskModelClass: Task,
        autoSync: true,
        autoLoad: true,
        transport: {
            load: {
                url: route('dataGantt', props.project.id)
            },
            sync: {
                url: route('syncGantt', props.project.id),
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                credentials: 'include'
            }
        },
        validateResponse: true,
        listeners: {
            syncFail: (e) => {
                console.log(e)
                toast.add({ severity: 'error', group: 'customToast', text: e.response.mensaje, life: 3000 });
                // setTimeout(() => { location.reload() }, 3000);
            },
            beforeSync: (data) => {
                // console.log(data)
                loading.value = true
            },
            sync: (e) => {
                loading.value = false
                let gantt = ganttref.value.instance.value
                config.value.canRedo = gantt.project.stm.canRedo
                config.value.canUndo = gantt.project.stm.canUndo
                getNotes()
            },
            load: (e) => {
                // onExpandAllClick()
                // onZoomToFitClick()
                let gantt = ganttref.value.instance.value
                gantt.zoomOutFull();
                gantt.expandAll();
                listCalendar.value = e.response.calendars.rows;
                getNotes()
                load.value=false
            }
        },
    },
)
const ganttConfig = ref({
    rowHeight: 28,
    dependencyIdField: 'sequenceNumber',
    // visibleDate: { date: today, block: 'center', animate: true },
    project,
    columns: [
        { id: 'wbs', type: 'wbs', text: 'EDT', autoWidth: true }, //gecon
        { id: 'sequence', type: 'sequence', text: 'Secuencia', autoWidth: true },//gemam
        {
            type: 'manager', autoWidth: true, text: 'Responsable'  //gemam
        },
        {
            type: 'executor', autoWidth: true, text: 'Ejecutor' //gemam
        },
        { id: 'name', type: 'name', autoWidth: true, text: 'Actividad' },
        { id: 'percentdone', type: 'percentdone', text: 'Avance', showCircle: true, autoWidth: true },
        { id: 'duration', type: 'duration', text: 'Duración', autoWidth: true },
        { id: 'startdate', type: 'startdate', text: 'Fecha Inicio', autoWidth: true },
        { id: 'enddate', type: 'enddate', text: 'Fecha fin', autoWidth: true },
        {
            id: 'resourceassignment',
            type: 'resourceassignment',
            text: 'Recursos',
            width: 150,
            showAvatars: true,
            align: 'center',
            finalizeCellEdit: ({ record }) => {
                if (record.data.children.length == 0) {
                    // if (DateHelper.isBefore(record.data.endDate, new Date())) {
                    //     return 'La actividad ya finalizo'
                    // } else {
                    //     return true
                    // }
                } else {
                    return 'No se puede programar recursos en esta actividad'
                }
            },
            editor: {
                chipView: {
                    itemTpl: assignment => assignment.resourceName
                },
                picker: {
                    height: 500,
                    width: 500,
                    unitsColumn: {
                        type: 'number',
                        text: 'Cantidad',
                        min: 0,
                        max: 1000,
                        step: 100,
                        width: 100,
                        finalizeCellEdit: ({ value }) => {
                            return value % 100 != 0 ? 'El valor debe ser en multiplos de 100' : true;
                        }
                    },
                    features: {
                        // group: 'resource.city',
                        filterBar: {
                            compactMode: true,
                        },

                        headerMenu: false,
                        cellMenu: false,
                    },
                    // The extra columns are concatenated onto the base column set.
                    columns: [{
                        text: 'Valor Hora',
                        // Read a nested property (name) from the resource calendar
                        field: 'resource.costo_hora',
                        filterable: false,
                        editor: false,
                        width: 100
                    }]
                }
            },
            avatarTooltipTemplate({ taskRecord, assignmentRecord, overflowCount, overflowAssignments }) {
                let overflowAssignments2 = '';
                var task = taskRecord._data;
                for (var element of overflowAssignments) {
                    overflowAssignments2 += ('<div class="flex justify-between mt-2 space-x-2 text-xs">'
                        + '<div>' + element.units / 100 + '</div>'
                        + '<div class="italic">' + element.name + '</div>' +
                        '<div class="font-bold"> $' + Math.round(task.durationUnit == 'day' ? (task.duration * (element.units / 100) * element.costo_hora) * 8.5 : (task.duration * (element.units / 100) * element.costo_hora)).toLocaleString('es')
                        + '</div></div>')
                }
                return `
                        <div class="flex justify-between space-x-2 text-xs"><div>${assignmentRecord.units / 100}</div><div class="italic">${assignmentRecord.name}</div><div class="font-bold">$${Math.round(task.durationUnit == 'day' ? (task.duration * (assignmentRecord.units / 100) * assignmentRecord.costo_hora) * 8.5 : (task.duration * (assignmentRecord.units / 100) * assignmentRecord.costo_hora)).toLocaleString('es')} </div></div>
                        ${overflowCount > 0 ? `${overflowAssignments2}` : ''}
                    `;
            }
        },
        { type: 'addnew', text: 'Añadir Columna', autoWidth: true },
    ],
    subGridConfigs: {
        locked: {
            flex: 1
        },
        normal: {
            flex: 1
        }
    },
    keyMap: {
        // This is a function from the existing Gantt API
        'Ctrl+Shift+Q': () => onAddTaskClick(),
        'Ctrl+Shift+e': () => onExportPDF(),
        'Ctrl+z': () => undo(),
        'Ctrl+y': () => redo(),
        'Ctrl+i': 'indent',
        'Ctrl+o': 'outdent',
    },
})

const headerTpl = ({ currentPage, totalPages }) => `
    <div class="flex space-x-3 items-center">
        <div class="p-0.5 bg-white">
            <img src="https://top.cotecmar.com/svg/cotecmar-logo.svg"/>
        </div>
        <h3>${props.project.name}</h3>
    </div>
    <dl>
        <dt>Fecha y hora de impresion: ${DateHelper.format(new Date(), 'll LT')}</dt>
        <dd c>Impreso por: ${usePage().props.auth.user.name}</dd>
        <dd>${totalPages ? `Pagina: ${currentPage + 1}/${totalPages}` : ''}</dd>
    </dl>
    `;

const footerTpl = ({ currentPage, totalPages }) => `
<h3>© ${new Date().getFullYear()} TOP - COTECMAR</h3></div>
`;
const pdfExport = ref({
    exportServer: 'https://dev.bryntum.com:8082',
    headerTpl,
    footerTpl,
    orientation: 'landscape',
    paperFormat: 'Letter',
    keepRegionSizes: { locked: true },
    columns: ['wbs', 'name', 'percentdone', 'duration', 'startdate', 'enddate'],
    repeatHeader: true,
    exporterType: 'multipagevertical',
    fileFormat: 'pdf',
    fileName: 'Cronograma-' + props.project.name + '-' + DateHelper.format(new Date(), 'YYYY-MM-DD')
})

const rowHeight = ref()
const barMargin = ref()
const barMarginMax = ref()

// const onSettingsShow = (event) => {
//     let gantt = ganttref.value.instance.value
//     rowHeight.value = gantt.rowHeight;
//     barMargin.value = gantt.barMargin;
//     barMarginMax.value = gantt.rowHeight / 2 - 5;
//     setConf.value.toggle(event)
// }

//ajuste de altura de filas

const onSettingsRowHeightChange = () => {
    let gantt = ganttref.value.instance.value
    gantt.rowHeight = rowHeight.value;
    barMarginMax.value = gantt.rowHeight / 2 - 5;
}

//ajuste de margen
const onSettingsMarginChange = () => {
    let gantt = ganttref.value.instance.value
    gantt.barMargin = barMargin.value;
}

const undo = () => {
    let gantt = ganttref.value.instance.value
    gantt.project.stm.undo()
}
const redo = () => {
    let gantt = ganttref.value.instance.value
    gantt.project.stm.redo()
}

//#endregion

const url = [
    {
        ruta: 'projects.index',
        label: 'Proyectos',
    },
    {
        ruta: 'createSchedule.create',
        label: 'Cronograma',
        active: true
    }
]

</script>
<template>
    <AppLayout :href="url">
        <div id="ganttContainer" :class="config.full ? 'fixed bg-white z-50 top-0 left-0 h-screen w-screen' : 'h-full w-full'"
            class="flex flex-col overflow-y-auto gap-y-1">
            <div class="rounded-t-lg h-8 flex justify-between cursor-default">
                <span class="bg-blue-800 flex justify-between rounded-tl-lg w-full">
                    <p class="text-md pl-3 flex items-center font-semibold capitalize text-white">
                        {{ props.project.name }}
                    </p>
                    <p :class="config.readOnly ? 'bg-success text-white font-bold ' : 'text-white bg-warning '"
                        class="px-3 flex items-center">{{ config.readOnly ? 'Modo lectura' : 'Modo edicion' }}</p>
                </span>
                <span v-if="!error"
                    v-tooltip.bottom="loading ? 'Sincronizando cambios...' : 'Todos los cambios estan guardados'"
                    class="w-48 justify-end px-2 flex items-center space-x-2 text-white bg-success rounded-tr-lg">
                    <p class="w-full text-center font-bold">{{ loading ? 'Sincronizando...' : 'Sincronizado' }}</p>
                    <i :class="loading ? 'fa-solid fa-spinner animate-spin' : 'fa-regular fa-circle-check'"
                        class="text-xl" />
                </span>
                <span v-else v-tooltip.bottom="'Hubo un error, se debe recargar la pagina'"
                    class="w-48 justify-end cursor-default px-2 flex items-center space-x-2 text-white bg-danger rounded-tr-lg">
                    <p class="w-full text-center">No sincronizado</p>
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </span>
            </div>
            <!-- <div class="px-1 flex justify-between">
                <div class="flex flex-wrap gap-1">
                    <Button raised icon="fa-solid fa-plus" v-tooltip.bottom="'Nueva actividad'" severity="success"
                        @click=onAddTaskClick() v-if="!readOnly" />
                    <Button raised icon="fa-solid fa-pen" v-tooltip.bottom="'Editar Actividad'" severity="warning"
                        @click="onEditTaskClick()" v-if="!readOnly" />
                    <Button icon="fa-solid fa-rotate-left" severity="secondary" @click="undo()" text
                        :disabled="!canUndo" v-if="!readOnly" v-tooltip.bottom="'Deshacer'" />
                    <Button icon="fa-solid fa-rotate-right" severity="secondary" @click="redo()" text
                        :disabled="!canRedo" v-if="!readOnly" v-tooltip.bottom="'Rehacer'" />
                    <Button raised icon="fa-solid fa-chevron-down" v-tooltip.bottom="'Expandir todo'"
                        severity="secondary" @click="onExpandAllClick()" />
                    <Button raised icon="fa-solid fa-chevron-up" v-tooltip.bottom="'Contraer todo'" severity="secondary"
                        @click="onCollapseAllClick()" />
                    <SplitButton raised v-tooltip.bottom="'Colorear'" v-if="!readOnly" :model="colors" text
                        @click="changeColorRow(colorRow)" :pt="{
        menu: {
            root: 'flex',
            menu: 'grid grid-cols-6 items-center gap-1 flex-wrap space-y-0',
            menuitem: 'm-0 w-min'
        }
    }">
                        <template #icon>
                            <div :class="colorRow" class="w-8 h-6"></div>
                        </template>
<template #menubuttonicon>
                            <i class="fa-solid fa-fill-drip"></i>
                        </template>
<template #item="{ item }">
                            <div :class="item.value" v-tooltip="item.name" @click="changeColorRow(item.value)"
                                class="w-8 h-6 border cursor-pointer hover:ring-1" />
                        </template>
</SplitButton>
<Button raised icon="fa-solid fa-gear" v-tooltip.bottom="'Ajustes'" severity="secondary" @click="onSettingsShow" />
<Button raised icon="fa-solid fa-magnifying-glass" v-tooltip.bottom="'Zoom'" severity="secondary"
    @click="zoom.toggle($event)" />
<Button raised v-tooltip.bottom="'Agregar Calendario'" icon="fa-regular fa-calendar-plus" v-if="!readOnly"
    @click="toggleCalendar" />
<Button raised v-tooltip.bottom="'Guardar en linea base'" icon="fa-solid fa-grip-lines" @click="setLB.toggle($event);"
    v-if="!readOnly" />
<Button raised v-tooltip.bottom="'Ver lineas base'" icon="fa-solid fa-eye" @click="seeLB.toggle($event)" />
<Button raised v-tooltip.bottom="'Exportar a PDF'" icon="fa-solid fa-file-pdf" @click="showModalExport()" />
<Button raised v-tooltip.bottom="'Exportar a XML'" icon="fa-solid fa-file-arrow-down" @click="onExport()" />
<Button raised v-tooltip.bottom="'Importar desde MSProject'" v-if="!readOnly" type="input" icon="fa-solid fa-upload"
    @click="modalImport = true" />
<Button raised v-tooltip.bottom="'Ruta critica'" severity="danger" icon="fa-solid fa-circle-exclamation"
    @click="showCritical()" />
<Button raised v-tooltip.bottom="'Guardar'" severity="success" icon="fa-solid fa-save" @click="reload"
    v-if="!readOnly" />
<Button raised v-tooltip.bottom="'Ver notas'" :disabled="notes.data.length == 0" :loading="notes.load"
    severity="success" icon="fa-regular fa-note-sticky" @click="visibleNotes = true" />

<Calendar dateFormat="dd/mm/yy" :manualInput="false" v-model="fecha" @dateSelect="onStartDateChange"
    placeholder="Buscar por fecha" class="hidden sm:flex !h-8 shadow-md" showIcon :pt="{ input: '!h-8' }" />
<InputText v-model="texto" @input="onFilterChange" placeholder="Buscar por actividad"
    class="shadow-md hidden sm:flex" />
</div>
<div class="flex gap-1">
    <Button v-tooltip.left="readOnly ? 'Modo edicion' : 'Solo lectura'"
        :icon="readOnly ? 'fa-solid fa-pen-to-square' : 'fa-solid fa-eye'" severity="help" raised @click="editMode" />
    <Button v-tooltip.left="full ? 'Pantalla normal' : 'Pantalla completa'"
        :icon="full ? 'fa-solid fa-minimize' : 'fa-solid fa-maximize'" severity="help" raised @click="full = !full" />
</div>
</div> -->
            <CustomToolbar v-if="!load" :notes :listCalendar v-model:config="config" :project="props.project" v-model:gantt="ganttref.instance.value">
            </CustomToolbar>
            <div v-else class="h-10 flex flex-col justify-center px-20">
                <ProgressBar  mode="indeterminate" style="height: 6px"></ProgressBar>
            </div>
            <BryntumGantt :filterFeature="true" :taskEditFeature="taskEdit"
                :projectLinesFeature="false" :timelineScrollButtons="true" :cellEditFeature="cellEdit"
                :pdfExportFeature="pdfExport" :mspExportFeature="true" :projectLines="true"
                :baselinesFeature="baselines" ref="ganttref" class="h-full" :printFeature="true" v-bind="ganttConfig"
                :dependenciesFeature="{ radius: 5 }" :timeRangesFeature="timeRanges" :taskTooltipFeature="taskTooltip"
                :criticalPathsFeature="criticalPaths" :nonWorkingTimeFeature="nonWorkingTime" />
        </div>
    </AppLayout>
    <!-- <OverlayPanel id="setLB" ref="setLB" :pt="{ content: '!p-1' }">
        <div class="flex flex-col space-y-1">
            <p>Lineas base</p>
            <span v-for="index in 4">
                <Button :label="'LB ' + index" text class="w-full" @click="setBaseline(index)" />
            </span>
        </div>
    </OverlayPanel>
    <OverlayPanel id="seeLB" ref="seeLB" :pt="{ content: '!p-1' }">
        <div class="flex flex-col space-y-1">
            <p>Lineas base</p>
            <span v-for="index in 4">
                <div class="flex items-center">
                    <Checkbox v-model="selectedLb[index]" :inputId="'LB' + index" binary :name="'LB' + index"
                        @change="toggleBaselineVisible" />
                    <label :for="'LB' + index" class="ml-2"> {{ 'LB ' + index }} </label>
                </div>
            </span>
        </div>
    </OverlayPanel>
    <OverlayPanel id="setConf" ref="setConf" :pt="{ content: '!p-4' }">
        <div class="flex flex-col h space-y-3">
            <span>
                <Slider v-model="rowHeight" :min="17" @change="onSettingsRowHeightChange" />
                <p>Altura de celdas ({{ rowHeight }})</p>
            </span>
            <span>
                <Slider v-model="barMargin" :max="barMarginMax" @change="onSettingsMarginChange" />
                <p>Altura de barras ({{ barMargin }})</p>
            </span>
            <span class="">
                <CustomInput v-tooltip="'Tamaño de letra'" v-model:input="fontSize" type="dropdown"
                    :options="['5px', '6px', '7px', '8px', '9px', '10px', '11px', '12px', '13px', '14px']" />
            </span>
        </div>
    </OverlayPanel> -->

</template>

<style>
.b-export-header,
.b-export-footer {
    display: flex;
    color: #fff;
    background: #2E3092;
    align-items: center;
    z-index: 10000;
}

.b-export-header {
    text-align: start;
    height: 44px;
    position: relative;
    padding: 0.7em 1em 0.5em 1em;
    display: flex;
    flex-flow: row nowrap;
    justify-content: space-between;
}

.b-export-header img {
    height: 32px;
    width: 32px;
}

.b-export-header dl {
    margin: 0;
    font-size: 10px;
}

.b-export-header dd {
    margin: 0;
}

.b-export-footer {
    justify-content: center;
}

.b-grid-body-container {
    position: relative;
    background-image: url('https://top.cotecmar.com/images/cotecmar-logo-opacity-20.png') !important;
    background-repeat: no-repeat !important;
    background-position: 50% 50% !important;
}

.b-export .b-grid-body-container {
    position: relative;
    background-image: url('https://top.cotecmar.com/images/cotecmar-logo-opacity-20.png') !important;
    background-repeat: no-repeat !important;
    background-position: 50% 50% !important;
}

.b-gantt-task {
    border-radius: 3px !important;
}
</style>
