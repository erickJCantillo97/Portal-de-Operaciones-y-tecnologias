<script>
import { defineComponent } from 'vue'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'
import { Calendar } from '@fullcalendar/core';
import esLocale from '@fullcalendar/core/locales/es';
import { INITIAL_EVENTS, createEventId } from '../event-utils'

// let calendar = new Calendar(calendarEl, {
//     locales: [esLocale],
//     locale: 'es'
// });

// calendar.setOption('locale', 'es');

export default defineComponent({
    components: {
        FullCalendar,
    },
    data() {
        return {
            calendarOptions: {
                plugins: [
                    dayGridPlugin,
                    timeGridPlugin,
                    interactionPlugin // needed for dateClick
                ],
                headerToolbar: {
                    left: '',
                    center: 'title',
                    right: ''
                    // right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                initialView: 'timeGridDay', //onMounted type calendar view
                initialEvents: INITIAL_EVENTS, // alternatively, use the `events` setting to fetch from a feed
                editable: true,
                selectable: true,
                selectMirror: true,
                dayMaxEvents: true,
                weekends: true,
                select: this.handleDateSelect,
                eventClick: this.handleEventClick,
                eventsSet: this.handleEvents
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
            <FullCalendar class='w-full h-96' :options='calendarOptions'>
                <template v - slot: eventContent=' arg'>
                    <b>{{ arg.timeText }}</b>
                    <i>{{ arg.event.title }}</i>
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

b {
    /* used for event dates/times */
    margin-right: 3px;
}

.demo-app {
    display: flex;
    width: 100%;
    min-height: 100%;
    font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
    font-size: 12px;
}

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
    /* the calendar root */
    max-width: 1100px;
    margin: 0 auto;
    font-size: 10px;
}
</style>
