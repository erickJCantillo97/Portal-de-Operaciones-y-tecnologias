<script >
import { defineComponent, ref, onMounted } from 'vue'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'
import esLocale from '@fullcalendar/core/locales/es'
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import TextInput from '@/Components/TextInput.vue'
import Button from '@/Components/Button.vue'
import Combobox from '@/Components/Combobox.vue'
import { Calendar } from '@fullcalendar/core';
import { INITIAL_EVENTS, createEventId } from '../event-utils'

export default defineComponent({
  components: {
    FullCalendar,
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogTitle,
    TextInput,
    Button,
    Combobox,
  },
  props: {
    initialEvents: Array,
    date: Date,
    tasks: Array,
    employee: Array,
    project: String,
  },
  // mounted() {

  // },
  // setup() {
  //   const isOpen = ref(false);
  //   const tasks = ref();
  //   const taskSelect = ref();
  //   const showHours = ref('Hours');
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
        eventClick: this.handleEditEventClick,
        eventsSet: this.handleEvents,
        locale: esLocale,
        eventOverlap: false,
        nowIndicator: true,
        selectable: true,
        eventConstraint: {
          startTime: '07:00',
          endTime: '16:30',
        },
        selectConstraint: {
          startTime: "07:00",
          endTime: "16:30",
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
        // you can update a remote database when these fire:
        // eventAdd: this.submit,
        // eventChange: this.submit,
        // eventRemove: this.handleDeleteEventClick,

        tasks: [],
      },
      isOpen: false,
      scheduleSelected: null,
      taskIDSelected: null,
      selectedEvent: null,
      canDeleteEvent: false,
      taskSelected: [],
      turnSelect: [],
      showHours: 'Hours',
      currentEvents: [],
    }
  },
  methods: {
    handleWeekendsToggle() {
      this.calendarOptions.weekends = !this.calendarOptions.weekends // update a property
    },
    handleDateSelect(selectInfo) {
      this.isOpen = true
      console.log(selectInfo)
      this.selectedEvent = selectInfo
      this.canDeleteEvent = false
    },
    handleEditEventClick(clickInfo) {
      //TODO Permitir editar el modal
      this.isOpen = true
      console.log(clickInfo)
      this.selectedEvent = clickInfo
      this.canDeleteEvent = true
    },
    handleDeleteEventClick(selectedEvent) {
      console.log(selectedEvent)
      if (this.selectedEvent) {
        this.selectedEvent.event.remove(); // Elimina el evento
        this.isOpen = false; // Cierra el modal
      }
    },
    handleEvents(events) {
      // console.log(events)
      this.currentEvents = events
    },
    submit(taskID, schedule) {
      console.log(schedule)
      console.log(taskID);

      let title = this.taskIDSelected.name
      let calendarApi = schedule.view.calendar

      calendarApi.unselect() // clear date selection

      if (title != null) {
        calendarApi.addEvent({
          id: createEventId(),
          title,
          start: schedule.startStr,
          end: schedule.endStr,
          color: 'purple'
          // allDay: selectInfo.allDay
        })
      } else {
        calendarApi.remove()
        alert("Por favor seleccione una tarea")
      }

      this.isOpen = false
      // if (clickInfo == null) {
      //   clickInfo.event.remove()
      //   this.isOpen = false
      // } else {
      //   axios.put('/api/eventos', newEvent)
      //     .then((response) => {
      //       console.log('Evento creado exitosamente');
      //     })
      //     .catch((error) => {
      //       console.error('Error al crear el evento', error);
      //     });
      // }
    }
  }
})
</script>

