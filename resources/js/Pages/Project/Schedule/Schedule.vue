<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { onMounted, ref } from 'vue';
import '@bryntum/gantt/gantt.material.css';
import '@bryntum/gantt/locales/gantt.locale.Es.js';
import { BryntumGantt, BryntumResourceUtilization } from '@bryntum/gantt-vue-3';
import { AjaxHelper, DateHelper, List, LocaleManager, StringHelper, Widget, ColumnStore, Column, TaskModel } from '@bryntum/gantt';
import Slider from 'primevue/slider'
import { useToast } from "primevue/usetoast";
import InputText from 'primevue/inputtext';
import Calendar from 'primevue/calendar';
import OverlayPanel from 'primevue/overlaypanel';
import Checkbox from 'primevue/checkbox';
import CustomModal from '@/Components/CustomModal.vue';
import FileUpload from 'primevue/fileupload';
import CustomInput from '@/Components/CustomInput.vue';

const toast = useToast();
const fontSize = ref('10px')
const props = defineProps({
    project: Object,
    groups: Array
})

LocaleManager.applyLocale('Es');
const ganttref = ref()
const loading = ref(false)
const error = ref(false)
const canRedo = ref()
const canUndo = ref()

//#region funciones
const headerTpl = ({ currentPage, totalPages }) => `
    <div class="flex space-x-3 items-center">
        <img class="max-h-8" alt="Company logo" src="https://top.cotecmar.com/svg/cotecmar-logo.svg"/>
        <h3>${props.project.name}</h3>
    </div>
    <img class="opacity-50" alt="Company logo" src="https://top.cotecmar.com/svg/cotecmar-logo.svg"/>
    <dl>
        <dt>Fecha y hora de impresion: ${DateHelper.format(new Date(), 'll LT')}</dt>
        <dd>${totalPages ? `Pagina: ${currentPage + 1}/${totalPages}` : ''}</dd>
    </dl>
    `;

const footerTpl = () => `<h3 class="">© ${new Date().getFullYear()} TOP - COTECMAR</h3>`;

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
class Importer {

    constructor(config) {
        this.gantt = config.ganttImport;
        this.defaultColumns = config.defaultColumns;
    }

    async importData(data) {
        const
            me = this,

            project = new me.gantt.projectModelClass({
                silenceInitialCommit: false,
                // To save imported data provide `sync` url and set `autoSync` true, or call `gantt.project.sync()` manually after data is imported.
                autoSync: false,
            });

        me.project = project;
        me.calendarManager = project.calendarManagerStore;
        me.taskStore = project.taskStore;
        me.assignmentStore = project.assignmentStore;
        me.resourceStore = project.resourceStore;
        me.dependencyStore = project.dependencyStore;

        Object.assign(me, {
            calendarMap: {},
            resourceMap: {},
            taskMap: {}
        });

        me.importCalendars(data);

        const tasks = me.getTaskTree(Array.isArray(data.tasks) ? data.tasks : [data.tasks]);

        me.importResources(data);
        me.importAssignments(data);

        me.taskStore.rootNode.appendChild(tasks[0].children);

        me.importDependencies(data);

        me.importProject(data);

        // Assign the new project to the gantt before launching commitAsync()
        // to let Gantt resolve possible scheduling conflicts
        me.gantt.project = project;

        await me.project.commitAsync();

        me.importColumns(data);

        return project;
    }

    // region RESOURCES
    importResources(data) {
        this.resourceStore.add(data.resources.map(this.processResource, this));
    }

    processResource(data) {
        const { id } = data;

        delete data.id;

        data.calendar = this.calendarMap[data.calendar];

        const resource = new this.resourceStore.modelClass(data);

        this.resourceMap[id] = resource;

        return resource;
    }
    // endregion

    // region DEPENDENCIES
    importDependencies(data) {
        this.dependencyStore.add(data.dependencies.map(this.processDependency, this));
    }

    processDependency(data) {
        const
            me = this,
            { fromEvent, toEvent } = data;

        delete data.id;

        const dep = new me.dependencyStore.modelClass(data);

        dep.fromEvent = me.taskMap[fromEvent].id;
        dep.toEvent = me.taskMap[toEvent].id;

        return dep;
    }
    // endregion

