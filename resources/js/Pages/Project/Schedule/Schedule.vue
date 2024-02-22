<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import "@bryntum/gantt/gantt.material.css";
import '@bryntum/gantt/locales/gantt.locale.Es.js';
import { BryntumGantt } from '@bryntum/gantt-vue-3';
import { DateHelper, LocaleManager } from '@bryntum/gantt/gantt.module.js';

import { useToast } from "primevue/usetoast";
import Toolbar from 'primevue/toolbar';
import CustomInput from '@/Components/CustomInput.vue';
import InputText from 'primevue/inputtext';
import Calendar from 'primevue/calendar';

const toast = useToast();

const props = defineProps({
    project: Object,
    groups: Array
})
LocaleManager.applyLocale('Es');
const ganttref = ref()
const loading = ref(false)
const error = ref(false)

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
const texto=ref()
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
                        @click="onCollapseAllClick()" />
                    <Calendar dateFormat="dd/mm/yy" :manualInput="false" v-model="fecha" @dateSelect="onStartDateChange"
                        placeholder="Buscar por fecha" class="shadow-md" showIcon :pt="{ input: '!h-8' }" />
                    <InputText v-model="texto" @input="onFilterChange" placeholder="Buscar por actividad" class="shadow-md" />
                    <Button raised v-tooltip="'Guardar en linea base'" icon="fa-solid fa-grip-lines" />
                    <Button raised v-tooltip="'Ver lineas base'" icon="fa-solid fa-eye" />
                    <Button raised v-tooltip="'Exportar a PDF'" icon="fa-solid fa-file-pdf" />
                </span>
                <span>
                    <Button v-tooltip.left="'Pantalla completa'" icon="fa-solid fa-maximize" severity="help" raised
                        @click="maximize" />
                </span>
            </span>
            <BryntumGantt id="containergantt" ref="ganttref" class="h-full" v-bind="ganttConfig" />
        </div>
    </AppLayout>
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
}</style>
