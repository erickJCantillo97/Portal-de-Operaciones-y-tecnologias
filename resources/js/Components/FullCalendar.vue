<script setup>
import { ref, onMounted } from 'vue';
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'
import esLocale from '@fullcalendar/core/locales/es'
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { XMarkIcon } from '@heroicons/vue/20/solid'
import TextInput from '@/Components/TextInput.vue'
import Button from 'primevue/button'
import Combobox from '@/Components/Combobox.vue'
import Moment from "moment"
import CustomModal from './CustomModal.vue';

const props = defineProps({
  initialEvents: Array,
  date: String,
  tasks: Array,
  employee: Object,
  project: String,
})

const isOpen = ref(false)
const scheduleSelected = ref(null)
const idTaskSelected = ref(null)
const selectedEvent = ref(null)
const canDeleteEvent = ref(false)
const getStartDateEvent = ref(null)
const getEndDateEvent = ref(null)
const taskSelected = ref([])
const turnSelect = ref([])
const showHours = ref('Hours')
const currentEvents = ref([])
const eventguid = ref(1)
const calendarOptions = ref({
  plugins: [
    dayGridPlugin,
    timeGridPlugin,
    interactionPlugin // needed for dateClick
  ],
  headerToolbar: {
    left: 'dayGridMonth,timeGridWeek,timeGridDay',
    center: 'title',
    right: ''
    // right: 'dayGridMonth,timeGridWeek,timeGridDay'
  },
  initialView: 'dayGridMonth', //onMounted type calendar view
  initialDate: props.date,
  initialEvents: props.initialEvents, // alternatively, use the `events` setting to fetch from a feed
  allDaySlot: true,
  editable: true,
  selectable: true,
  selectMirror: true,
  dayMaxEvents: true,
  weekends: true,
  select: handleDateSelect,
  eventClick: handleEditEventClick,
  eventsSet: handleEvents,
  locale: esLocale,
  eventOverlap: false,
  nowIndicator: true,
  selectable: true,
  eventConstraint: {
    startTime: '07:00',
    endTime: '16:30',
  },
  // selectConstraint: {
  //   startTime: "07:00",
  //   endTime: "16:30",
  // },
  businessHours: {
    // days of week. an array of zero-based day of week integers (0=Sunday)
    daysOfWeek: [1, 2, 3, 4, 5], // Monday - Friday
    // startTime: '07:00', // a start time (07am in this example)
    // endTime: '16:30', // an end time (4:30pm in this example)
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
    hour12: false
  },
  // you can update a remote database when these fire:
  // eventAdd: this.submit,
  // eventChange: this.submit,
  // eventRemove: this.handleDeleteEventClick,

  // tasks: [],
})

onMounted(() => {
  eventguid.value++
})

function handleWeekendsToggle() {
  calendarOptions.value.weekends = !calendarOptions.value.weekends // update a property
}

function handleDateSelect(selectInfo) {
  console.log(selectInfo)
  isOpen.value = true
  selectedEvent.value = selectInfo
  canDeleteEvent.value = false

  let formatStartDate = Moment(selectInfo.startStr).format('HH:mm')
  getStartDateEvent.value = formatStartDate

  let formatEndDate = Moment(selectInfo.endStr).format('HH:mm')
  getEndDateEvent.value = formatEndDate
}

function handleEditEventClick(clickInfo) {
  //TODO Permitir editar el modal
  isOpen.value = true
  console.log(clickInfo)
  selectedEvent.value = clickInfo
  canDeleteEvent.value = true
}
function handleDeleteEventClick(selectedEvent) {
  console.log(selectedEvent)
  if (selectedEvent) {
    selectedEvent.event.remove(); // Elimina el evento
    isOpen.value = false; // Cierra el modal
  }
}
function handleEvents(events) {
  // console.log(events)
  currentEvents.value = events
}
function handleItem() {
  console.log('Hello World')
}