    // region ASSIGNMENTS
    importAssignments(data) {
        this.assignmentStore.add(data.assignments.map(this.processAssignment, this));
    }

    processAssignment(data) {
        const me = this;

        delete data.id;

        return new me.assignmentStore.modelClass({
            units: data.units,
            event: me.taskMap[data.event],
            resource: me.resourceMap[data.resource]
        });
    }
    // endregion

    // region TASKS
    getTaskTree(tasks) {
        return tasks.map(this.processTask, this);
    }

    processTask(data) {
        const
            me = this,
            { id, children } = data;

        delete data.children;
        delete data.id;
        delete data.milestone;

        data.calendar = me.calendarMap[data.calendar];

        const t = new me.taskStore.modelClass(data);

        if (children) {
            t.appendChild(me.getTaskTree(children));
        }

        t._id = id;
        me.taskMap[t._id] = t;

        return t;
    }
    // endregion

    // region CALENDARS
    processCalendarChildren(children) {
        return children.map(this.processCalendar, this);
    }

    processCalendar(data) {
        const
            me = this,
            { id, children } = data,
            intervals = data.intervals;

        delete data.children;
        delete data.id;

        const t = new me.calendarManager.modelClass(Object.assign(data, { intervals }));

        if (children) {
            t.appendChild(me.processCalendarChildren(children));
        }

        t._id = id;
        me.calendarMap[t._id] = t;

        return t;
    }

    // Entry point of calendars loading
    importCalendars(data) {
        this.calendarManager.add(this.processCalendarChildren(data.calendars.children));
    }
    // endregion

    // region Columns

    importColumns(data) {
        let columns = data.columns.map(this.processColumn, this).filter(column => column);

        const columnStore = this.gantt.subGrids.locked.columns;

        // if no columns extracted apply default set (if configured)
        if (!columns.length && this.defaultColumns) {
            columns = this.defaultColumns;
        }

        if (columns.length) {
            columnStore.removeAll(true);
            columnStore.add(columns);
        }
    }

    processColumn(data) {
        const columnClass = this.gantt.columns.constructor.getColumnClass(data.type);

        // ignore unknown columns (or columns that classes are not loaded)
        if (columnClass) {
            return Object.assign({ region: 'locked' }, data);
        }
    }

    // endregion

    importProject(data) {
        if ('calendar' in data.project) {
            data.project.calendar = this.calendarMap[data.project.calendar];
        }
        Object.assign(this.project, data.project);
    }
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
class Task extends TaskModel {

    static $name = 'Task';

    get cls() {
        // adds 'b-critical' CSS class to critical tasks
        return Object.assign(super.cls, { 'b-critical': this.critical });
    }

}
//#endregion

onMounted(() => {
    onExpandAllClick()
    editMode()
})

const full = ref(false)
const readOnly = ref()

const editMode = () => {
    let gantt = ganttref.value.instance.value
    gantt.readOnly = !gantt.readOnly
    gantt.project.autoSync = !gantt.readOnly
    readOnly.value = gantt.readOnly
    if (readOnly.value == false) {
        gantt.project.stm.enable()
    }
}

//#region Features Gantt

const pdfExport = ref({
    exportServer: 'https://dev.bryntum.com:8082',
    headerTpl,
    footerTpl,
    orientation: 'portrait',
    paperFormat: 'Letter',
    keepRegionSizes: { locked: true },
    exportDialog: {
        autoSelectVisibleColumns: false,
        items: {
            columnsField: { value: ['wbs', 'name', 'percentdone', 'duration', 'startdate', 'enddate'] }
        }
    }
})
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
    // Custom tooltip template for baselines
    template(data) {
        const
            me = this,
            { baseline } = data,
            { task } = baseline,
            delayed = task.startDate > baseline.startDate,
            overrun = task.durationMS > baseline.durationMS;

        let { decimalPrecision } = me;

        if (decimalPrecision == null) {
            decimalPrecision = me.client.durationDisplayPrecision;
        }

        const
            multiplier = Math.pow(10, decimalPrecision),
            displayDuration = Math.round(baseline.duration * multiplier) / multiplier;

        return `
                    <div class="b-gantt-task-title">${StringHelper.encodeHtml(task.name)} (${me.L('Linea Base ')} ${baseline.parentIndex + 1})</div>
                    <table>
                    <tr><td>${me.L('Inicio')}:</td><td>${data.startClockHtml}</td></tr>
                    ${baseline.milestone ? '' : `
                        <tr><td>${me.L('Fin')}:</td><td>${data.endClockHtml}</td></tr>
                        <tr><td>${me.L('Duracion')}:</td><td class="b-right">${displayDuration + ' ' + DateHelper.getLocalizedNameOfUnit(baseline.durationUnit, baseline.duration !== 1)}</td></tr>
                    `}
                    </table>
                    ${delayed ? `
                        <h4 class="statusmessage b-baseline-delay"><i class="statusicon b-fa b-fa-exclamation-triangle"></i>${me.L('Inicio retrasado por')} ${DateHelper.formatDelta(task.startDate - baseline.startDate)}</h4>
                    ` : ''}
                    ${overrun ? `
                        <h4 class="statusmessage b-baseline-overrun"><i class="statusicon b-fa b-fa-exclamation-triangle"></i>${me.L('Atrasado por')} ${DateHelper.formatDelta(task.durationMS - baseline.durationMS)}</h4>
                    ` : ''}
                    `;
    },

