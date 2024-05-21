<script setup>
import CustomModal from '@/Components/CustomModal.vue';
import { BryntumGantt } from '@bryntum/gantt-vue-3';
import FileUpload from 'primevue/fileupload';
import { ref } from 'vue';
import { useToast } from "primevue/usetoast";
const toast = useToast();
const props = defineProps({
    class: String,
    project: Object
})

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

const gantt = defineModel('gantt', {
    type: Object,
})

const modalImport = ref(false)

const fileMSP = ref()
const ganttrefimport = ref()
const ganttConfigImporter = ref({
    emptyText: 'Seleccione un archivo',
    startDate: new Date(),
    endDate: new Date((new Date).getFullYear(), (new Date).getMonth(), (new Date).getDate() + 30),
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
let auxproj
const btnImport = ref(true)
const uploadMSP = async (file) => {
    let ganttImport = ganttrefimport.value.instance.value
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

    formData.append('mppFile', file);

    ganttImport.maskBody('Importando, espere por favor ...');

    try {
        await axios.post(route('ganttImporter'), formData)
            .then(async (res) => {
                if (res.status == 200) {
                    const { project } = ganttImport;
                    await importer.importData(res.data);
                    auxproj = res.data.project
                    // project.destroy();
                    ganttImport.setStartDate(ganttImport.project.startDate);
                    await ganttImport.scrollToDate(ganttImport.project.startDate, { block: 'start' });
                    btnImport.value = false
                } else {
                    console.log(res)
                    toast.add({ text: 'Ha ocurrido un error, verifique el archivo e intente nuevamente', severity: 'error', group: 'customToast', life: 3000 });
                }
            })
            .catch((error) => {
                console.log(error)
                toast.add({ text: 'Ha ocurrido un error, verifique el archivo e intente nuevamente', severity: 'error', group: 'customToast', life: 3000 });
            })
        ganttImport.unmaskBody();
    }
    catch (error) {
        toast.add({ text: 'Ha ocurrido un error, verifique el archivo e intente nuevamente', severity: 'error', group: 'customToast', life: 3000 });
        console.log(error);
        ganttImport.unmaskBody();
    }
}
const loadImport = ref(false)
const importMSP = async () => {
    loadImport.value = true
    let ganttImport = ganttrefimport.value.instance.value
    let project = {
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
    }
    Object.assign(ganttImport.project, project)
    const dataImport = JSON.parse(JSON.stringify(ganttImport.project))
    const projectImport = dataImport.project
    const calendarImport = dataImport.calendarsData.find((calendar) => { return calendar.id === projectImport.calendar })
    await axios.post(route('before.sync', props.project), { project: auxproj, calendar: calendarImport })
        .then(async (res) => {
            // console.log(res)
            await ganttImport.project.sync()
            await gantt.value.project.load()
            toast.add({ text: 'Importado correctamente', severity: 'info', group: 'customToast', life: 3000 });
        })
        .catch((error) => {
            console.log(error)
            toast.add({ text: 'Hubo un error', severity: 'info', group: 'customToast', life: 3000 });
        })
    modalImport.value = false
    loadImport.value = false
}

</script>

<template>
    <Button raised v-tooltip.bottom="'Importar desde MSProject'" icon="fa-solid fa-upload"
        @click="modalImport = true" />

    <CustomModal v-model:visible="modalImport" icon="fa-solid fa-upload" titulo="Importar desde project" width="80vw">
        <template #body>
            <div class="w-full flex h-[70vh] flex-col space-y-2">
                <div class="flex space-x-4 items-center">
                    <p>Seleccione el archivo a importar</p>
                    <FileUpload mode="basic" class="h-8" v-model:input="fileMSP" accept=".mpp"
                        @select="uploadMSP($event.files[0])" />
                </div>
                <p class="text-center font-bold text-xl">Previsualizacion del proyecto</p>
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