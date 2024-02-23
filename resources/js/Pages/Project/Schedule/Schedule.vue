<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import "@bryntum/gantt/gantt.material.css";
import '@bryntum/gantt/locales/gantt.locale.Es.js';
import { BryntumGantt } from '@bryntum/gantt-vue-3';
import { DateHelper, LocaleManager, StringHelper } from '@bryntum/gantt/gantt.module.js';
import Slider from 'primevue/slider'
import { useToast } from "primevue/usetoast";
import InputText from 'primevue/inputtext';
import Calendar from 'primevue/calendar';
import OverlayPanel from 'primevue/overlaypanel';
import Checkbox from 'primevue/checkbox';

const toast = useToast();

const props = defineProps({
    project: Object,
    groups: Array
})
LocaleManager.applyLocale('Es');
const ganttref = ref()
const loading = ref(false)
const error = ref(false)
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


const maximize = () => {
    let elementGantt = document.getElementById("containergantt");
    if (elementGantt.requestFullScreen) {
        elementGantt.requestFullScreen();
    } else if (elementGantt.mozRequestFullScreen) {
        elementGantt.mozRequestFullScreen();
    } else if (elementGantt.webkitRequestFullScreen) {
        elementGantt.webkitRequestFullScreen();
    }
}
const features = ref(
    {
        filter: true,
        mspExport: true,
        pdfExport: {
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
        },
        projectLines: false,
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
    })
