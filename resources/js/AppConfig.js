import { StringHelper } from '@bryntum/gantt';
import { DateHelper } from '@bryntum/gantt';

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
                    url: './datos/launch-saas.json'
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
                width: 250,
                editor: {
                    type: 'combo',
                    items: ['100', '200', '300']
                }
            },
            {
                text: 'Programacion',
                collapsible: true,
                collapseMode: 'toggleAll',
                collapsed: false,
                children: [
                    { type: 'startdate' }, { type: 'duration' }, { type: 'enddate' }, // Column that is shown when the header is collapsed
                    {
                        field: 'startDate',
                        hidden: true,
                        text: 'Fechas',
                        width: 150,
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
            {
                type: 'percentdone',
                text:'Avance',
                showCircle: true,
                width: 80,
                editor: false,
                region: 'right',
            },
            { field: 'code', text: 'Codigo SAP', width: 50 },
        ],

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
    width: '14em',
    showTooltip: false
};

export { useGanttConfig, sliderConfig };