function submit(idTask, schedule) {
  console.log(schedule)
  console.log(idTask)

  let title = idTaskSelected.value.name
  let calendarApi = schedule.value.view.calendar

  calendarApi.unselect() // clear date selection

  if (title != null) {
    calendarApi.addEvent({
      id: String(eventguid++),
      title,
      start: schedule.startStr,
      end: schedule.endStr,
      color: 'purple'
      // allDay: selectInfo.allDay
    })
  }

  isOpen.value = false
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
function closeDialog() {
  isOpen.value = false
}

</script>

<template>
  <FullCalendar :key="eventguid" :options="calendarOptions">
    <template #eventContent="arg">
      <div class="flex flex-col h-fu border p-2 justify-center">
        <p> {{ arg.timeText }} </p>
        <p> {{ arg.event.title }} </p>
      </div>
    </template>
  </FullCalendar>
  <!--MODAL DE FORMULARIO-->
  <CustomModal v-model:visible="isOpen" :titulo="(currentEvents != 0 ? 'Editar ' : 'Crear') + ' nuevo horario'">

    <template #body>
      <!--COLUMNA 3 - SELECCIÓN DE ACTIVIDADES-->
      <Combobox class="mt-2 text-left" label="Actividad" placeholder="Seleccione Actividad" :options="tasks"
        v-model="idTaskSelected">
      </Combobox>

      <!--RADIO BUTTONS DE HORAS-->
      <div class="flex flex-wrap w-full justify-around text-left align-items-center h-10 mt-4 space-x-2">
        <div>
          <input type="radio" name="action" value="Hours" v-model="showHours">
          <label for="Hours">Intervalo</label>
        </div>
        <div>
          <input type="radio" name="action" value="Resto" v-model="showHours">
          <label for="Resto">Resto</label>
        </div>
        <div>
          <input type="radio" name="action" value="Turno" v-model="showHours">
          <label for="Turno">Turno</label>
        </div>
      </div>

      <!--SELECCIÓN DE HORAS-->
      <div v-if="showHours === 'Hours'" class="w-full h-auto">
        <!--campo hora inicio-->
        <TextInput class="mt-2 text-left" type="time" label="Hora de inicio" v-model="getStartDateEvent">
        </TextInput>

        <!--campo hora finalización-->
        <TextInput class="mt-2 text-left" type="time" label="Hora de Finalización" v-model="getEndDateEvent">
        </TextInput>
      </div>

      <!--SELECCIÓN DE TURNOS-->
      <div v-if="showHours === 'Turno'" class="w-full h-64">
        <!--campo selección de turnos-->
        <Combobox class="mt-2 text-left" label="Turnos" placeholder="Seleccione Turno" :options="tasks"
          v-model="turnSelect">
        </Combobox>
      </div>

      <!--SELECCIÓN RESTO-->
      <div v-if="showHours === 'Resto'" class="flex justify-center w-full h-auto flex-nowrap">
        <span class="info-resto">
          <i>Se asignarán por defecto las horas que no se programaron</i>
        </span>
      </div>
    </template>
    <!--BOTONES GUARDAR Y CANCELAR DEL MODAL-->
    <template #footer>
      <Button v-if="canDeleteEvent" severity="danger" label="Eliminar" @click="handleEditEventClick(selectedEvent)" />

      <Button severity="danger" @click="closeDialog()" label="Cancelar" />

      <Button severity="success" :loading="false" @click="submit(idTaskSelected, selectedEvent)"
        :label="currentEvents != 0 ? 'Actualizar ' : 'Guardar'" />
    </template>

  </CustomModal>
  <TransitionRoot as="template" :show="false">
    <Dialog as="div" class="relative z-30" @close="closeDialog()">
      <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100"
        leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
        <div class="fixed h-screen w-screen inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-30" />
      </TransitionChild>
      <div class="fixed inset-0 z-50 overflow-y-auto h-screen">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
          <TransitionChild as="template" enter="ease-out duration-300"
            enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200"
            leave-from="opacity-100 translate-y-0 sm:scale-100"
            leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            <DialogPanel
              class="relative transform overflow-hidden rounded-lg bg-white px-2 pb-4 pt-2 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg ">
              <div>
                <div class="px-8 mt-2">
                  <div class="flex items-center justify-between text-center">
                    <DialogTitle as="h3" class="text-xl font-semibold text-primary text-center flex-grow">
                      {{ currentEvents != 0 ? 'Editar ' : 'Crear' }}
                      Nuevo Horario
                    </DialogTitle>

                    <!-- BOTÓN 'X'->(cerrar) DEL MODAL -->
                    <div class="w-8 h-8">
                      <button v-tooltip.top="'Cerrar'" @click="closeDialog()">
                        <XMarkIcon
                          class="w-8 h-8 p-0.5 border rounded-md text-white bg-danger border-danger hover:bg-red-600" />
                      </button>
                    </div>
                  </div>

                  <!--COLUMNA 3 - SELECCIÓN DE ACTIVIDADES-->
                  <Combobox class="mt-2 text-left" label="Actividad" placeholder="Seleccione Actividad" :options="tasks"
                    v-model="idTaskSelected">
                  </Combobox>

                  <!--RADIO BUTTONS DE HORAS-->
                  <div class="flex flex-wrap w-full justify-around text-left align-items-center h-10 mt-4 space-x-2">
                    <div>
                      <input type="radio" name="action" value="Hours" v-model="showHours">
                      <label for="Hours">Intervalo</label>
                    </div>
                    <div>
                      <input type="radio" name="action" value="Resto" v-model="showHours">
                      <label for="Resto">Resto</label>
                    </div>
                    <div>
                      <input type="radio" name="action" value="Turno" v-model="showHours">
                      <label for="Turno">Turno</label>
                    </div>
                  </div>

                  <!--SELECCIÓN DE HORAS-->
                  <div v-if="showHours === 'Hours'" class="w-full h-auto">

                    <!--campo hora inicio-->
                    <TextInput class="mt-2 text-left" type="time" label="Hora de inicio" v-model="getStartDateEvent">
                    </TextInput>

                    <!--campo hora finalización-->
                    <TextInput class="mt-2 text-left" type="time" label="Hora de Finalización" v-model="getEndDateEvent">
                    </TextInput>
                  </div>

                  <!--SELECCIÓN DE TURNOS-->
                  <div v-if="showHours === 'Turno'" class="w-full h-64">
                    <!--campo selección de turnos-->
                    <Combobox class="mt-2 text-left" label="Turnos" placeholder="Seleccione Turno" :options="tasks"
                      v-model="turnSelect">
                    </Combobox>
                  </div>

                  <!--SELECCIÓN RESTO-->
                  <div v-if="showHours === 'Resto'" class="flex justify-center w-full h-auto flex-nowrap">
                    <span class="info-resto">
                      <i>Se asignarán por defecto las horas que no se programaron</i>
                    </span>
                  </div>

                  <!--BOTONES GUARDAR Y CANCELAR DEL MODAL-->
                  <div class="flex px-2 mt-2 space-x-4">
                    <Button v-if="canDeleteEvent" class="hover:bg-danger text-danger border-danger" severity="danger"
                      @click="handleEditEventClick(selectedEvent)">
                      Eliminar
                    </Button>

                    <Button class="hover:bg-danger text-danger border-danger" severity="danger" @click="closeDialog()">
                      Cancelar
                    </Button>

                    <Button severity="success" :loading="false" class="text-success hover:bg-success border-success"
                      @click="submit(idTaskSelected, selectedEvent)">
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
<style scoped></style>
