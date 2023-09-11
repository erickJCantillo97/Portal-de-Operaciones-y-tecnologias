<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, reactive, onMounted } from 'vue';
import { BryntumGantt } from '@bryntum/gantt-vue-3';
// import { ganttConfig } from '@/AppConfig';
import "@bryntum/gantt/gantt.material.css";
import '@bryntum/gantt/locales/gantt.locale.Es.js';
import { AssignmentField, Gantt, LocaleManager, ProjectModel, Widget } from '@bryntum/gantt/gantt.module.js';
import '@/GanttToolbar.js'

const props = defineProps({
    project: Object,
})

LocaleManager.applyLocale('Es');
// const ganttconf = reactive(ganttConfig(props.project));
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
    validateResponse: true
});

const gantt = new Gantt(({
    columns: [
        { type: 'wbs', text: 'Nivel', width: 20 },
        { type: 'name', width: 200 },
        { type: 'percentdone', text: 'Avance', showCircle: true, width: 80 },
        { type: 'duration', text: 'Duración', with: 50 },
        { type: 'startdate', text: 'Fecha Inicio', with: 50 },
        { type: 'enddate', text: 'Fecha fin', with: 50 },
        {
            type: 'resourceassignment',
            width: 250,
            showAvatars: true,
            editor: {
                type: 'assignmentfield',
                picker: {
                    height: 350,
                    width: 450,
                    features: {
                        filterBar: false,
                        headerMenu: false,
                        cellMenu: false,
                    },
                    // The extra columns are concatenated onto the base column set.
                    columns: [
                        {
                            text: 'Cantidad',
                            // Read a nested property (name) from the resource calendar
                            field: 'resource.quantity',
                            editor: true,
                            width: 85
                        }]
                }
            }
        },
    ],
    keyMap: {
        // This is a function from the existing Gantt API
        'Ctrl+Shift+Q': 'addSubTask',
        'Ctrl+i': 'indent',
        'Ctrl+o': 'outdent',

    },
    tbar: {
        type: 'gantttoolbar',
        height: '4em'
    },
    project,
    dependencyIdField: 'sequenceNumber',
    features: {
    },
}))
gantt.project.load();
onMounted(() => {
    gantt.appendTo = 'container'
})



</script>
<template>
    <AppLayout title="">
        <div class="">
            <div class="h-full">
                <div class="overflow-hidden shadow-xl sm:rounded-lg h-screen">
                    <!-- <bryntum-gantt v-bind="ganttconf" /> -->
                    <div id="container" class="overflow-hidden shadow-xl sm:rounded-lg h-full"></div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
