
const useGanttConfig = function () {
    return {
        rowHeight: 28,
        dependencyIdField: 'sequenceNumber',
        project: {
            autoSync: true,
            autoLoad: true,
            transport: {
                load: {
                    // url: route('dataGantt', props.project)
                },
                sync: {
                    // url: route('syncGantt', props.project),
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
    };
};

export { useGanttConfig };