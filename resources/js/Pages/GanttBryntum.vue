<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { reactive, onMounted } from 'vue';
import "@bryntum/gantt/gantt.material.css";
import '@bryntum/gantt/locales/gantt.locale.Es.js';
import { Gantt, List, LocaleManager, ProjectModel, StringHelper, Widget } from '@bryntum/gantt/gantt.module.js';
import '@/GanttToolbar.js'
import '../../css/app.scss'

const props = defineProps({
    project: Number,
})
LocaleManager.applyLocale('Es');

//#region colocar tab personalizado en el editor de tareas
// if (!Widget.factoryable.registry.custom_filestab) {
//     // Custom FilesTab class (the last item of tabsConfig)
//     class FilesTab extends Grid {

//         // Factoryable type name
//         static get type() {
//             return 'custom_filestab';
//         }

//         static get defaultConfig() {
//             return {
//                 title    : 'Archivos',
//                 defaults : {
//                     labelWidth : 200
//                 },
//                 columns : [{
//                     text     : 'Archivos asociados a la actividad',
//                     field    : 'name',
//                     type     : 'template',
//                     template : data => `<i class="b-fa b-fa-fw b-fa-${data.record.data.icon}"></i>${data.record.data.name}`
//                 }]
//             };
//         } // eo getter defaultConfig

//         loadEvent(eventRecord) {
//             let files = [];

//             // prepare dummy files data
//             switch (eventRecord.data.id) {
//                 case 1:
//                     files = [
//                         { name : 'Image1.png', icon : 'image' },
//                         { name : 'Chart2.pdf', icon : 'chart-pie' },
//                         { name : 'Spreadsheet3.pdf', icon : 'file-excel' },
//                         { name : 'Document4.pdf', icon : 'file-word' },
//                         { name : 'Report5.pdf', icon : 'user-chart' }
//                     ];
//                     break;
//                 case 2:
//                     files = [
//                         { name : 'Chart11.pdf', icon : 'chart-pie' },
//                         { name : 'Spreadsheet13.pdf', icon : 'file-excel' },
//                         { name : 'Document14.pdf', icon : 'file-word' }
//                     ];
//                     break;
//                 case 3:
//                     files = [
//                         { name : 'Image21.png', icon : 'image' },
//                         { name : 'Spreadsheet23.pdf', icon : 'file-excel' },
//                         { name : 'Document24.pdf', icon : 'file-word' },
//                         { name : 'Report25.pdf', icon : 'user-chart' }
//                     ];
//                     break;
//             } // eo switch

//             this.store.data = files;
//         } // eo function loadEvent
//     } // eo class FilesTab

//     // register 'filestab' type with its Factory
//     FilesTab.initClass();
// }

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
    validateResponse: true
});


const gantt = new Gantt(({
    project,
    // resourceImageFolderPath: '../images/users/',
    dependencyIdField: 'sequenceNumber',
    columns: [
        { type: 'wbs', text: 'Nivel', width: 20 },
        { type: 'name', width: 200 },
        { type: 'percentdone', text: 'Avance', showCircle: true, width: 80 },
        { type: 'duration', text: 'Duración', with: 50 },
        { type: 'startdate', text: 'Fecha Inicio', with: 50 },
        { type: 'enddate', text: 'Fecha fin', with: 50 },
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
                    overflowAssignments2 += ('<div class="text-xs flex justify-between space-x-2 mt-2">'
                        + '<div>' + element.units / 100 + '</div>'
                        + '<div class="italic">' + element.name + '</div>' +
                        '<div class="font-bold"> $' + Math.round(task.durationUnit == 'day' ? (task.duration * (element.units / 100) * element.costo_hora) * 8.5 : (task.duration * (element.units / 100) * element.costo_hora)).toLocaleString('es')
                        + '</div></div>')
                }
                return `
                    <div class="text-xs flex justify-between space-x-2"><div>${assignmentRecord.units / 100}</div><div class="italic">${assignmentRecord.name}</div><div class="font-bold">$${Math.round(task.durationUnit == 'day' ? (task.duration * (assignmentRecord.units / 100) * assignmentRecord.costo_hora) * 8.5 : (task.duration * (assignmentRecord.units / 100) * assignmentRecord.costo_hora)).toLocaleString('es')} </div></div>
                     ${overflowCount > 0 ? `${overflowAssignments2}` : ''}
                `;
            }
        },
    ],
    features: {
        projectLines: false,
        taskEdit: {
            items: {
                resourcesTab: {
                    type: 'resourcelist',
                    weight: 120,
                    title: 'Recursos',
                },
            }
        }
    },
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

}))
onMounted(() => {
    gantt.appendTo = 'container';
})

</script>
<template>
    <AppLayout title="">
        <div class="">
            <div class="h-full">
                <div class="overflow-hidden shadow-xl sm:rounded-lg h-screen">
                    <!-- <bryntum-gantt v-bind="ganttconf" /> -->
                    <div id="container" class="overflow-hidden shadow-xl sm:rounded-lg h-full">

                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