    renderer: baselineRenderer
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
    disabled: false
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
                url: route('dataGantt', props.project)
            },
            sync: {
                url: route('syncGantt', props.project),
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
                toast.add({ severity: 'error', group: 'customToast', text: 'Ha ocurrido un error, reiniciando...', life: 3000 });
                // setTimeout(() => { location.reload() }, 3000);
            },
            beforeSync: (data) => {
                loading.value = true
            },
            sync: (e) => {
                loading.value = false
                let gantt = ganttref.value.instance.value
                canRedo.value = gantt.project.stm.canRedo
                canUndo.value = gantt.project.stm.canUndo
            },
            load: () => {
                onExpandAllClick()
                onZoomToFitClick()
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
            type: 'manager', autoWidth: true //gemam
        },
        {
            type: 'executor', //gemam
        },
        { id: 'name', type: 'name', },
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
//#endRegion

//#region toolbar
const onAddTaskClick = async () => {
    let gantt = ganttref.value.instance.value
    // console.log(gantt.value.instance.value)
    const added = gantt.taskStore.rootNode.appendChild({
        name: "Nueva tarea",
        duration: 1
    });
    gantt.indent(added)
    // wait for immediate commit to calculate new task fields
    await gantt.project.commitAsync();

    // scroll to the added task
    await gantt.scrollRowIntoView(added);

    gantt.features.cellEdit.startEditing({
        record: added,
        field: "name"
    });
}

const onEditTaskClick = () => {
    let gantt = ganttref.value.instance.value
    if (gantt.selectedRecord) {
        gantt.editTask(gantt.selectedRecord);
    } else {
        toast.add({ severity: 'error', group: 'customToast', text: 'Debe seleccionar la tarea a editar', life: 2000 });
    }
}

const onExpandAllClick = () => {
    let gantt = ganttref.value.instance.value
    gantt.expandAll();
}

//recojer todas las tareas
const onCollapseAllClick = () => {
    let gantt = ganttref.value.instance.value
    gantt.collapseAll();
}

const zoom = ref()
function onZoomInClick() {
    let gantt = ganttref.value.instance.value
    gantt.zoomIn();
}

function onZoomOutClick() {
    let gantt = ganttref.value.instance.value
    gantt.zoomOut();
}

function onZoomToFitClick() {
    let gantt = ganttref.value.instance.value
    gantt.zoomToFit({
        leftMargin: 50,
        rightMargin: 50
    });
}

const fecha = ref()
function onStartDateChange() {
    // console.log(event)
    let gantt = ganttref.value.instance.value
    gantt.scrollToDate(fecha.value, { animate: 300, block: 'start' });

    // gantt.startDate = DateHelper.add(event, -1, "week");

    // gantt.project.setStartDate(event);
}

