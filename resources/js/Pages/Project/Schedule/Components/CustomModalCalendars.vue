<script setup>
import CustomModal from '@/Components/CustomModal.vue';
import Calendar from 'primevue/calendar';
import getDays from 'colombia-holidays'
import { ref } from 'vue';
import InputSwitch from 'primevue/inputswitch';
import CustomDataTable from '@/Components/CustomDataTable.vue';
import CustomInput from '@/Components/CustomInput.vue';
import MultiSelect from 'primevue/multiselect';
import TabPanel from 'primevue/tabpanel';
import TabView from 'primevue/tabview';
import OverlayPanel from 'primevue/overlaypanel';
import Dropdown from 'primevue/dropdown';
import { useToast } from "primevue/usetoast";
const toast = useToast();

const props = defineProps({
    project: Object,
    listCalendar: Array,
    class: String
})

const yearsSelect = ref([(new Date).getFullYear()])
const holidays = ref({})
const selecDayWeek = ref({})
const newCalendarData = ref({})
const visible = ref()
const form = ref({
    project: props.project.id,
    name: null,
    statusHolidays: false,
    holidays: [],
    daysWeek: {
        MONDAY: {
            active: true,
            hours: [
                {
                    start: new Date('1900/01/01 07:00'),
                    end: new Date('1900/01/01 12:00')
                },
                {
                    start: new Date('1900/01/01 13:00'),
                    end: new Date('1900/01/01 16:30')
                },
            ]
        },
        TUESDAY: {
            active: true,
            hours: [
                {
                    start: new Date('1900/01/01 07:00'),
                    end: new Date('1900/01/01 12:00')
                },
                {
                    start: new Date('1900/01/01 13:00'),
                    end: new Date('1900/01/01 16:30')
                },
            ]
        },
        WEDNESDAY: {
            active: true,
            hours: [
                {
                    start: new Date('1900/01/01 07:00'),
                    end: new Date('1900/01/01 12:00')
                },
                {
                    start: new Date('1900/01/01 13:00'),
                    end: new Date('1900/01/01 16:30')
                },
            ]
        },
        THURSDAY: {
            active: true,
            hours: [
                {
                    start: new Date('1900/01/01 07:00'),
                    end: new Date('1900/01/01 12:00')
                },
                {
                    start: new Date('1900/01/01 13:00'),
                    end: new Date('1900/01/01 16:30')
                },
            ]
        },
        FRIDAY: {
            active: true,
            hours: [
                {
                    start: new Date('1900/01/01 07:00'),
                    end: new Date('1900/01/01 12:00')
                },
                {
                    start: new Date('1900/01/01 13:00'),
                    end: new Date('1900/01/01 16:30')
                },
            ]
        },
        SATURDAY: {
            active: true,
            hours: [
                {
                    start: new Date('1900/01/01 07:00'),
                    end: new Date('1900/01/01 12:00')
                },
                {
                    start: new Date('1900/01/01 13:00'),
                    end: new Date('1900/01/01 16:30')
                },
            ]
        },
        SUNDAY: {
            active: false,
            hours: [
                {
                    start: new Date('1900/01/01 07:00'),
                    end: new Date('1900/01/01 12:00')
                },
                {
                    start: new Date('1900/01/01 13:00'),
                    end: new Date('1900/01/01 16:30')
                },
            ]
        },
    },
    hours: [
        {
            start: new Date('1900/01/01 07:00'),
            end: new Date('1900/01/01 12:00')
        },
        {
            start: new Date('1900/01/01 13:00'),
            end: new Date('1900/01/01 16:30')
        },
    ]
})

let years = []
for (var index = (new Date).getFullYear(); index < 2040; index++) {
    years.push(index)
}

function formatDate(date) {
    const parts = date.split("-"); // Dividimos la cadena en partes

    if (parts.length !== 3) {
        console.error("Fecha inválida. Debe estar en formato YYYY-MM-DD");
        return null; // Manejo de error
    }

    const year = parseInt(parts[0], 10);
    const month = parseInt(parts[1], 10) - 1; // Restamos 1 al mes (enero es 0)
    const day = parseInt(parts[2], 10);

    const formattedDate = new Date(year, month, day);
    return formattedDate;
}

