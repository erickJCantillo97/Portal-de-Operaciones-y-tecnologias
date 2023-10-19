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
                customBoldStyle: {
                    fontWeight: 'bold',
                    fontSize: '50px',
                },
                customItalicStyle: {
                    fontStyle: 'italic',
                    fontSize: '50px',
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
    }
})
</script>

<template>
    <div class='demo-app'>
        <!-- <div class='demo-app-sidebar'>
            <div class='demo-app-sidebar-section'>
                <h2>Instructions</h2>
                <ul>
                    <li>Select dates and you will be prompted to create a new event</li>
                    <li>Drag, drop, and resize events</li>
                    <li>Click an event to delete it</li>
                </ul>
            </div>
            <div class='demo-app-sidebar-section'>
                <label>
                    <input type='checkbox' :checked='calendarOptions.weekends' @change='handleWeekendsToggle' />
                    toggle weekends
                </label>
            </div>
            <div class='demo-app-sidebar-section'>
                <h2>All Events ({{ currentEvents.length }})</h2>
                <ul>
                    <li v-for='event in currentEvents' :key='event.id'>
                        <b>{{ event.startStr }}</b>
                        <i>{{ event.title }}</i>
                    </li>
                </ul>
            </div>
        </div> -->
        <div class='max-w-full w-full h-[90%]'>
            <FullCalendar class='w-full h-96' :options='calendarOptions' :style="customBoldClass">
                <template v - slot: eventContent=' arg'>
                    <h2> {{ arg.timeText }} </h2>
                    <h1> {{ arg.event.title }} </h1>
                </template>
            </FullCalendar>
        </div>
    </div>
</template>

<style scoped lang='css'>
h2 {
    margin: 0;
    font-size: 16px;
}

ul {
    margin: 0;
    padding: 0 0 0 1.5em;
}

li {
    margin: 1.5em 0;
    padding: 0;
}

.demo-app {
    display: flex;
    width: 100%;
    min-height: 100%;
    font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
    font-size: .7rem;
}

.customBoldClass {
    font-weight: bold;
    font-size: 12px;
    /* Cambia el tamaño de fuente de <b> */
}

.customItalicClass {
    font-style: italic;
    /* Cambia el estilo de fuente de <i> */
    font-size: 14px;
    /* Cambia el tamaño de fuente de <i> */
}

/* .custom-event-class {
    background: linear-gradient(to right, #FF5733, #FFA500);
    color: white;
    border: 2px solid #FF5733;
} */

/*
.demo-app-sidebar {
    width: 900px;
    line-height: 1.5;
    background: #eaf9ff;
    border-right: 1px solid #d3e2e8;
}

.demo-app-sidebar-section {
    padding: 2em;
}

.demo-app-main {
    flex-grow: 1;
    padding: 3em;
}

.fc {
    // the calendar root
    max-width: 1100px;
    margin: 0 auto;
    font-size: 10px;
} */
</style>