const texto = ref()
function onFilterChange() {
    let gantt = ganttref.value.instance.value
    if (texto.value === "") {
        gantt.taskStore.clearFilters();
    } else {
        texto.value = texto.value.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");

        gantt.taskStore.filter({
            filters: task =>
                task.name && task.name.match(new RegExp(texto.value, "i")),
            replace: true
        });
    }
}

const setLB = ref();
const seeLB = ref();
const setBaseline = (index) => {
    let gantt = ganttref.value.instance.value
    gantt.taskStore.setBaseline(index);
}

const selectedLb = ref({})

const toggleBaselineVisible = () => {
    let gantt = ganttref.value.instance.value
    // console.log(selectedLb.value)
    Object.entries(selectedLb.value).forEach(element => {
        // console.log(element[0])
        gantt.element.classList[element[1] ? 'remove' : 'add'](`b-hide-baseline-${element[0]}`);
    });
}

function baselineRenderer({ baselineRecord, taskRecord, renderData }) {
    if (baselineRecord.endDate.getTime() + 24 * 3600 * 1000 < taskRecord.endDate.getTime()) {
        renderData.className['b-baseline-behind'] = 1;
    }
    else if (taskRecord.endDate < baselineRecord.endDate) {
        renderData.className['b-baseline-ahead'] = 1;
    }
    else {
        renderData.className['b-baseline-on-time'] = 1;
    }
}

const onExport = () => {
    let gantt = ganttref.value.instance.value
    // give a filename based on task name
    const filename = props.project.SAP_code + '-' + props.project.name + '.xml'
    // call the export to download the XML file
    gantt.features.mspExport.export({
        filename
    });
}

const onExportPDF = () => {
    let gantt = ganttref.value.instance.value
    gantt.features.pdfExport.showExportDialog();
}

const setConf = ref()
const rowHeight = ref()
const barMargin = ref()
const barMarginMax = ref()

const onSettingsShow = (event) => {
    let gantt = ganttref.value.instance.value
    rowHeight.value = gantt.rowHeight;
    barMargin.value = gantt.barMargin;
    barMarginMax.value = gantt.rowHeight / 2 - 5;
    setConf.value.toggle(event)
}

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
const modalImport = ref(false)
const fileMSP = ref()
const ganttrefimport = ref()
const ganttConfigImporter = ref({
    emptyText: 'Seleccione un archivo',
    startDate: '2023-01-08',
    endDate: '2023-04-01',
    rowHeight: 28,
    dependencyIdField: 'sequenceNumber',
    columns: [
        { id: 'wbs', type: 'wbs', text: 'EDT' },
        { id: 'sequence', type: 'sequence', text: 'Secuencia' },
        { id: 'name', type: 'name', },
        { id: 'percentdone', type: 'percentdone', text: 'Avance', showCircle: true },
        { id: 'duration', type: 'duration', text: 'Duración' },
        { id: 'startdate', type: 'startdate', text: 'Fecha Inicio' },
        { id: 'enddate', type: 'enddate', text: 'Fecha fin' },
        { type: 'addnew', text: 'Añadir Columna' },
    ],
    subGridConfigs: {
        locked: {
            flex: 1
        },
        normal: {
            flex: 1
        }
    },
}
)
const btnImport = ref(true)
const uploadMSP = async (file) => {
    let ganttImport = ganttrefimport.value.instance.value
    let projectLoaderScript = '/modImport/php/load.php'
    const importer = new Importer({
        // gantt panel reference
        ganttImport,
        // Columns that should be shown by the Gantt
        // if the imported file does not provide columns list
        defaultColumns: [
            { id: 'wbs', type: 'wbs', text: 'EDT' },
            { id: 'name', type: 'name', },
            { id: 'percentdone', type: 'percentdone', text: 'Avance', showCircle: true },
            { id: 'duration', type: 'duration', text: 'Duración' },
            { id: 'startdate', type: 'startdate', text: 'Fecha Inicio' },
            { id: 'enddate', type: 'enddate', text: 'Fecha fin' },
            { type: 'addnew', text: 'Añadir Columna' },
        ]
    });
    const formData = new FormData();

    formData.append('mpp-file', file);

    ganttImport.maskBody('Importando, espere por favor ...');

    try {
        const { parsedJson } = await AjaxHelper.post(projectLoaderScript, formData, { parseJson: true });
        if (parsedJson.success && parsedJson.data) {
            const { project } = ganttImport;
            // Import the uploaded mpp-file data (will make a new project and assign it to the gantt)
            await importer.importData(parsedJson.data);
            // destroy old project
            project.destroy();
            // set the view start date to the loaded project start
            ganttImport.setStartDate(ganttImport.project.startDate);
            await ganttImport.scrollToDate(ganttImport.project.startDate, { block: 'start' });
            // input.clear();
            // remove "Importing project ..." mask
            ganttImport.unmaskBody();
            // Toast.show('Importado con exito!');
            btnImport.value = false
        }
        else {
            console.log('Error al importar')
            console.log(parsedJson.msg);
            toast.add({ text: 'Ha ocurrido un error, verifique el archivo e intente nuevamente', severity: 'error', group: 'customToast', life: 3000 });
            // onError(`Error al importar: ${parsedJson.msg}`);
        }
    }
    catch (error) {
        toast.add({ text: 'Ha ocurrido un error, verifique el archivo e intente nuevamente', severity: 'error', group: 'customToast', life: 3000 });
        console.log(error);
    }
}
const loadImport = ref(false)
const importMSP = async () => {
    loadImport.value = true
    let ganttImport = ganttrefimport.value.instance.value
    let gantt = ganttref.value.instance.value
    const project = ref({
        autoSync: false,
        autoLoad: false,
        transport: {
            sync: {
                url: route('syncGantt', props.project),
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
                toast.add({ text: 'Ha ocurrido un error, verifique el archivo e intente nuevamente', severity: 'error', group: 'customToast', life: 3000 });
                loadImport.value = false
            },
        }
    })
    Object.assign(ganttImport.project, project.value)
    await ganttImport.project.sync()
    await gantt.project.load()
    modalImport.value = false
    loadImport.value = false
}