function activateHolidays(option) {
    if (option) {
        if (yearsSelect.value.length == 0) {
            yearsSelect.value.push(new Date().getFullYear())
        }
    }
    if (yearsSelect.value.length == 0) {
        form.value.statusHolidays = false
    }
    form.value.holidays = form.value.holidays.filter(day => day.type !== 'Festivo');
    yearsSelect.value.sort((a, b) => a - b)
    yearsSelect.value.forEach(year => {
        getholidays(year)
        if (form.value.statusHolidays) {
            holidays.value[year].forEach(holiday => {
                const existingHoliday = form.value.holidays.find(day => day.startDay === holiday.holiday);
                if (!existingHoliday) {
                    form.value.holidays.push({
                        startDay: holiday.holiday,
                        endDay: holiday.holiday,
                        description: holiday.celebration,
                        type: 'Festivo',
                        isWorking: false
                    });
                }
            });
        }
    });
}

function getholidays(year) {
    if (holidays.value[year] == undefined) {
        holidays.value[year] = getDays.getColombiaHolidaysByYear(year)
        holidays.value[year].forEach((holiday) => {
            holiday.holiday = formatDate(holiday.holiday)
        })
    }
}
const listDays = [
    { name: 'Lunes', code: "MONDAY" },
    { name: 'Martes', code: "TUESDAY" },
    { name: 'Miercoles', code: "WEDNESDAY" },
    { name: 'Jueves', code: "THURSDAY" },
    { name: 'Viernes', code: "FRIDAY" },
    { name: 'Sabado', code: "SATURDAY" },
    { name: 'Domingo', code: "SUNDAY" }
];

function compareDates(date) {
    const day = date.year + '-' + (date.month + 1 < 10 ? '0' + (date.month + 1) : (date.month + 1)) + '-' + (date.day < 10 ? '0' + date.day : date.day);


    const existeFestivo = form.value.holidays.find(h => {
        return formatDate(day) >= h.startDay && formatDate(day) <= h.endDay;
    });

    if (existeFestivo) {
        if (existeFestivo.isWorking) {
            return 1
        } else {
            return 2
        }
    }

    if (form.value.daysWeek[listDays[(new Date(day)).getDay()].code].active) {
        if (JSON.stringify(form.value.daysWeek[listDays[(new Date(day)).getDay()].code].hours) === JSON.stringify(form.value.hours)) {
            return 0;
        } else {
            return 1;
        }
    }
    return 3;
}


const references = [
    { class: 'bg-white', text: 'Laborable' },
    { class: 'bg-blue-500 text-white', text: 'Laborable exepcion' },
    { class: 'bg-red-500 text-white', text: 'No laborable' },
    { class: 'bg-yellow-500 text-white', text: 'Descanso' },
]

const columnas = [
    { field: 'description', header: 'Descripcion', filter: 'true' },
    { field: 'startDay', header: 'Dia inicio', type: 'date', filter: 'true' },
    { field: 'endDay', header: 'Dia fin', type: 'date', filter: 'true' },
    { field: 'isWorking', header: 'Laborable', filter: 'true', type: 'boolean' },
]
const actions = [
    { event: 'deleteClic', severity: 'danger', icon: 'fa-solid fa-trash', class: '!h-8', text: true, outlined: false, rounded: false },
]


function removeExeption(event, data) {
    form.value.holidays = form.value.holidays.filter((day) => {
        return day.startDay !== data.startDay;
    })
}

function deepCopy(obj) {
    if (obj === null || typeof obj !== 'object') {
        return obj;
    }

    if (obj instanceof Date) {
        return new Date(obj);
    }

    if (Array.isArray(obj)) {
        return obj.map(deepCopy);
    }

    const copy = {};
    for (const key in obj) {
        if (Object.prototype.hasOwnProperty.call(obj, key)) {
            copy[key] = deepCopy(obj[key]);
        }
    }

    return copy;
}

function applyHoursDefault() {
    listDays.forEach((day) => {
        form.value.daysWeek[day.code].hours = deepCopy(form.value.hours);
    });
}

