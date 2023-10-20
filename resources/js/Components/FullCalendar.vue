<script >
import { defineComponent } from 'vue'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'
import esLocale from '@fullcalendar/core/locales/es';
import { Calendar } from '@fullcalendar/core';
import { INITIAL_EVENTS, createEventId } from '../event-utils'

export default defineComponent({
    components: {
        FullCalendar,
    },
    props: {
        initialEvents: Array,
        date: Date,
        project: String
    },
    // mounted() {

    // },

    data() {
        return {
            calendarOptions: {
                plugins: [
                    dayGridPlugin,
                    timeGridPlugin,
                    interactionPlugin // needed for dateClick
                ],
                headerToolbar: {
                    left: 'today',
                    center: 'title',
                    right: ''
                    // right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                initialView: 'timeGridDay', //onMounted type calendar view
                initialDate: this.date,
                initialEvents: this.initialEvents, // alternatively, use the `events` setting to fetch from a feed
                allDaySlot: false,
                editable: true,
                selectable: true,
                selectMirror: true,
                dayMaxEvents: true,
                weekends: true,
                select: this.handleDateSelect,
                eventClick: this.handleEventClick,
                eventsSet: this.handleEvents,
                locale: esLocale,
                eventOverlap: false,
                nowIndicator: true,
                selectable: true,
                selectConstraint: {
                    start: "07:00",
                    end: "14:30",
                },
                businessHours: {
                    // days of week. an array of zero-based day of week integers (0=Sunday)
                    daysOfWeek: [1, 2, 3, 4, 5], // Monday - Friday
                    startTime: '07:00', // a start time (07am in this example)
                    endTime: '16:30', // an end time (4:30pm in this example)
                },
                // eventColor: '#378006',
                // eventClassNames: 'custom-event-class',
                eventTimeFormat: { // like '14:30:00'
                    hour: '2-digit',
                    minute: '2-digit',
                    meridiem: 'short',
                    hour12: true
                },
                slotLabelFormat: {
                    hour: 'numeric',
                    minute: '2-digit',
                    meridiem: 'short',
                    hour12: true
                },
                /* you can update a remote database when these fire:
                eventAdd:
                eventChange:
                eventRemove:
                */
            },
            currentEvents: [],
        }
    },
    methods: {
        handleWeekendsToggle() {
            this.calendarOptions.weekends = !this.calendarOptions.weekends // update a property
        },
        handleDateSelect(selectInfo) {
            let title = prompt('Por favor ingresa el título del evento:')
            let calendarApi = selectInfo.view.calendar

            calendarApi.unselect() // clear date selection

            if (title) {
                calendarApi.addEvent({
                    id: createEventId(),
                    title,
                    start: selectInfo.startStr,
                    end: selectInfo.endStr,
                    color: 'purple'
                    // allDay: selectInfo.allDay
                })
            }
        },
        handleEventClick(clickInfo) {
            if (confirm(`¿Estás seguro de eliminar este evento '${clickInfo.event.title}'?`)) {
                clickInfo.event.remove()
            }
        },
        handleEvents(events) {
            // console.log(events)
            this.currentEvents = events
        },
        handleDateClick(selectInfo) {
            // Solo se permitirá la creación de eventos dentro del rango "07:00" a "14:30"
            if (
                selectInfo.start >= selectInfo.view.activeStart &&
                selectInfo.start <= selectInfo.view.activeEnd
            ) {
                // Crea el evento dentro del rango permitido
                // Puedes usar selectInfo.start y selectInfo.end para obtener las fechas seleccionadas
            } else {
                console.log("No puedes crear eventos fuera del horario restringido.");
            }
        },
    }
})
</script>

<template>
    <div class='flex w-full min-h-[100%] font-sans text-sm rounded-md font-bold shadow-md border border-solid p-2'>
        <div class='max-w-full w-full h-[60%]'>
            <FullCalendar class='w-full h-96 custom-scroll' :options='calendarOptions' @dateClick="handleDateClick()">
                <template v - slot: eventContent=' arg'>
                    <!--Los estilos de estos elementos se encuentran en 'resources/css/custom/fullcalendar.css'-->
                    <b> {{ arg.timeText }} </b>
                    <i> {{ arg.event.title }} </i>
                </template>
            </FullCalendar>
        </div>
    </div>
</template>
