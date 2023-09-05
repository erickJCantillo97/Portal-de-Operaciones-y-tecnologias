import { StringHelper } from '@bryntum/gantt';
import { DateHelper } from '@bryntum/gantt';
import '@/GanttToolbar.js'
/**
 * Application configuration
 */
const useGanttConfig = function () {
    return {
        project: {
            autoSync : true,
            autoLoad: true,
            transport: {
                load: {
                    // url: route('dataGantt')
                    url: 'data/launch-saas.json'
                },
                sync: {
                    url : route('syncGantt'),
                    headers     : {
                        'Content-Type' : 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    credentials : 'include'
                }
            }
        },
        features : {
            mspExport : true // enable the feature Microsoft Project export
        },
        // agrega opcion a menu contextual
        // features : {
        //     taskMenu : {
        //         // Extra items for all events
        //         items : {
        //             flagTask : {
        //                 text : 'Extra',
        //                 icon : 'b-fa b-fa-fw b-fa-flag',
        //                 onItem({taskRecord}) {
        //                     taskRecord.flagged = true;
        //                 }
        //             }
        //         }
        //     }
        // },

        dependencyIdField: 'sequenceNumber',

        columns: [
            {
                type: 'name',
                width: 500,
            },
            {
                type: 'percentdone',
                text:'Avance',
                showCircle: true,
                width: 80,
            },
            {
                text: 'Programacion',
                collapsible: true,
                collapseMode: 'toggleAll',
                collapsed: false,
                children: [
                    { type: 'duration' },{ type: 'startdate' }, { type: 'enddate' }, // Column that is shown when the header is collapsed
                    {
                        field: 'startDate',
                        hidden: true,
                        text: 'Fechas',
                        width: 30,
                        editor: false,
                        htmlEncode: false,
                        renderer({ record }) {
                            return `
                                <div class="calendar">
                                    <div class="date">${DateHelper.format(record.startDate, 'D')}</div>
                                    <div class="month">${DateHelper.format(record.startDate, 'MMM')}</div>
                                </div>
                                ${record.duration ?? 0} ${DateHelper.getLocalizedNameOfUnit(record.durationUnit, record.duration !== 1)}
                            `;
                        }
                    }
                ]
            },

            { field: 'code', text: 'Codigo SAP', autoWidth: true, hidden: true},
        ],

        keyMap : {
            // This is a function from the existing Gantt API
            'Ctrl+Shift+Q': 'addSubTask',
            'Ctrl+i' : 'indent',
            'Ctrl+o' : 'outdent',

        },
        tbar : {
            type : 'gantttoolbar',
            height : '4em'
        },

        // Custom task content, display task name on child tasks
        taskRenderer({ taskRecord }) {
            if (taskRecord.isLeaf && !taskRecord.isMilestone) {
                return StringHelper.encodeHtml(taskRecord.name);
            }
        }
    };
};

const sliderConfig = {
    text: 'Set Bar Margin',
    min: 0,
    max: 15,
    width: '50em',
    showTooltip: false
};

export { useGanttConfig, sliderConfig };