<template>
  <div class='flex w-full min-h-[100%] font-sans text-sm rounded-md font-bold shadow-md border border-solid p-2'>
    <div class='max-w-full w-full h-[60%]'>
      <FullCalendar class='w-full h-96 custom-scroll' :options='calendarOptions'>
        <template v - slot: eventContent=' arg'>
          <!--Los estilos de estos elementos se encuentran en 'resources/css/custom/fullcalendar.css'-->
          <b> {{ arg.timeText }} </b>
          <i> {{ arg.event.title }} </i>
        </template>
      </FullCalendar>
    </div>
  </div>

  <!--MODAL DE FORMULARIO-->
  <TransitionRoot as="template" :show="isOpen">
    <Dialog as="div" class="relative z-30" @close="isOpen = false">
      <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100"
        leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
        <div class="fixed h-screen w-screen inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-30" />
      </TransitionChild>
      <div class="fixed inset-0 z-50 overflow-y-auto h-screen">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0" >
          <TransitionChild as="template" enter="ease-out duration-300"
            enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200"
            leave-from="opacity-100 translate-y-0 sm:scale-100"
            leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            <DialogPanel
              class="relative transform overflow-hidden rounded-lg bg-white px-2 pb-4 pt-2 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg ">
              <div>
                <div class="px-8 mt-2 text-center">
                  <DialogTitle as="h3" class="text-xl font-semibold text-primary text-center">
                    {{ taskSelected.id != 0 ? 'Editar ' : 'Crear' }}
                    Nuevo Horario
                  </DialogTitle>
                  <!--COLUMNA 3 - SELECCIÓN DE ACTIVIDADES-->

                  <Combobox class="mt-2 text-left" label="Actividad" placeholder="Seleccione Actividad" :options="tasks"
                  v-model="taskIDSelected" >
                  </Combobox>

                  <!--RADIO BUTTONS DE HORAS-->
                  <div class="flex flex-wrap w-full justify-between h-10 mt-4 space-x-2">
                    <input type="radio" name="action" value="Horas" v-model="showHours">
                    <label for="Horas">Intervalo</label>
                    <input type="radio" name="action" value="Resto" v-model="showHours">
                    <label for="Resto">Resto</label>
                    <input type="radio" name="action" value="Turno" v-model="showHours">
                    <label for="Turno">Turno</label>
                  </div>

                  <!--sección de selección de horas-->
                  <div v-if="showHours === 'Horas'" class="w-full h-auto">
                    <!--CAMPO HORA INICIO-->
                    <TextInput class="mt-2 text-left" type="time" label="Hora de inicio">
                    </TextInput>

                    <!--CAMPO HORA FINALIZACIÓN-->
                    <TextInput class="mt-2 text-left" type="time" label="Hora de Finalización">
                    </TextInput>
                  </div>

                  <!--sección de selección de turnos-->
                  <div v-if="showHours === 'Turno'" class="w-full h-auto">
                    <!--campo select de turnos-->
                    <Combobox class="mt-2 text-left" label="Turnos" placeholder="Seleccione Turno" :options="tasks"
                      v-model="turnSelect">
                    </Combobox>
                  </div>

                  <!--sección de Resto-->
                  <div v-if="showHours === 'Resto'" class="flex justify-center w-full h-auto flex-nowrap">
                    <p class="info-resto">
                      <i>Se asignarán por defecto las horas que no se programaron</i>
                    </p>
                  </div>


                  <!--BOTONES GUARDAR Y CANCELAR DEL MODAL-->
                  <div class="flex px-2 mt-2 space-x-4">
                    <Button v-if="canDeleteEvent" class="hover:bg-danger text-danger border-danger" severity="danger"
                      @click="handleEditEventClick(selectedEvent)">
                      Eliminar
                    </Button>
                    <Button class="hover:bg-danger text-danger border-danger" severity="danger"
                      @click="isOpen = false">
                      Cancelar
                    </Button>
                    <Button severity="success" :loading="false" class="text-success hover:bg-success border-success"
                      @click="submit(taskIDSelected, selectedEvent)">
                      {{ currentEvents != 0 ? 'Actualizar ' : 'Guardar' }}
                    </Button>
                  </div>
                </div>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>
