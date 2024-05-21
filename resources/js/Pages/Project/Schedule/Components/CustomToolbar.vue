<script setup>
import { ref } from 'vue';
import { useToast } from "primevue/usetoast";
import CustomColorSelect from '@/Components/CustomColorSelect.vue';
import OverlayPanel from 'primevue/overlaypanel';
import CustomModalCalendars from './CustomModalCalendars.vue';
import CustomNotesGantt from './CustomNotesGantt.vue';
import InputText from 'primevue/inputtext';
import Calendar from 'primevue/calendar';
import CustomExportGantt from './CustomExportGantt.vue';
import CustomImportGantt from './CustomImportGantt.vue';
const toast = useToast();
const zoom = ref()

const props = defineProps({
    project: {
        type: Object,
    },
    notes: Object,
    listCalendar: Array
})

const full = defineModel('full', {
    type: Boolean,
    default: false
})
const gantt = defineModel('gantt', {
    type: Object,
})
const config = defineModel('config', {
    type: Object,
})
const taskFilter = ref()

const fecha = ref()


// #region eventos
const addTaskClick = async () => {
    if (props.project.calendar_id != null) {
        const added = gantt.value.taskStore.rootNode.appendChild({
            name: "Nueva tarea",
            duration: 1
        });
        gantt.value.indent(added)
        // wait for immediate commit to calculate new task fields
        await gantt.value.project.commitAsync();

        // scroll to the added task
        await gantt.value.scrollRowIntoView(added);

        gantt.value.features.cellEdit.startEditing({
            record: added,
            field: "name"
        });
    } else {
        toast.add({ severity: 'error', group: 'customToast', text: 'Primero debe asociar un calendario', life: 2000 });
    }
}

const editTaskClick = () => {
    if (props.gantt.selectedRecord) {
        props.gantt.editTask(props.gantt.selectedRecord);
    } else {
        toast.add({ severity: 'error', group: 'customToast', text: 'Debe seleccionar la tarea a editar', life: 2000 });
    }
}

const editMode = () => {
    gantt.value.readOnly = !gantt.value.readOnly
    gantt.value.project.autoSync = !gantt.value.readOnly
    config.value.readOnly = gantt.value.readOnly
    if (config.value.readOnly == false) {
        gantt.value.project.stm.enable()
    }
}

function changeColorRow(color) {
    if (gantt.value.selectedRecords.length > 0) {
        gantt.value.selectedRecords.forEach((task) => {
            task.set('rowcolor', color ? color : 'default')
        })
    } else {
        toast.add({ severity: 'error', group: 'customToast', text: 'Debe seleccionar la tarea a editar', life: 2000 });
    }
}

function filterChange() {
    if (taskFilter.value === "") {
        gantt.value.taskStore.clearFilters();
    } else {
        taskFilter.value = taskFilter.value.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
        gantt.value.taskStore.filter({
            filters: task =>
                task.name && task.name.match(new RegExp(taskFilter.value, "i")),
            replace: true
        });
    }
}

// #endregion


const options = ref({
    key: (Math.random() * 10000 * 10000).toFixed(0),
    buttonLeft: [
        // Expandir todo
        {
            type: 'button',
            icon: () => 'fa-solid fa-chevron-down',
            tooltip: () => 'Expandir todo',
            severity: 'info',
            event: () => gantt.value.expandAll(),
            show: () => true
        },
        // Contraer todo
        {
            type: 'button',
            icon: () => 'fa-solid fa-chevron-up',
            tooltip: () => 'Contraer todo',
            severity: 'info',
            event: () => gantt.value.collapseAll(),
            show: () => true
        },
        // Ver ruta critica
        {
            type: 'button',
            icon: () => 'fa-solid fa-circle-exclamation',
            tooltip: () => 'Ruta critica',
            severity: 'danger',
            event: () => {
                gantt.value.features.criticalPaths.disabled = gantt.value.features.criticalPaths.disabled ? false : true
            },
            show: () => true
        },
        // Nueva actividad
        {
            type: 'button',
            icon: () => 'fa-solid fa-plus',
            tooltip: () => 'Nueva actividad',
            severity: 'success',
            event: () => addTaskClick(),
            show: () => !config.value.readOnly
        },
        // Editar actividad
        {
            type: 'button',
            icon: () => 'fa-solid fa-pencil',
            tooltip: () => 'Editar Actividad',
            severity: 'success',
            event: () => editTaskClick(),
            show: () => !config.value.readOnly
        },
        // Deshacer
        {
            type: 'button',
            icon: () => 'fa-solid fa-rotate-left',
            tooltip: () => 'Deshacer',
            severity: 'secondary',
            event: () => gantt.value.project.stm.undo(),
            show: () => !config.value.readOnly,
            disabled: () => !config.value.canUndo
        },
        // Rehacer
        {
            type: 'button',
            icon: () => 'fa-solid fa-rotate-right',
            tooltip: () => 'Rehacer',
            severity: 'secondary',
            event: () => gantt.value.project.stm.redo(),
            show: () => !config.value.readOnly,
            disabled: () => !config.value.canRedo
        },
        // Zoom
        {
            type: 'button',
            icon: () => 'fa-solid fa-magnifying-glass',
            tooltip: () => 'Zoom',
            severity: 'secondary',
            event: (event) => zoom.value.toggle(event),
            show: () => true
        },

    ],
    buttonRight: [
        {
            type: 'button',
            icon: () => 'fa-solid fa-file-arrow-down',
            tooltip: () => 'Exportar a XML',
            severity: 'primary',
            event: () => {
                const filename = props.project.SAP_code + '-' + props.project.name + '.xml'
                gantt.value.features.mspExport.export({
                    filename
                })
            },
            show: () => true
        },
        // Modo edicion/lectura
        {
            type: 'button',
            icon: () => {
                return !config.value.readOnly ? 'fa-solid fa-pen-to-square' : 'fa-solid fa-eye'
            },
            tooltip: () => { return !config.value.readOnly ? 'Modo edicion' : 'Solo lectura' },
            severity: 'info',
            event: () => editMode(),
            show: () => true
        },
        // Maximizar/minimizar
        {
            type: 'button',
            icon: () => {
                return full.value ? 'fa-solid fa-minimize' : 'fa-solid fa-maximize'
            },
            tooltip: () => { return full ? 'Pantalla normal' : 'Pantalla completa' },
            severity: 'info',
            event: () => config.value.full = !config.value.full,
            show: () => true
        },
    ]
})