const overlayAddExeption = ref()
const toggle = (event) => {
    overlayAddExeption.value.toggle(event);
}

const newExeption = ref({
    description: null,
    startDay: null,
    endDay: null,
    startHour: null,
    endHour: null,
    type: 'Personalizado',
    isWorking: false
})
function addExeption() {
    form.value.holidays.push(newExeption.value)
    newExeption.value = {
        description: null,
        startDay: null,
        endDay: null,
        startHour: null,
        endHour: null,
        type: 'Personalizado',
        isWorking: false
    }
    overlayAddExeption.value.hide()
}

const calendar = ref()
const loadSaveCalendar = ref()
const formCalendar = ref({
    project: props.project.id,
    calendar: props.project.calendar,
    newCalendar: false
})
const toggleCalendar = (event) => {
    calendar.value.toggle(event);
}
const newCalendar = () => {
    visible.value = true
    calendar.value.hide();
}
const gantt = defineModel('gantt', {
    type: Object,
})
async function setCalendarToProject() {
    if(formCalendar.value.calendar != null){
    loadSaveCalendar.value = true
    await axios.post(route('create.calendar',formCalendar.value.project), formCalendar.value).then(async (res) => {
        if (res.data.status) {
            toast.add({
                severity: 'success',
                group: 'customToast',
                text: res.data.mensaje,
                life: 4000
            })
           await gantt.value.project.load();
        } else {
            toast.add({
                severity: 'error',
                group: 'customToast',
                text: res.data.mensaje,
                life: 4000
            })
        }
    })
    loadSaveCalendar.value = false
}
}


function convertirHorasARecurrent(objeto) {
    const dias = Object.keys(objeto);
    const resultado = [];
    for (const dia of dias) {
        const horas = objeto[dia].hours;
        for (const hora of horas) {
            const startHour = new Date(hora.start).toLocaleTimeString('es-CO', { hour12: false, hour: '2-digit', minute: '2-digit' });
            const endHour = new Date(hora.end).toLocaleTimeString('es-CO', { hour12: false, hour: '2-digit', minute: '2-digit' });
            resultado.push({
                isWorking: true,
                day: dia,
                startHour,
                endHour
            });
        }
    }

    return resultado;
}

async function save() {
    let aux = deepCopy(form.value)
    aux.holidays.forEach((item) => {
        item.startDay = new Date(item.startDay).toLocaleDateString('es-CO', { year: 'numeric', month: 'numeric', day: 'numeric' });
        item.endDay = new Date(item.endDay).toLocaleDateString('es-CO', { year: 'numeric', month: 'numeric', day: 'numeric' });
    });
    let data = {
        newCalendar: true,
        name: aux.name,
        exeptions: aux.holidays,
        recurrent: convertirHorasARecurrent(aux.daysWeek)
    }
    console.log(data)
    await axios.post(route('create.calendar', props.project.id), data)
        .then(async () => {
            if (res.data.status) {
                toast.add({
                    severity: 'success',
                    group: 'customToast',
                    text: res.data.mensaje,
                    life: 4000
                })
                await gantt.value.project.setCalendar(formCalendar.value.calendar);
            } else {
                toast.add({
                    severity: 'error',
                    group: 'customToast',
                    text: res.data.mensaje,
                    life: 4000
                })
            }
        })
        .catch((error) => {
            console.log(error)
            toast.add({
                severity: 'error',
                group: 'customToast',
                text: 'Error no controlado',
                life: 4000
            })
        })
}


</script>