const undo = () => {
    let gantt = ganttref.value.instance.value
    gantt.project.stm.undo()
}
const redo = () => {
    let gantt = ganttref.value.instance.value
    gantt.project.stm.redo()
}

const critical =ref(false)
const showCritical = () => {
    critical.value=!critical.value
    let gantt = ganttref.value.instance.value
    gantt.features.criticalPaths.disabled = critical.value
}

//#endregion

</script>
<template>
    <AppLayout>
        <div id="ganttContainer" :class="full ? 'fixed bg-white z-50 top-0 left-0 h-screen w-screen' : 'h-[89vh]'"
            class="flex flex-col overflow-y-auto gap-y-1">
            <div class="rounded-t-lg h-8 flex justify-between cursor-default">
                <span class="bg-blue-800 flex justify-between rounded-tl-lg w-full">
                    <p class="text-md pl-3 flex items-center font-semibold capitalize text-white">
                        {{ props.project.name }}
                    </p>
                    <p :class="readOnly ? 'bg-success text-white font-bold ' : 'text-white bg-warning '"
                        class="px-3 flex items-center">{{ readOnly ? 'Modo lectura' : 'Modo edicion' }}</p>
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
            <span class="flex justify-between px-1">
                <span class="flex space-x-1">
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
                    <Button raised icon="fa-solid fa-gear" v-tooltip.bottom="'Ajustes'" severity="secondary"
                        @click="onSettingsShow" />
                    <Button raised icon="fa-solid fa-magnifying-glass" v-tooltip.bottom="'Zoom'" severity="secondary"
                        @click="zoom.toggle($event)" />
                    <Calendar dateFormat="dd/mm/yy" :manualInput="false" v-model="fecha" @dateSelect="onStartDateChange"
                        placeholder="Buscar por fecha" class=" !h-8 shadow-md" showIcon :pt="{ input: '!h-8' }" />
                    <InputText v-model="texto" @input="onFilterChange" placeholder="Buscar por actividad"
                        class="shadow-md" />
                    <Button raised v-tooltip.bottom="'Guardar en linea base'" icon="fa-solid fa-grip-lines"
                        @click="setLB.toggle($event);" v-if="!readOnly" />
                    <Button raised v-tooltip.bottom="'Ver lineas base'" icon="fa-solid fa-eye"
                        @click="seeLB.toggle($event)" />
                    <Button raised v-tooltip.bottom="'Exportar a PDF'" icon="fa-solid fa-file-pdf"
                        @click="onExportPDF()" />
                    <Button raised v-tooltip.bottom="'Exportar a XML'" icon="fa-solid fa-file-arrow-down"
                        @click="onExport()" />
                    <Button raised v-tooltip.bottom="'Importar desde MSProject'" v-if="!readOnly" type="input"
                        icon="fa-solid fa-upload" @click="modalImport = true" />
                    <Button raised v-tooltip.bottom="'Ruta critica'" severity="danger" icon="fa-solid fa-circle-exclamation"
                        @click="showCritical()" />
                </span>
                <span class="flex space-x-1">
                    <Button v-tooltip.left="readOnly ? 'Modo edicion' : 'Solo lectura'"
                        :icon="readOnly ? 'fa-solid fa-pen-to-square' : 'fa-solid fa-eye'" severity="help" raised
                        @click="editMode" />
                    <Button v-tooltip.left="full ? 'Pantalla normal' : 'Pantalla completa'"
                        :icon="full ? 'fa-solid fa-minimize' : 'fa-solid fa-maximize'" severity="help" raised
                        @click="full = !full" />
                </span>
            </span>
            <span class="h-full" :style="'font-size:' + fontSize + ';'">
                <BryntumGantt :filterFeature="true" :taskEditFeature="taskEdit" :projectLinesFeature="false"
                    :timelineScrollButtons="true" :cellEditFeature="cellEdit" :pdfExportFeature="pdfExport"
                    :mspExportFeature="true" :projectLines="true" :baselinesFeature="baselines" ref="ganttref"
                    class="h-full" :printFeature="true" v-bind="ganttConfig" :dependenciesFeature="{ radius: 5 }"
                    :timeRangesFeature="timeRanges" :taskTooltipFeature="taskTooltip"
                    :criticalPathsFeature="criticalPaths" />
            </span>
        </div>
    </AppLayout>
    <OverlayPanel id="setLB" ref="setLB" :pt="{ content: '!p-1' }">
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
    </OverlayPanel>
    <OverlayPanel id="zoom" ref="zoom" :pt="{ content: '!p-2' }">
        <div class="flex space-x-1">
            <Button raised icon="fa-solid fa-magnifying-glass-plus" v-tooltip.bottom="'Aumentar'" severity="primary"
                @click="onZoomInClick()" />
            <Button raised icon="fa-solid fa-magnifying-glass-minus" v-tooltip.bottom="'Reducir'" severity="warning"
                @click="onZoomOutClick()" />
            <Button raised icon="fa-solid fa-xmark" v-tooltip.bottom="'Reestablecer'" severity="danger"
                @click="onZoomToFitClick()" />
        </div>
    </OverlayPanel>
    <CustomModal v-model:visible="modalImport" icon="fa-solid fa-upload" titulo="Importar desde project">
        <template #body>
            <div class="w-full flex h-[70vh] flex-col">
                <div class="flex space-x-4 items-center">
                    <p>Seleccione el archivo a importar</p>
                    <FileUpload mode="basic" class="h-8" v-model:input="fileMSP" accept=".mpp"
                        @select="uploadMSP($event.files[0])" />
                </div>
                <BryntumGantt ref="ganttrefimport" class="h-full" v-bind="ganttConfigImporter" />
            </div>
        </template>
        <template #footer>
            <Button severity="danger" label="Cancelar" :disabled="loadImport" @click="modalImport = false" />
            <Button severity="success" :loading="loadImport" label="Importar" :disabled="btnImport"
                @click="importMSP" />
        </template>
    </CustomModal>
</template>


<style>

.b-export-header,
.b-export-footer {
    display: flex;
    color: #fff;
    background: #0076f8;
    align-items: center;
}

.b-export-header {
    text-align: start;
    height: 40px;
    position: relative;
    padding: 0.7em 1em 0.5em 1em;
    display: flex;
    flex-flow: row nowrap;
    justify-content: space-between;
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