{/* <Button raised v-tooltip.bottom="'Ver notas'" :disabled="notes.data.length == 0" :loading="notes.load"
    severity="success" icon="fa-regular fa-note-sticky" @click="visibleNotes = true" /> */}

</script>
<template>
    <div class="px-1 flex justify-between">
        <div class="flex flex-wrap" :key="options.key">
            <span v-for="item, index in options.buttonLeft">
                <span v-if="item?.show() ?? true">
                    <span v-if="item.type == 'button'">
                        <Button class="ml-1" raised :icon="item.icon()" :disabled="item.disabled" :text="item.text"
                            v-tooltip.bottom="item.tooltip()" :severity="item.severity" @click="item.event" />
                    </span>
                </span>
            </span>
            <CustomImportGantt class="ml-1" v-model:gantt="gantt" v-if="!config.readOnly" :project="props.project"/>
            <CustomNotesGantt class="ml-1" :notes="notes" v-model:taskFilter="taskFilter" />
            <CustomModalCalendars class="ml-1" :listCalendar :project="props.project" v-if="!config.readOnly" />
            <CustomColorSelect v-if="!config.readOnly" class="ml-1" @changeColorRow="changeColorRow($event)"
                :gama="'200'" />
            <InputText v-model="taskFilter" @input="filterChange" placeholder="Buscar por actividad"
                class="shadow-md ml-1 hidden sm:flex" />
            <Calendar dateFormat="dd/mm/yy" :manualInput="false" v-model="fecha"
                @dateSelect="gantt.scrollToDate(fecha, { animate: 300, block: 'start' });"
                placeholder="Buscar por fecha" class="hidden ml-1 sm:flex !h-8 shadow-md" showIcon :pt="{ input: '!h-8' }" />
        </div>
        <div class="flex">
            <CustomExportGantt v-model:gantt="gantt" :project="props.project"></CustomExportGantt>
            <span v-for="item, index in options.buttonRight">
                <span v-if="item.type == 'button' && (item?.show() ?? true)">
                    <Button class="ml-1" :raised="item.raised" :icon="item.icon()" :disabled="item.disabled"
                        :text="item.text" v-tooltip.bottom="item.tooltip()" :severity="item.severity"
                        @click="item.event" />
                </span>
            </span>
        </div>
    </div>
    <!--
            
<Button raised icon="fa-solid fa-gear" v-tooltip.bottom="'Ajustes'" severity="secondary" @click="onSettingsShow" />
<Button raised v-tooltip.bottom="'Guardar en linea base'" icon="fa-solid fa-grip-lines" @click="setLB.toggle($event);"
    v-if="!readOnly" />
<Button raised v-tooltip.bottom="'Ver lineas base'" icon="fa-solid fa-eye" @click="seeLB.toggle($event)" />
<Button raised v-tooltip.bottom="'Exportar a PDF'" icon="fa-solid fa-file-pdf" @click="showModalExport()" />

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
</div> -->

    <OverlayPanel ref="zoom" :pt="{ content: '!p-2' }">
        <div class="flex space-x-1">
            <Button raised icon="fa-solid fa-magnifying-glass-plus" v-tooltip.bottom="'Aumentar'" severity="primary"
                @click="gantt.zoomIn()" />
            <Button raised icon="fa-solid fa-magnifying-glass-minus" v-tooltip.bottom="'Reducir'" severity="warning"
                @click="gantt.zoomOut()" />
            <Button raised icon="fa-solid fa-xmark" v-tooltip.bottom="'Reestablecer'" severity="danger"
                @click="gantt.zoomOutFull()" />
        </div>
    </OverlayPanel>

</template>