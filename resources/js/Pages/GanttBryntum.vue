<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { onMounted} from 'vue';
import "@bryntum/gantt/gantt.material.css";
import '@bryntum/gantt/locales/gantt.locale.Es.js';
import { DateHelper, Gantt, List, LocaleManager, ProjectModel, StringHelper, Widget } from '@bryntum/gantt/gantt.module.js';
import '../../css/app.scss'
import { useSweetalert } from '@/composable/sweetAlert';
const { toast } = useSweetalert();
const props = defineProps({
    project: Object,
    groups: Array
})
LocaleManager.applyLocale('Es');

//#region colocar tab personalizado en el editor de tareas

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

//#endregion

const project = new ProjectModel({
    autoSync: true,
    autoLoad: true,
    transport: {
        load: {
            url: route('dataGantt', props.project)
            // url: 'data/launch-saas.json'
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
    // This config enables response validation and dumping of found errors to the browser console.
    // It's meant to be used as a development stage helper only so please set it to false for production systems.
    validateResponse: true,
    listeners: {
        syncFail: (e) => {
            gantt.unmaskBody();
            // console.log(e);
            toast('Ha ocurrido un error, reiniciando...', 'error');
            setTimeout(() => { location.reload() }, 3000);

        }
    }

});

const gantt = new Gantt(({
    project,
    // resourceImageFolderPath: '../images/users/',
    dependencyIdField: 'sequenceNumber',
    rowHeight: 30,
    columns: [
        { type: 'wbs', text: 'EDT', width: 20 },
        { type: 'name', width: 280 },
        { type: 'percentdone', text: 'Avance', showCircle: true, width: 20 },
        { type: 'duration', text: 'Duración', with: 20 },
        { type: 'startdate', text: 'Fecha Inicio', with: 20 },
        { type: 'enddate', text: 'Fecha fin', with: 20 },
        {
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
        { type: 'addnew', text: 'Añadir Columna', with: 50 },
        {
            text: 'Linea Base 1',
            collapsible: true,
            children: [
                { type: 'baselinestartdate', text: 'Start', field: 'baselines[0].startDate' },
                { type: 'baselineenddate', text: 'Finish', field: 'baselines[0].endDate' },
                { type: 'baselineduration', text: 'Duration', field: 'baselines[0].fullDuration' },
                { type: 'baselinestartvariance', field: 'baselines[0].startVariance' },
                { type: 'baselineendvariance', field: 'baselines[0].endVariance' },
                { type: 'baselinedurationvariance', field: 'baselines[0].durationVariance' }
            ]
        },
        {
            text: 'Linea Base 2',
            collapsible: true,
            collapsed: true,
            children: [
                { type: 'baselinestartdate', text: 'Start', field: 'baselines[1].startDate' },
                { type: 'baselineenddate', text: 'Finish', field: 'baselines[1].endDate' },
                { type: 'baselineduration', text: 'Duration', field: 'baselines[1].fullDuration' },
                { type: 'baselinestartvariance', field: 'baselines[1].startVariance' },
                { type: 'baselineendvariance', field: 'baselines[1].endVariance' },
                { type: 'baselinedurationvariance', field: 'baselines[1].durationVariance' }
            ]
        },
        {
            text: 'Linea Base 3',
            collapsible: true,
            collapsed: true,
            children: [
                { type: 'baselinestartdate', text: 'Start', field: 'baselines[2].startDate' },
                { type: 'baselineenddate', text: 'Finish', field: 'baselines[2].endDate' },
                { type: 'baselineduration', text: 'Duration', field: 'baselines[2].fullDuration' },
                { type: 'baselinestartvariance', field: 'baselines[2].startVariance' },
                { type: 'baselineendvariance', field: 'baselines[2].endVariance' },
                { type: 'baselinedurationvariance', field: 'baselines[2].durationVariance' }
            ]
        }
    ],
    // Allow extra space for baseline(s)
    subGridConfigs: {
        locked: {
            flex: 1
        },
        normal: {
            flex: 1
        }
    },
    features: {
        filter: true,
        projectLines: false,
        mspExport: true,
        taskEdit: {
            items: {
                resourcesTab: {
                    type: 'resourcelist',
                    weight: 120,
                    title: 'Recursos',
                },
            }
        },
        baselines: {
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
        },
    },
    keyMap: {
        // This is a function from the existing Gantt API
        'Ctrl+Shift+Q': 'addSubTask',
        'Ctrl+i': 'indent',
        'Ctrl+o': 'outdent',

    },
    tbar: {
        height: '4em',
        items: [
            {
                type: "buttonGroup",
                items: [
                    {
                        color: "b-green",
                        ref: "addTaskButton",
                        icon: "b-fa b-fa-plus",
                        text: "",
                        tooltip: "Crear una nueva tarea",
                        onAction() {
                            onAddTaskClick()
                        }
                    }
                ]
            },
            {
                type: "buttonGroup",
                items: [
                    {
                        ref: "editTaskButton",
                        icon: "b-fa b-fa-pen",
                        text: "",
                        tooltip: "Editar celda seleccionada",
                        onAction() {
                            onEditTaskClick()
                        }
                    },
                    {
                        ref: "undoRedo",
                        type: "undoredo",
                        items: {
                            transactionsCombo: null
                        }
                    }
                ]
            },
            {
                type: "buttonGroup",
                items: [
                    {
                        ref: "expandAllButton",
                        icon: "b-fa b-fa-angle-double-down",
                        tooltip: "Expandir todo",
                        onAction() {
                            onExpandAllClick()
                        }
                    },
                    {
                        ref: "collapseAllButton",
                        icon: "b-fa b-fa-angle-double-up",
                        tooltip: "Colapsar todo",
                        onAction() {
                            onCollapseAllClick()
                        }
                    }
                ]
            },
            {
                type: "buttonGroup",
                items: [
                    {
                        type: "button",
                        ref: "settingsButton",
                        tooltip: "Ajustes",
                        toggleable: true,
                        icon: 'b-fa-gear',
                        menu: {
                            type: "popup",
                            anchor: true,
                            cls: "settings-menu",
                            layoutStyle: {
                                flexDirection: "column"
                            },
                            onBeforeShow(e) {
                                onSettingsShow(e)
                            },

                            items: [
                                {
                                    type: "slider",
                                    ref: "rowHeight",
                                    text: "Altura de celdas",
                                    width: "12em",
                                    showValue: true,
                                    min: 10,
                                    max: 70,
                                    onInput(e) {
                                        onSettingsRowHeightChange(e)
                                    }
                                },
                                {
                                    type: "slider",
                                    ref: "barMargin",
                                    text: "Altura barras",
                                    width: "12em",
                                    showValue: true,
                                    min: 0,
                                    max: 10,
                                    onInput(e) {
                                        onSettingsMarginChange(e)
                                    }
                                },
                            ]
                        }
                    },
                    // {
                    //     type: "button",
                    //     color: "b-red",
                    //     ref: "criticalPathsButton",
                    //     icon: "b-fa b-fa-fire",
                    //     text: "Critical paths",
                    //     tooltip: "Highlight critical paths",
                    //     toggleable: true,
                    //     onAction: "up.onCriticalPathsClick"
                    // }
                ]
            },
            {
                type: "datefield",
                ref: "startDateField",
                placeholder: "Busqueda por fecha",
                maxWidth: "12em",
                required: 'done',
                flex: "0 0 17em",
                listeners: {
                    change() {
                        onStartDateChange()
                    }
                }
            },
            {
                type: "textfield",
                ref: "filterByName",
                cls: "filter-by-name",
                flex: "0 0 12.5em",
                // Placeholder for others
                placeholder: "Buscar Actividad",
                clearable: true,
                keyStrokeChangeDelay: 100,
                triggers: {
                    filter: {
                        align: "end",
                    }
                },
                onChange(e) {
                    onFilterChange(e)
                }
            },
            {
                type: 'button',
                ref: 'mspExportBtn',
                hidden: true,
                tooltip: "Exportar a XML",
                icon: 'b-fa-file-export',
                onAction() {
                    onExport()
                }
            },
            {
                type: 'button',
                text: 'LB',
                cls: '',
                // hidden:true,
                tooltip: "Guardar en linea base",
                menu: [{
                    text: 'Linea base 1',
                    onItem() {
                        setBaseline(1);
                    }
                }, {
                    text: 'Linea base 2',
                    onItem() {
                        setBaseline(2);
                    }
                }, {
                    text: 'Linea base 3',
                    onItem() {
                        setBaseline(3);
                    }
                }]
            },
            {
                type: 'button',
                icon: "b-fa b-fa-eye",
                tooltip: "Ver lineas base",
                menu: [{
                    checked: true,
                    text: 'Linea Base 1',
                    onToggle({ checked }) {
                        toggleBaselineVisible(1, checked);
                    }
                }, {
                    checked: true,
                    text: 'Linea base 2',
                    onToggle({ checked }) {
                        toggleBaselineVisible(2, checked);
                    }
                }, {
                    checked: true,
                    text: 'Linea base 3',
                    onToggle({ checked }) {
                        toggleBaselineVisible(3, checked);
                    }
                }]
            },
            // {
            //     type: 'checkbox',
            //     text: 'Mostrar todas',
            //     checked: true,
            //     toggleable: true,
            //     onAction({ checked }) {
            //         gantt.features.baselines.disabled = !checked;
            //     }
            // },
            // {
            //     type: 'checkbox',
            //     text: 'Habilitar lineas base',
            //     cls: 'b-baseline-toggle',
            //     checked: true,
            //     toggleable: true,
            //     onAction({ checked }) {
            //         gantt.features.baselines.renderer = checked ? baselineRenderer : () => { };
            //     }
            // }
        ]
    },
}))

//#region baseline

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
const setBaseline = (index) => {
    gantt.taskStore.setBaseline(index);
}

const toggleBaselineVisible = (index, visible) => {
    gantt.element.classList[visible ? 'remove' : 'add'](`b-hide-baseline-${index}`);
}
//#endregion

onMounted(() => {
    gantt.appendTo = 'container';
})
//#region toolbar

//exportar
const onExport = () => {
    // give a filename based on task name
    const filename = gantt.project.taskStore.first && `${gantt.project.taskStore.first.name}.xml`;

    // call the export to download the XML file
    gantt.features.mspExport.export({
        filename
    });
}


// añadir tarea

const onAddTaskClick = async () => {
    const added = gantt.taskStore.rootNode.appendChild({
        name: "Nueva tarea",
        duration: 1
    });

    // wait for immediate commit to calculate new task fields
    await gantt.project.commitAsync();

    // scroll to the added task
    await gantt.scrollRowIntoView(added);

    gantt.features.cellEdit.startEditing({
        record: added,
        field: "name"
    });
}

//editar tarea

const onEditTaskClick = () => {

    if (gantt.selectedRecord) {
        gantt.editTask(gantt.selectedRecord);
    } else {
        Toast.show("Primero selecciona una tarea a editar");
    }
}

//expandir todas las tareas
const onExpandAllClick = () => {
    gantt.expandAll();
}

//recojer todas las tareas
const onCollapseAllClick = () => {
    gantt.collapseAll();
}

//buscar por fecha
function onStartDateChange({ value, oldValue }) {
    if (!oldValue) {
        // ignore initial set
        return;
    }

    gantt.startDate = DateHelper.add(value, -1, "week");

    gantt.project.setStartDate(value);
}

//buscar por nombre
const onFilterChange = ({ value }) => {
    if (value === "") {
        gantt.taskStore.clearFilters();
    } else {
        value = value.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");

        gantt.taskStore.filter({
            filters: task =>
                task.name && task.name.match(new RegExp(value, "i")),
            replace: true
        });
    }
}

//ver ajustes
const onSettingsShow = ({ source: menu }) => {
    const { rowHeight, barMargin } = menu.widgetMap;
    rowHeight.value = gantt.rowHeight;
    barMargin.value = gantt.barMargin;
    barMargin.max = gantt.rowHeight / 2 - 5;
}
//ajuste de altura de filas
const onSettingsRowHeightChange = ({ value }) => {
    gantt.rowHeight = value;
    gantt.widgetMap.settingsButton.menu.widgetMap.barMargin.max =
        value / 2 - 5;
}
//ajuste de marjen
const onSettingsMarginChange = ({ value }) => {
    gantt.barMargin = value;
}

//#endregion


</script>
<template>
    <AppLayout>
        <div class="grid grid-cols-5 justify-center w-full h-[10vh] overflow-y-auto">
            <p>{{ props.project.name }}</p>
        </div>
        <div class="grid h-full grid-cols-1 grid-rows-1">
            <div id="container" class="h-[80vh] flex-1 text-xs">
            </div>
        </div>
    </AppLayout>
</template>
<style>
#id {
    font-size: 12px !important;
}
</style>