const ganttConfig = ref({
    rowHeight: 28,
    dependencyIdField: 'sequenceNumber',
    project: {
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
        // listeners: {
        //     syncFail: (e) => {
        //         gantt.unmaskBody();
        //         toast('Ha ocurrido un error, reiniciando...', 'error');
        //         setTimeout(() => { location.reload() }, 3000);
        //     },
        //     beforeSync: () => {
        //         loading.value = true
        //     },
        //     sync: (e) => {
        //         loading.value = false
        //     }
        // }
    },
    columns: [
        { id: 'wbs', type: 'wbs', text: 'EDT' },
        { id: 'name', type: 'name', },
        { id: 'percentdone', type: 'percentdone', text: 'Avance', showCircle: true },
        { id: 'duration', type: 'duration', text: 'Duración' },
        { id: 'startdate', type: 'startdate', text: 'Fecha Inicio' },
        { id: 'enddate', type: 'enddate', text: 'Fecha fin' },
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
    keyMap: {
        // This is a function from the existing Gantt API
        'Ctrl+Shift+Q': 'addSubTask',
        'Ctrl+i': 'indent',
        'Ctrl+o': 'outdent',
        // 'Ctrl+z': 'outdent',
    },
    features: features
})




//#region toolbar


const onAddTaskClick = async () => {
    let gantt = ganttref.value.instance.value
    // console.log(gantt.value.instance.value)
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

// const onUndo = () => {
//     let gantt = ganttref.value.instance.value
//     console.log(gantt)
// }
// const onRedo = () => {
//     console.log(gantt.value.project)
// }

const fecha = ref()
function onStartDateChange(event) {
    let gantt = ganttref.value.instance.value
    console.log(event)
    // if (!oldValue) {
    //     // ignore initial set
    //     return;
    // }

    gantt.startDate = DateHelper.add(event, -1, "week");

    gantt.project.setStartDate(event);
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

const setConf=ref()
const rowHeight=ref()
const barMargin=ref()
const barMarginMax=ref()

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

//#endregion

</script>
<template>
    <AppLayout>
        <div class="h-[89vh] flex flex-col overflow-y-auto gap-y-1">
            <div class="rounded-t-lg h-8 flex space-x-0.5">
                <p
                    class="text-md w-full pl-3 bg-blue-800 flex items-center rounded-tl-lg font-semibold capitalize text-white">
                    {{ props.project.name }}
                </p>
                <span v-if="!error"
                    v-tooltip.bottom="loading ? 'Sincronizando cambios...' : 'Todos los cambios estan guardados'"
                    class="w-48 justify-end cursor-default px-2 flex items-center space-x-2 text-white bg-success rounded-tr-lg">
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
            <span class="flex justify-between">
                <span class="flex space-x-1">
                    <Button raised icon="fa-solid fa-plus" v-tooltip="'Nueva actividad'" severity="success"
                        @click=onAddTaskClick() />
                    <Button raised icon="fa-solid fa-pen" v-tooltip="'Editar Actividad'" severity="warning"
                        @click="onEditTaskClick()" />
                    <!-- <Button icon="fa-solid fa-rotate-left" severity="secondary" @click="onUndo()" />
                            <Button icon="fa-solid fa-rotate-right" severity="secondary" @click="onRedo()" /> -->
                    <Button raised icon="fa-solid fa-chevron-down" v-tooltip="'Expandir todo'" severity="secondary"
                        @click="onExpandAllClick()" />
                    <Button raised icon="fa-solid fa-chevron-up" v-tooltip="'Contraer todo'" severity="secondary"
                        @click="onCollapseAllClick()" />
                    <Button raised icon="fa-solid fa-gear" v-tooltip="'Ajustes'" severity="secondary"
                        @click="onSettingsShow" />
                    <Calendar dateFormat="dd/mm/yy" :manualInput="false" v-model="fecha" @dateSelect="onStartDateChange"
                        placeholder="Buscar por fecha" class=" !h-8 shadow-md" showIcon :pt="{ input: '!h-8' }" />
                    <InputText v-model="texto" @input="onFilterChange" placeholder="Buscar por actividad"
                        class="shadow-md" />
                    <Button raised v-tooltip="'Guardar en linea base'" icon="fa-solid fa-grip-lines"
                        @click="setLB.toggle($event);" />
                    <Button raised v-tooltip="'Ver lineas base'" icon="fa-solid fa-eye" @click="seeLB.toggle($event)" />
                    <Button raised v-tooltip="'Exportar a PDF'" icon="fa-solid fa-file-pdf" @click="onExportPDF" />
                    <Button raised v-tooltip="'Exportar a XML'" icon="fa-solid fa-file-arrow-down" @click="onExport" />
                </span>
                <span>
                    <Button v-tooltip.left="'Pantalla completa'" icon="fa-solid fa-maximize" severity="help" raised
                        @click="maximize" />
                </span>
            </span>
            <BryntumGantt id="containergantt" :features="features.features" ref="ganttref" class="h-full"
                v-bind="ganttConfig" />
        </div>
    </AppLayout>
    <OverlayPanel ref="setLB" :pt="{ content: '!p-1' }">
        <div class="flex flex-col space-y-1">
            <p>Lineas base</p>
            <span v-for="index in 4">
                <Button :label="'LB ' + index" text class="w-full" @click="setBaseline(index)" />
            </span>
        </div>
    </OverlayPanel>
    <OverlayPanel ref="seeLB" :pt="{ content: '!p-1' }">
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
    <OverlayPanel ref="setConf" :pt="{ content: '!p-4' }">
        <div class="flex flex-col h space-y-3">
            <span>
                <Slider v-model="rowHeight" @change="onSettingsRowHeightChange" />
                <p>Altura de celdas ({{rowHeight}})</p>
            </span>
            <span>
                <Slider v-model="barMargin" :max="barMarginMax" @change="onSettingsMarginChange"/>
                <p>Altura de barras ({{ barMargin }})</p>
            </span>
        </div>
    </OverlayPanel>
</template>
<style>
/* .b-export .b-panel {
    overflow: visible !important;
    contain: none !important;
} */

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

/* .b-export .b-sch-canvas {
    width: 100% !important;
    background-color: #62ff65 !important;
    overflow: visible !important;
    contain: none !important;
    height: 100% !important;
} */

#id {
    font-size: 12px !important;
}
</style>