<template>
    <Button :class raised v-tooltip.bottom="'Agregar Calendario'" icon="fa-solid fa-calendar-plus"
        @click="toggleCalendar" />
    <CustomModal v-model:visible="visible" :closeOnEscape="false" icon="fa-solid fa-file-export"
        :titulo="'Nuevo calendario para el projecto: ' + project.name" width="80vw">
        <template #body>
            <div class="flex gap-2 h-[75vh]">
                <div class="space-y-1 w-min flex flex-col items-center">
                    <div class="w-full px-3">
                        <CustomInput v-model:input="form.name" class="w-full"
                            placeholder="Escriba un nombre para el calendario" label="Nombre del calendario">
                        </CustomInput>
                    </div>
                    <Calendar v-model="newCalendarData.days" inline>
                        <template #date="slotProps">
                            <div class="w-full h-full flex items-center justify-center"
                                :class="references[compareDates(slotProps.date)].class">
                                <p>{{ slotProps.date.day }}</p>
                            </div>
                        </template>
                    </Calendar>
                    <div class="grid grid-cols-2 pr-5 gap-1">
                        <span v-for="reference in references" class="flex gap-1 w-full cursor-default">
                            <div :class="reference.class" class=" w-full p-1 border rounded-md h-6 flex items-center">
                                <p v-tooltip="reference.text" class="text-center w-full truncate">{{ reference.text }}
                                </p>
                            </div>
                        </span>
                    </div>
                </div>
                <div class="px-2 h-full w-full max-w-full">
                    <TabView :pt="{ root: 'h-full overflow-hidden', navContainer: '!h-12', panelContainer: '!h-full' }">
                        <TabPanel header="Exepciones" :pt="{ root: 'h-full' }">
                            <div class="grid grid-cols-2 p-2 h-">
                                <div class="flex space-x-3 items-center">
                                    <label class="mb-0.5" for="holidays">¿Agregar dias festivos?</label>
                                    <InputSwitch id="holidays" @change="activateHolidays(true)"
                                        v-model="form.statusHolidays" />
                                </div>
                                <div class="flex w-full space-x-3 items-center">
                                    <label class="mb-0.5 w-min" for="holidays">Año</label>
                                    <MultiSelect :disabled="!form.statusHolidays" class="w-min max-w-full"
                                        v-model="yearsSelect" @change="activateHolidays(false)" :options="years">
                                    </Multiselect>
                                </div>
                            </div>
                            <div class="h-[85%] border rounded-md">
                                <CustomDataTable showItem title="Exepciones" :actions :showAdd="true"
                                    :data="form.holidays" @addClick="toggle" :columnas :showHeader="true"
                                    :paginator="false" @deleteClic="removeExeption">
                                </CustomDataTable>
                            </div>
                        </TabPanel>
                        <TabPanel header="Semana laboral">
                            <div class="flex justify-between p-1 rounded-md">
                                <div class="w-5/6">
                                    <p class="font-bold text-lg">Horario normal</p>
                                    <ul class="flex gap-1 overflow-x-auto">
                                        <li v-for="hour, index in form.hours" class="border rounded-md p-2">
                                            <div class="flex items-center justify-between">
                                                <p class="font-bold text-center">Intervalo {{ index + 1 }}</p>
                                                <Button v-tooltip="index == 0 ? 'Añadir intervalo' : 'Quitar intervalo'"
                                                    text
                                                    :icon="index == 0 ? 'fa-solid fa-plus' : 'fa-solid fa-trash-can'"
                                                    :severity="index == 0 ? 'success' : 'danger'"
                                                    @click="index == 0 ? form.hours.push(hour) : form.hours.splice(index, 1)" />
                                            </div>
                                            <span class="flex gap-2 w-56">
                                                <CustomInput label="Hora inicio" v-model:input="hour.start"
                                                    type="time" />
                                                <CustomInput label="Hora fin" v-model:input="hour.end" type="time" />
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="flex w-full justify-end items-end">
                                    <Button v-tooltip.top="'Aplicar a todos los dias activos'" severity="success"
                                        label="Aplicar" @click="applyHoursDefault"></Button>
                                </div>
                            </div>
                            <div class="flex">
                                <div class="flex flex-col gap-2 p-1">
                                    <p class="font-bold">Dias activos</p>
                                    <ul class="border flex flex-col gap-1 w-min p-3 rounded-md">
                                        <li v-for="day in listDays" @click="selecDayWeek = day"
                                            :class="selecDayWeek.code == day.code ? 'border-success bg-success-light' : undefined"
                                            class="border flex justify-between gap-3 items-center hover:bg-primary-light p-2 cursor-pointer rounded-md">
                                            <p class="mb-0 cursor-pointer">
                                                {{ day.name }}
                                            </p>
                                            <InputSwitch v-model="form.daysWeek[day.code].active"
                                                :inputId="day.code + day.name" :value="true" />
                                        </li>
                                    </ul>
                                </div>
                                <div class="p-7 flex flex-col h-full w-full overflow-hidden max-w-full"
                                    v-if="selecDayWeek.code">
                                    <p class="text-lg font-bold text-center mb-2">
                                        Personalizar horario de los dias {{ selecDayWeek.name }}
                                    </p>
                                    <ul class="flex gap-1 overflow-x-auto">
                                        <li v-for="hour, index in form.daysWeek[selecDayWeek.code].hours"
                                            class="border rounded-md p-2">
                                            <div class="flex items-center justify-between">
                                                <p class="font-bold text-center">Intervalo {{ index + 1 }}</p>
                                                <Button v-tooltip="index == 0 ? 'Añadir intervalo' : 'Quitar intervalo'"
                                                    text
                                                    :icon="index == 0 ? 'fa-solid fa-plus' : 'fa-solid fa-trash-can'"
                                                    :severity="index == 0 ? 'success' : 'danger'"
                                                    @click="index == 0 ? form.daysWeek[selecDayWeek.code].hours.push(hour) : form.daysWeek[selecDayWeek.code].hours.splice(index, 1)" />
                                            </div>
                                            <span class="flex gap-2 w-56">
                                                <CustomInput label="Hora inicio" v-model:input="hour.start"
                                                    type="time" />
                                                <CustomInput label="Hora fin" v-model:input="hour.end" type="time" />
                                            </span>
                                        </li>
                                    </ul>
                                    <!-- {{ form.daysWeek[selecDayWeek] ?? null }} -->
                                </div>
                            </div>
                        </TabPanel>
                    </TabView>
                </div>
            </div>
        </template>
        <template #footer>
            <Button label="Cancelar" severity="danger" icon="fa-regular fa-circle-xmark" @click="visible = false" />
            <Button label="Guardar" severity="success" icon="fa-solid fa-download" @click="save()" />
        </template>
    </CustomModal>
    <OverlayPanel ref="overlayAddExeption">
        <div class="flex flex-col w-96">
            <CustomInput v-model:input="newExeption.description"
                placeholder="Descripcion del dia a agregar a exepciones" label="Descripcion" />
            <span class="grid grid-cols-2 gap-3">
                <CustomInput v-model:input="newExeption.startDay" type="date" label="Dia inicio"
                    @value-change="newExeption.endDay = newExeption.startDay" />
                <CustomInput v-model:input="newExeption.endDay" :minDate="newExeption.startDay" type="date"
                    label="Dia fin" />
            </span>
            <span class="space-y-2 mt-2">
                <CustomInput label="Laborable" v-model:input="newExeption.isWorking" type="boolean" />
                <span class="flex gap-2">
                    <CustomInput label="Hora inicio" v-model:input="newExeption.startHour" type="time" />
                    <CustomInput label="Hora fin" v-model:input="newExeption.endHour" type="time" />
                </span>
                <span class="flex items-end justify-end">
                    <Button severity="success" label="Agregar" icon="fa-solid fa-plus" @click="addExeption" />
                </span>
            </span>
        </div>
    </OverlayPanel>
    <OverlayPanel ref="calendar" class="w-96">
        <div class="flex flex-col gap-2 mb-2">
            <div class="flex justify-between items-center gap-2">
                <label class="">Asignación de calendario</label>
                <Button severity="success" v-tooltip.rigth="'Nuevo Calendario'" icon="fa-solid fa-plus"
                    @click="newCalendar" />
            </div>
            <Dropdown v-model="formCalendar.calendar" :options="listCalendar" optionLabel="name"
                placeholder="Seleccione un calendario" checkmark :highlightOnSelect="false" class="w-full" showClear />
        </div>

        <span class="flex justify-end">
            <Button severity="success" label="Guardar" :loading="loadSaveCalendar" @click="setCalendarToProject()" />
        </span>
    </OverlayPanel>
</template>
