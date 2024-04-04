<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, onMounted } from 'vue';
import { Container, Draggable } from "vue-dndrop";
import { usePermissions } from '@/composable/permission';
import Knob from 'primevue/knob';
import FullCalendar from '@/Components/FullCalendar.vue'
import Loading from '@/Components/Loading.vue';
import CustomInput from '@/Components/CustomInput.vue';
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';
import Listbox from 'primevue/listbox';
import CustomModal from '@/Components/CustomModal.vue';
import RadioButton from 'primevue/radiobutton';
import Calendar from 'primevue/calendar';
import CustomShiftSelector from '@/Components/CustomShiftSelector.vue';
import ButtonGroup from 'primevue/buttongroup';
import Breadcrumb from 'primevue/breadcrumb';
import { useToast } from "primevue/usetoast";
import { useConfirm } from "primevue/useconfirm";
import InputSwitch from 'primevue/inputswitch';
import axios from 'axios';
const toast = useToast();
const confirm = useConfirm();
// const { hasRole, hasPermission } = usePermissions()

const openConflict = ref(false)

//#region Draggable
const listaDatos = ref({})
const date = ref(new Date())
const personal = ref()
const tasks = ref([]);
const loadingProgram = ref(true);
const loadingPerson = ref(true);
const personalHours = ref({});
const loadingTasks = ref(true)
const loadingTask = ref({})
const optionValue = ref('today')
const conflicts = ref()
const task = ref({})

// El código anterior define una función `onDrop` en Vue. Esta función se activa cuando un elemento se
// coloca en una colección.
const onDrop = async (collection, dropResult) => {
    const { addedIndex, payload } = dropResult;
    if (addedIndex !== null) {
        loadingTask.value[collection.id] = true
        await axios.post(route('programming.store'), { task_id: collection.id, employee_id: payload.Num_SAP, name: payload.Nombres_Apellidos, fecha: date.value.toISOString().split("T")[0] }).then((res) => {
            listaDatos.value[collection.id] = res.data.task
            personalHours.value[(payload.Num_SAP)] = res.data.hours
            loadingTask.value[collection.id] = false
            conflicts.value = Object.values(res.data.conflict)
            if (conflicts.value.length > 0) {
                openConflict.value = true;
                task.value = collection
            }
        })
    }
}

const getAssignmentHoursForEmployee = (employee_id) => {
    axios.get(route('get.assignment.hours', [date.value.toISOString().split("T")[0], employee_id])).then((res) => {
        personalHours.value[(employee_id)] = res.data;
    })
}

const getAssignmentHoursAll = () => {
    personalHours.value = {}
    personal.value.forEach(
        element => {
            axios.get(route('get.assignment.hours', [date.value.toISOString().split("T")[0], (element.Num_SAP)])).then((res) => {
                personalHours.value[element.Num_SAP] = res.data;
            });
        })
}

const getPersonalData = () => {
    if (personal.value) {
        getAssignmentHoursAll()
    } else {
        loadingPerson.value = true
        axios.get(route('get.personal.user')).then((res) => {
            // console.log(res)
            personal.value = Object.values(res.data.personal)
            getAssignmentHoursAll()
            loadingPerson.value = false
        })
    }
}

// El código anterior define una función llamada "getChildPayload" que toma un parámetro "índice". Esta
// función devuelve el valor en el índice especificado en la variable "personal".
const getChildPayload = (index) => {
    return personal.value[index];
}

//#endregion

// El código anterior utiliza el gancho de ciclo de vida `onMounted` de Vue para ejecutar algún código
// cuando el componente está montado.
onMounted(() => {
    getPersonalData()
    getTask('tomorrow')
})

// El código anterior es una función de Vue.js que recupera tareas según la opción seleccionada.
const getTask = async (option) => {
    loadingProgram.value = true
    optionValue.value = option
    switch (option) {
        case 'today':
            date.value = new Date();
            break;
        case 'tomorrow':
            var tomorrow = new Date()
            date.value = new Date(tomorrow.setDate(tomorrow.getDate() + 1));
            break;
        default:
            break;
    }
    tasks.value = [];
    await axios.get(route('actividadesDeultimonivel', { date: date.value.toISOString().split("T")[0] })).then((res) => {
        tasks.value = res.data
        loadingProgram.value = false;
    })
    tasks.value.forEach(element => {
        loadingTask.value[element.id] = true
        axios.get(route('get.schedule.task', { task_id: element.id, date: date.value.toISOString().split("T")[0] })).then((res) => {
            listaDatos.value[element.id] = res.data.schedule
            loadingTask.value[element.id] = false
            loadingTasks.value = false
        })
    });
}

// El código anterior es una función llamada `format24h` que toma un parámetro `hora` (que representa
// una hora en formato de 24 horas) y devuelve una cadena de hora formateada en formato de 12 horas con
// AM/PM.
function format24h(hora) {
    if (hora.length > 5) {
        return new Date(hora).toLocaleString('es-CO',
            { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' })
    } else {
        return new Date("1970-01-01T" + hora).toLocaleString('es-CO',
            { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' })
    }
}
//#region

//#endregion

//#region Modal Persona
const employee = ref([])
const open = ref(false)
const events = ref([])
const rendersCalendars = ref(0)

// El código anterior define una función llamada `employeeDialog` que toma un parámetro `item`. Dentro
// de la función, establece el valor de "open" en "verdadero" y el valor de "employee" en el
// parámetro "elemento".
const employeeDialog = (item) => {
    open.value = true
    employee.value = item
    axios.get(route('get.times.employees', { date: date.value.toISOString().split("T")[0], employee_id: item.Num_SAP }))
        .then((res) => {
            events.value = res.data.times
            rendersCalendars.value++;
        })
        .catch(error => {
            console.log(error);
        })
}



const formEditHour = ref({
    userName: null, //nombre de la persona
    timeName: null, // nombre del horario personalizado
    startShift: '07:00',// hora inicio
    endShift: '16:30',// hora fin
    timeBreak: null,
    schedule_time: null,
    schedule: null, // id del schedule/cronograma (TABLA SCHEDULES)
    idUser: null, // Id de la persona seleccionada (COLUMNA EMPLOYEE_ID DE LA TABLA SCHEDULES)
    date: date, // fecha seleccionada en el calendario
    personalized: true, // Seleccionar turno => false, Seleccionar Horario Personalizado => false, Nuevo horario personalizado =>true
    type: 1,// Solo el => 1; Resto de la actividad => 2; Rango de fechas => 3; Fechas específicos => 4
    details: [],
    loading: false,
    /* la propiedad details depende de la propiedad type, es decir:
        si la opción type es 1, en details se debe enviar la fecha (Solo el =>) ej: ['2024-03-07']
        si la opcion type es 2, en details se debe enviar el id de la actividad seleccionada (EN BASE DE DATOS ES LA TABLA TASK) ej: [7683]
        si la opcion type es 3, en details se debe enviar la fecha inicial y la fecha final (EN LA PRIMERA POSICION SIEMPRE SE DEBE MANDAR LA FECHA INICIAL), ej: ['2024-03-01','2024-03-10']
        si la opcion type es 4, en details se debe enviar las fechas seleccionadas en el calendario, no importa el orden ej: ['2024-03-01', '2024-03-10', '2024-01-01','2024-02-10']
    */
});
const editHorario = ref()
const nuevoHorario = ref({})
const optionSelectHours = ref('select')
const modhours = ref(false)

const editHour = (horario, data, option) => {
    editHorario.value = horario
    editHorario.value.data = data
    editHorario.value.option = option
    formEditHour.value.idUser = data.employee_id
    formEditHour.value.schedule = data.id
    formEditHour.value.schedule_time = horario.id
    formEditHour.value.userName = data.name
    nuevoHorario.value = {}
    modhours.value = true
}
//#endregion

const filter = ref('');

const selectDays = ref()
const tabActive = ref()
const save = async () => {
    task.value = tasks.value.find((task) => task.id = editHorario.value.data.task_id)
    formEditHour.value.loading = true
    if (tabActive.value != 2) {
        formEditHour.value.personalized = false
        formEditHour.value.startShift = new Date(nuevoHorario.value.startShift).toLocaleString('es-CO',
            { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' })
        formEditHour.value.endShift = new Date(nuevoHorario.value.endShift).toLocaleString('es-CO',
            { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' })
        formEditHour.value.timeBreak = parseFloat(nuevoHorario.value.timeBreak)
    } else {
        formEditHour.value.startShift = new Date(formEditHour.value.startShift).toLocaleString('es-CO',
            { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' })
        formEditHour.value.endShift = new Date(formEditHour.value.endShift).toLocaleString('es-CO',
            { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' })
        formEditHour.value.timeBreak = parseFloat(formEditHour.value.timeBreak)
    }

    if (formEditHour.value.type == 1) {
        formEditHour.value.details[0] = date.value.toISOString().split("T")[0]
    } else if (formEditHour.value.type == 2) {
        formEditHour.value.details[0] = formEditHour.value.schedule
    }
    else {
        selectDays.value.forEach((day, index) => {
            formEditHour.value.details[index] = new Date(day).toISOString().split("T")[0]
        })
    }
    //aplicar validaciones de campos requeridos (TODOS LOS CAMPOS SON REQUERIDOS)
    /*
    NOTA:
    Se debe cambiar el campo de hora inicio y hora fin a un formato de 24 horas.
    */
    await axios.post(route(editHorario.value.option != 'delete' ? 'programming.saveCustomizedSchedule' : 'programming.removeSchedule'), formEditHour.value)
        .then((res) => {
            if (res.data.status) {
                modhours.value = false
                listaDatos.value[formEditHour.value.schedule] = res.data.task
                editHorario.value.data.hora_inicio = formEditHour.value.startShift
                editHorario.value.data.hora_fin = formEditHour.value.endShift
                getAssignmentHoursForEmployee((formEditHour.value.idUser))
                toast.add({ severity: 'success', group: "customToast", text: res.data.mensaje, life: 2000 })
            }
            else {
                conflicts.value = Object.values(res.data.conflict)
                if (conflicts.value.length > 0) {
                    openConflict.value = true;
                    // task.value.id = schedule
                }
                // toast.add({ severity: 'danger', group: "customToast", text: res.data.mensaje, life: 2000 })
            }
            formEditHour.value.loading = false
        }).catch((error) => {
            console.log(error)
            toast.add({ severity: 'error', group: "customToast", text: 'Error no controlado', life: 2000 })
        });
}


//#region popup confirmacion coliciones
const formColision = ref({
    actionType: null, // accion a realizar: 1: Reemplazar , 2: Omitir
    idTask: null,// idTask de la Nueva tarea
    scheduleTime: null, // id del schedule time
    endSchedule: null, // se manda true si es la ultima colisión
    startShift: null, // hora de inicio de la nueva tarea
    endShift: null, // hora final de la nueva tarea
});

async function confirm1(event, scheduleTime, option, data) {
    formColision.value.idTask = task.value.id
    formColision.value.startShift = format24h(task.value.shift.startShift)
    formColision.value.endShift = format24h(task.value.shift.endShift)
    if (option == 'omit') {
        scheduleTime.status = option
        formColision.value.actionType = 1
        formColision.value.endSchedule = data.find((a) => { a.status == null }) == undefined
        scheduleTime.status = undefined
        if (formColision.value.endSchedule) {
            confirm.require({
                target: event.currentTarget,
                message: '¿Está totalmente seguro? No se puede deshacer',
                icon: 'pi pi-exclamation-triangle text-danger',
                rejectClass: 'p-button-secondary p-button-outlined p-button-sm',
                acceptClass: 'p-button-sm p-button-success',
                rejectLabel: 'No',
                acceptLabel: 'Sí',
                accept: async () => {
                    scheduleTime.status = option
                    let status = resolveCollision(formColision)
                    if (!status) {
                        scheduleTime.status = undefined
                        toast.add({ severity: 'error', group: "customToast", text: 'Error no controlado', life: 3000 });
                    } else {
                        conflicts.value.splice(conflicts.value.indexOf(data), 1);
                        openConflict.value=conflicts.value.length>0
                        toast.add({ severity: 'info', group: "customToast", text: 'Omitida', life: 3000 });
                    }
                }, reject: () => {
                    scheduleTime.status = undefined
                    formColision.value.endSchedule = false
                }
            })
        } else {
            let status = resolveCollision(formColision)
            if (!status) {
                scheduleTime.status = undefined
                toast.add({ severity: 'error', group: "customToast", text: 'Error no controlado', life: 3000 });
            } else {
                toast.add({ severity: 'info', group: "customToast", text: 'Omitida', life: 3000 });
            }
        }
    } else if (option == 'remplace') {
        scheduleTime.status = option
        formColision.value.actionType = 2
        formColision.value.endSchedule = data.find((a) => { a.status == null }) == undefined
        scheduleTime.status = undefined
        if (formColision.value.endSchedule) {
            confirm.require({
                target: event.currentTarget,
                message: '¿Está totalmente seguro? No se puede deshacer',
                icon: 'pi pi-exclamation-triangle text-danger',
                rejectClass: 'p-button-secondary p-button-outlined p-button-sm',
                acceptClass: 'p-button-sm p-button-success',
                rejectLabel: 'No',
                acceptLabel: 'Sí',
                accept: async () => {
                    scheduleTime.status = option
                    let status = resolveCollision(formColision)
                    if (!status) {
                        scheduleTime.status = undefined
                        toast.add({ severity: 'error', group: "customToast", text: 'Error no controlado', life: 3000 });
                    } else {
                        conflicts.value.splice(conflicts.value.indexOf(data), 1);
                        openConflict.value=conflicts.value.length>0
                        toast.add({ severity: 'info', group: "customToast", text: 'Omitida', life: 3000 });
                    }
                }, reject: () => {
                    scheduleTime.status = undefined
                    formColision.value.endSchedule = false
                }
            })
        } else {
            let status = resolveCollision(formColision)
            if (!status) {
                scheduleTime.status = undefined
                toast.add({ severity: 'error', group: "customToast", text: 'Error no controlado', life: 3000 });
            } else {
                toast.add({ severity: 'info', group: "customToast", text: 'Omitida', life: 3000 });
            }
        }
    } else {
        confirm.require({
            target: event.currentTarget,
            message: '¿Está totalmente seguro? No se puede deshacer',
            icon: 'pi pi-exclamation-triangle text-danger',
            rejectClass: 'p-button-secondary p-button-outlined p-button-sm',
            acceptClass: 'p-button-sm p-button-success',
            rejectLabel: 'No',
            acceptLabel: 'Sí',
            accept: async () => {
                if (option == 'omitAll') {
                    let error
                    data.forEach((scheduleD) => {
                        const endIndex = scheduleD.findLastIndex((element) => element.status == null)
                        scheduleD.forEach((scheduleT, index) => {
                            if (scheduleT.status == null) {
                                formColision.value.endSchedule = endIndex == index ? true : false
                                formColision.value.actionType = 2
                                formColision.value.scheduleTime = scheduleT.idScheduleTime
                                let status = resolveCollision(formColision)
                                if (!status) {
                                    error = scheduleT
                                    toast.add({ severity: 'error', group: "error", text: 'Hubo un error inesperado', life: 3000 });
                                } else {
                                    scheduleT.status = 'remplace'
                                }
                            }
                        })
                    })
                    if (error) {
                        toast.add({ severity: 'warn', group: "customToast", text: 'Se remplazaron todas con errores', life: 3000 });
                    } else {
                        conflicts.value=[]
                        openConflict.value=conflicts.value.length>0
                        toast.add({ severity: 'success', group: "customToast", text: 'Se remplazaron todas', life: 3000 });
                    }
                } else if (option == 'remplaceAll') {
                    let error
                    data.forEach((scheduleD) => {
                        const endIndex = scheduleD.findLastIndex((element) => element.status == null)
                        scheduleD.forEach((scheduleT, index) => {
                            if (scheduleT.status == null) {
                                formColision.value.endSchedule = endIndex == index ? true : false
                                formColision.value.actionType = 1
                                formColision.value.scheduleTime = scheduleT.idScheduleTime
                                let status = resolveCollision(formColision)
                                if (!status) {
                                    error = scheduleT
                                    toast.add({ severity: 'error', group: "error", text: 'Hubo un error inesperado', life: 3000 });
                                } else {
                                    scheduleT.status = 'remplace'
                                }
                            }
                        })
                    })
                    if (error) {
                        toast.add({ severity: 'warn', group: "customToast", text: 'Se remplazaron todas con errores', life: 3000 });
                    } else {
                        conflicts.value=[]
                        openConflict.value=conflicts.value.length>0
                        toast.add({ severity: 'success', group: "customToast", text: 'Se remplazaron todas', life: 3000 });
                    }
                } else if (option == 'omitAllDay') {
                    let error
                    const endIndex = data.findLastIndex((element) => element.status == null)
                    await data.forEach((scheduleT, index) => {
                        if (scheduleT.status == null) {
                            formColision.value.endSchedule = endIndex == index ? true : false
                            formColision.value.actionType = 2
                            formColision.value.scheduleTime = scheduleT.idScheduleTime
                            let status = resolveCollision(formColision)
                            if (!status) {
                                error = scheduleT
                                toast.add({ severity: 'error', group: "error", text: 'Hubo un error inesperado', life: 3000 });
                            } else {
                                scheduleT.status = 'omit'
                            }
                        }
                    })
                    if (error) {
                        toast.add({ severity: 'warn', group: "customToast", text: 'Se omitieron todas las del dia con errores', life: 3000 });
                    } else {
                        conflicts.value.splice(conflicts.value.indexOf(data), 1);
                        openConflict.value=conflicts.value.length>0
                        toast.add({ severity: 'success', group: "customToast", text: 'Se omitieron todas las del dia', life: 3000 });
                    }
                } else if (option == 'remplaceAllDay') {
                    let error
                    const endIndex = data.findLastIndex((element) => element.status == null)
                    await data.forEach((scheduleT, index) => {
                        if (scheduleT.status == null) {
                            formColision.value.endSchedule = endIndex == index ? true : false
                            formColision.value.actionType = 1
                            formColision.value.scheduleTime = scheduleT.idScheduleTime
                            let status = resolveCollision(formColision)
                            if (!status) {
                                error = scheduleT
                                toast.add({ severity: 'error', group: "error", text: 'Hubo un error inesperado', life: 3000 });
                            } else {
                                scheduleT.status = 'remplace'
                            }
                        }
                    })
                    if (error) {
                        toast.add({ severity: 'warn', group: "customToast", text: 'Se remplazaron todas las del dia con errores', life: 3000 });
                    } else {
                        conflicts.value.splice(conflicts.value.indexOf(data), 1);
                        openConflict.value=conflicts.value.length>0
                        toast.add({ severity: 'success', group: "customToast", text: 'Se remplazaron todas las del dia', life: 3000 });
                    }
                }
            },
            reject: () => {

                // toast.add({ severity: 'error', group: "customToast", text: 'You have rejected', life: 3000 });
            }
        });
    }
};

async function resolveCollision(form) {
    await axios.post(route('programming.collisionsPerDay', form)).then((r) => {
         return true
    }).catch((e) => {
        console.log(e)
        return false
    })
    return true
}

//#endregion

</script>

<template>
    <AppLayout>
        <div class="h-full w-full grid grid-cols-3 ">
            <span class="col-span-2 space-y-1 pt-1 px-1">
                <span class="flex justify-between items-center">
                    <span class="text-xl font-bold text-primary h-full items-center flex">
                        Programación de actividades
                    </span>
                    <div class="flex items-center space-x-2">
                        <ButtonGroup>
                            <Button label="Hoy" :outlined="optionValue != 'today'"
                                @click="getTask('today'); getPersonalData()" />
                            <Button label="Mañana" :outlined="optionValue != 'tomorrow'"
                                @click="getTask('tomorrow'); getPersonalData()" />
                        </ButtonGroup>
                        <Calendar :disabledDays="[0]" v-model="date" showIcon :manualInput="false" dateFormat="dd/mm/yy"
                            @date-select="getTask('date'); getPersonalData()" :pt="{
                                root: '!w-44',
                                input: '!h-8 text-center'
                            }" />
                    </div>
                </span>
                <Listbox :options="tasks" :filterFields="['task', 'name', 'project']" class="col-span-2" filter :pt="{
                                list: '!h-[76vh] !px-1 !snap-y !snap-mandatory',
                                item: '!h-full !p-0 !rounded-md !snap-start !my-0.5',
                                filterInput: '!h-8',
                                header: '!p-1'
                            }">
                    <template #option="slotProps">
                        <div class="flex flex-col justify-between h-full p-2 border rounded-md shadow-md snap-start">
                            <p><b>{{ slotProps.option.task }}</b> <i class="fa-solid fa-angle-right"></i>
                                {{ slotProps.option.name }}
                            </p>
                            <p class="text-xs italic uppercase text-primary">{{ slotProps.option.project.name }}</p>
                            <span class="grid items-center text-xs grid-cols-6 space-x-2">
                                <span class="grid grid-cols-3">
                                    <p class="font-bold ">I:</p>
                                    <p class="font-mono col-span-2 cursor-default" v-tooltip="'Fecha inicio'">
                                        {{ slotProps.option.startDate }}
                                    </p>
                                    <p class="font-bold">F:</p>
                                    <p class="font-mono col-span-2 cursor-default" v-tooltip="'Fecha fin'">
                                        {{ slotProps.option.endDate }}
                                    </p>
                                </span>
                                <span class="flex justify-center">
                                    <Knob v-tooltip.top="'Avance: ' + parseInt(slotProps.option.percentDone) + '%'"
                                        :model-value=parseInt(slotProps.option.percentDone) :size=50
                                        valueTemplate="{value}%" readonly />
                                </span>
                                <div class="text-center justify-center">
                                    <p class="font-bold">Horario predefinido</p>
                                    <span
                                        class="flex items-center justify-center space-x-2 text-green-900 bg-green-200 rounded-md p-1">
                                        <p class="">
                                            {{ format24h(slotProps.option.shift.startShift) }}
                                        </p>
                                        <p class="">
                                            {{ format24h(slotProps.option.shift.endShift) }}
                                        </p>
                                    </span>
                                </div>
                                <div class="text-center justify-center">
                                    <p class="font-bold">Valor estimado</p>
                                    <p class="text-green-900 bg-green-200 rounded-md p-1">$1.000.000
                                    </p>
                                </div>
                                <div class="text-center justify-center">
                                    <p class="font-bold">Valor programado</p>
                                    <p class="text-green-900 bg-green-200 rounded-md p-1">$1.000.000
                                    </p>
                                </div>
                                <div class="text-center justify-center">
                                    <p class="font-bold">Diferencia</p>
                                    <p class="text-green-900 bg-green-200 rounded-md p-1">$1.000.000
                                    </p>
                                </div>
                            </span>
                            <div v-if="loadingTasks ? true : loadingTask[slotProps.option.id] ? true : false"
                                class="flex flex-col items-center justify-center h-full p-2">
                                <Loading message="Cargando" />
                            </div>
                            <Container v-if="!loadingTask[slotProps.option.id]"
                                class="h-full p-2 overflow-auto border border-blue-400 border-dashed rounded-lg shadow-sm hover:bg-blue-50 shadow-primary custom-scroll"
                                @drop="onDrop(slotProps.option, $event)" group-name="1">
                                <div class="grid grid-cols-2 gap-1"
                                    v-if="listaDatos[slotProps.option.id] !== undefined && listaDatos[slotProps.option.id].length != 0">
                                    <div v-for="(item, index) in listaDatos[slotProps.option.id]"
                                        class="p-1 mt-1 border-2 rounded-md">
                                        <div class="flex items-center justify-between w-full ">
                                            <p class="text-sm font-semibold ">{{ item.name }}</p>
                                            <button v-tooltip.top="'Eliminar de la Actividad'"
                                                v-if="item.is_my_personal"
                                                @click="editHour(slotProps.option, item, 'delete')">
                                                <i
                                                    class="fa-solid fa-circle-xmark text-danger hover:animate-pulse hover:scale-125" />
                                            </button>
                                        </div>
                                        <div class="flex items-center justify-between w-full font-mono align-middle ">
                                            <div class="grid w-full grid-cols-3 gap-2">
                                                <div v-for="horario in item.schedule_times"
                                                    class="flex items-center justify-between px-1 py-1 text-green-900 bg-green-200 rounded-md cursor-default group">
                                                    <button v-tooltip.bottom="'En desarrollo'"
                                                        v-if="item.is_my_personal" class="hidden group-hover:flex"
                                                        @click="console.log('En desarrollo')">
                                                        <i
                                                            class="fa-solid fa-trash-can text-danger text-xs hover:animate-pulse hover:scale-125"></i>
                                                    </button>
                                                    <span class="w-full text-xs tracking-tighter text-center">
                                                        {{
                                format24h(horario.hora_inicio.slice(0,
                                    horario.hora_inicio.lastIndexOf(':')))
                            }}
                                                        {{
                                    format24h(horario.hora_fin.slice(0,
                                        horario.hora_fin.lastIndexOf(':')))
                                }}
                                                    </span>
                                                    <button v-tooltip.bottom="'Cambiar horario'"
                                                        v-if="item.is_my_personal" class="hidden group-hover:flex"
                                                        @click="optionSelectHours = 'select'; editHour(horario, item, 'modify')">
                                                        <i
                                                            class="fa-solid fa-pencil text-primary text-xs hover:animate-pulse hover:scale-125"></i>
                                                    </button>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="items-center justify-center text-center align-middle opacity-50">
                                    <h2 class="mt-4 text-xl font-medium tracking-wide text-gray-700">No hay personas
                                        asignadas
                                    </h2>

                                    <p class="mt-2 tracking-wide text-gray-500">Arrastre una persona de la lista de la
                                        izquierda
                                        para agregarla a la actividad </p>
                                </div>
                            </Container>
                        </div>
                    </template>

                    <template #empty>
                        <Loading v-if="loadingProgram" class="mt-10" message="Cargando actividades" />
                    </template>

                </Listbox>
            </span>
            <!--#region LISTA PERSONAL-->
            <div class="row-span-2 rounded-lg border">
                <TabView class="tabview-custom h-full" :scrollable="true" :pt="{
                                nav: '!flex !justify-between',
                                panelContainer: '!p-1'
                            }">
                    <TabPanel header="Personas" :pt="{
                                root: 'w-full !p-0',
                                header: '!p-0 w-full',
                                headerTitle: '!w-full !flex !justify-center',
                            }">
                        <CustomInput v-model:input="filter" type="search" icon="fa-solid fa-magnifying-glass" />
                        <Loading v-if="loadingPerson" class="mt-10" message="Cargando personas" />
                        <Container v-else oncontextmenu="return false" onkeydown="return false" behaviour="copy"
                            group-name="1" :get-child-payload="getChildPayload"
                            class="h-[74vh] flex flex-col space-y-1 mt-1 p-1 snap-y snap-mandatory overflow-y-auto">
                            <!-- <span v-for="item in personal"> -->
                            <Draggable v-for="item in personal" v-tooltip.top="{ value: 'Arrastra hasta la tarea donde asignaras la persona', showDelay: 1000, hideDelay: 300, pt:{text:'text-center'} }"
                                :class="(item.Nombres_Apellidos.toUpperCase().includes(filter.toUpperCase()) || item.Cargo.toUpperCase().includes(filter.toUpperCase())) ? '' : '!hidden'"
                                :drag-not-allowed="personalHours[(item.Num_SAP)] < 9.5 ? false : true"
                                class="snap-start rounded-xl shadow-md cursor-pointer hover:bg-primary-light hover:ring-1 hover:ring-primary">
                                <div class="grid grid-cols-5 gap-x-1 p-1">
                                    <img class="custom-image " :src="item.photo" align="center"
                                        onerror="this.src='/svg/cotecmar-logo.svg'" draggable="false"
                                        alt="profile-photo" />
                                    <span class="col-span-3 flex flex-col justify-center">
                                        <p class="text-sm font-semibold truncate  text-gray-900">
                                            {{ item.Nombres_Apellidos }}
                                        </p>
                                        <p class="flex mt-1 text-xs truncate  text-gray-500">
                                            {{ item.Cargo }}
                                        </p>
                                        <p class="flex mt-1 text-xs truncate  text-gray-500">
                                            {{ item.Oficina }}
                                        </p>
                                    </span>
                                    <span class="flex items-center">
                                        <Button v-tooltip.left="'Horas programadas'" class="w-full"
                                            :key="personalHours[item.Num_SAP]"
                                            :icon="personalHours[(item.Num_SAP)] == undefined ? 'fa-solid fa-spinner animate-spin' : undefined"
                                            :label="personalHours[item.Num_SAP] != undefined ? personalHours[item.Num_SAP] + ' horas' : undefined"
                                            :severity="personalHours[item.Num_SAP] < 9.5 ? 'primary' : 'success'"
                                            @click="employeeDialog(item)" :pt="{ label: '!text-xs' }" />
                                    </span>
                                </div>
                            </Draggable>
                            <!-- </span> -->
                        </Container>
                    </TabPanel>

                    <TabPanel header="Grupos" :pt="{
                                root: 'w-full',
                                headerTitle: '!w-full !flex !justify-center'
                            }">
                    </TabPanel>

                </TabView>
            </div>

        </div>
    </AppLayout>

    <!--#region MODALES -->
    <CustomModal v-model:visible="modhours" :footer="false" icon="fa-regular fa-clock" width="60vw"
        :closeOnEscape="false"
        :titulo="editHorario?.option != 'delete' ? 'Modificar horario de ' + editHorario?.data.name : 'Eliminando a ' + editHorario?.data.name + 'de la actividad ' + editHorario?.task">
        <template #body>
            <form @submit.prevent="save" class="pb-2">
                <div v-if="editHorario?.option != 'delete'" class="flex flex-col gap-1">
                    <div class="flex items-center justify-between col-span-3 ">

                        <p class="font-bold">Horario actual:</p>
                        <p class="px-1 py-1 text-green-900 bg-green-200 rounded-md">
                            {{ format24h(editHorario.hora_inicio.slice(0,
                                editHorario.hora_inicio.lastIndexOf(':'))) }}
                            {{ format24h(editHorario.hora_fin.slice(0,
                                editHorario.hora_fin.lastIndexOf(':'))) }}
                        </p>
                    </div>
                    <TabView v-model:activeIndex="tabActive" class="border rounded-md p-1" :pt="{
                                nav: '!flex !justify-between',
                                panelContainer: '!p-1'
                            }">
                        <TabPanel header="Seleccionar Turno" :pt="{
                                root: 'w-full',
                                headerTitle: '!w-full !flex !justify-center',
                            }">
                            <CustomShiftSelector v-model:shift="nuevoHorario" />
                        </TabPanel>
                        <TabPanel header="Seleccionar Horario Personalizado" :pt="{
                                root: 'w-full',
                                headerTitle: '!w-full !flex !justify-center',
                            }">
                            <CustomShiftSelector :shiftUser="parseInt($page.props.auth.user.id)"
                                v-model:shift="nuevoHorario" />
                        </TabPanel>
                        <TabPanel header="Personalizado" :pt="{
                                root: 'w-full',
                                headerTitle: '!w-full !flex !justify-center',
                            }">
                            <div class="h-52 space-y-2 m-1 mt-3">
                                <span class="flex gap-2 items-center">
                                    <label class="-mb-0.5" for="switch1">Guardar en personalizados?</label>
                                    <InputSwitch inputId="switch1" v-model="formEditHour.personalized" />
                                </span>
                                <CustomInput v-if="formEditHour.personalized" v-model:input="formEditHour.timeName"
                                    label="Nombre" type="text" id="name" placeholder="Nombre del horario"
                                    :required="tabActive == 2" />
                                <span class="grid grid-cols-3 gap-x-1">
                                    <CustomInput v-model:input="formEditHour.startShift" label="Hora inicio" type="time"
                                        :stepMinute="30" id="start" placeholder="Hora de inicio"
                                        :required="tabActive === 2" />
                                    <CustomInput v-model:input="formEditHour.endShift" label="Hora fin" type="time"
                                        id="end" :stepMinute="30" placeholder="Hora de fin"
                                        :required="tabActive === 2" />
                                    <CustomInput v-model:input="formEditHour.timeBreak" label="Descanso" type="number"
                                        suffix=" Hora" id="break" placeholder="Descanso en horas"
                                        :required="tabActive === 2" />
                                </span>
                            </div>
                        </TabPanel>
                    </TabView>
                </div>
                <p class="font-bold text-xl">Aplicar por:</p>
                <span class="flex items-center p-2 gap-4">
                    <div v-for="category in [{ name: 'Solo el ' + date.toISOString().split('T')[0], key: 1 }, { name: 'Resto de la actividad', key: 2 }, { name: 'Rango de fechas', key: 3 }, { name: 'Fechas específicos', key: 4 }]"
                        :key="category.key" class="flex items-center">
                        <RadioButton v-model="formEditHour.type" :inputId="category.key + category.name" name="dynamic"
                            :value="category.key" @click="formEditHour.details = []" />
                        <label :for="category.key" class="ml-1 mb-0">{{ category.name }}</label>
                    </div>
                </span>
                <!-- {{editHorario?.option != 'delete'?tabActive != 2? nuevoHorario?.startShift!=null?false:true:false:false }} -->
                <span class="w-full grid grid-cols-4 justify-end gap-5 items-center">
                    <Calendar v-if="formEditHour.type == 3 || formEditHour.type == 4" show-icon v-model="selectDays"
                        class="col-span-3" :selectionMode="formEditHour.type == 3 ? 'range' : 'multiple'"
                        :manualInput="false" :pt="{
                                root: '!w-full',
                                input: '!h-8'
                            }" />
                    <Button type="submit" class="col-start-4" :loading="formEditHour.loading"
                        :disabled=" (editHorario?.option !== 'delete') && (tabActive !== 2) && (nuevoHorario?.startShift !== null) "
                        :severity="editHorario?.option != 'delete' ? 'success' : 'danger'"
                        :icon="editHorario?.option != 'delete' ? 'fa-solid fa-floppy-disk' : 'fa-solid fa-trash-can'"
                        :label="editHorario?.option != 'delete' ? 'Guardar' : 'Eliminar'" />
                </span>
            </form>
        </template>
    </CustomModal>

    <CustomModal v-model:visible="open" width="90vw" :close-on-escape="false"
        :titulo="'Ver detalle de horario de ' + employee.Nombres_Apellidos">
        <template #body>
            <div class="py-2 max-h-[80vh] w-[40vw]">
                <FullCalendar :initialEvents="events" :tasks="tasks" :date="date" :employee="employee"
                    :key="rendersCalendars" />
            </div>
        </template>
    </CustomModal>

    <CustomModal icon="fa-solid fa-triangle-exclamation" :base-z-index="10" v-model:visible="openConflict"
        severity="danger" :closable="false" :close-on-escape="false" width="90vw"
        :titulo="'Existen colisiones al programar tarea: ' + task.name">
        <template #body>
            <div class="py-2 flex flex-col gap-4">
                <div v-for="conflictForDay in conflicts"
                    class="border ring-success ring-1 rounded-md p-1 hover:shadow-lg hover:shadow-primary-light">
                    <span class="flex space-x-2 font-bold">
                        <span class="text-lg flex items-center gap-2 p-2">
                            <p>
                                Fecha:
                            </p>
                            <p>
                                {{ conflictForDay[0]?.fecha }}
                            </p>
                        </span>
                        <span v-if="conflictForDay.length > 1"
                            class="flex p-2 justify-end items-center gap-2  w-[400px]">
                            <Button class="!w-full" label="Reemplazar todo" severity="contrast"
                                @click="confirm1($event, null, 'remplaceAllDay', conflictForDay)" />
                            <Button class="!w-full" label="Omitir todo" severity="success"
                                @click="confirm1($event, null, 'omitAllDay', conflictForDay)" />
                        </span>
                    </span>
                    <div class="flex flex-col gap-2">
                        <div v-for="conflict in conflictForDay" class="grid grid-cols-5 border rounded-md"
                            :class="conflict.status ? conflict.status == 'omit' ? '!bg-green-100' : '!bg-red-100' : ''">
                            <div class="flex  p-0.5 col-span-4">
                                <div class="flex justify-between items-center w-full">
                                    <Breadcrumb :pt="{ root: '!bg-transparent' }"
                                        :model="[{ label: conflict.NombreProyecto, tooltip: 'Nombre del proyecto',class:'font-bold' }, { label: conflict.nombrePadreTask, tooltip: 'Nombre del proceso' }, { label: conflict.nombreTask, tooltip: 'Nombre de la actividad',class:'font-bold italic' }]">
                                        <template #item="item">
                                            <p v-tooltip.bottom="{ value: item.item.tooltip, pt: { text: 'text-center' } }"
                                                :class="item.item.class"
                                                class="cursor-default truncate w-full">
                                                {{ item.label }}
                                            </p>
                                        </template>
                                    </Breadcrumb>
                                    <div class="flex items-center">
                                        <div class="px-4 flex w-min space-x-2">
                                            <p class="flex space-x-2">
                                                <span class="font-bold">Inicio: </span>
                                                <span>
                                                    {{
                                conflict.horaInicio.slice(0, conflict.horaInicio.lastIndexOf(':'))
                            }}
                                                </span>
                                            </p>
                                            <p class="flex space-x-2">
                                                <span class="font-bold"> Fin: </span>
                                                <span>
                                                    {{ conflict.horaFin.slice(0, conflict.horaFin.lastIndexOf(':')) }}
                                                </span>
                                            </p>
                                        </div>
                                        <div class="pr-10">
                                            <Knob v-tooltip.top="'Avance: ' + (conflict.taskDetails.percentDone) + '%'"
                                                :model-value="parseFloat(conflict.taskDetails.percentDone)" :size=50
                                                valueTemplate="{value}%" readonly />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="conflict.status == null" class="flex p-2 justify-center items-center gap-2">
                                <Button class="!w-full" label="Reemplazar" severity="warning" v-tooltip.top="'Remplaza esta actividad por la nueva'"
                                    @click="confirm1($event, conflict, 'remplace', conflictForDay)" />
                                <Button label="Omitir" class="!w-full" severity="success"  v-tooltip.top="'Omite esta actividad y programa el restante del horario'"
                                    @click="confirm1($event, conflict, 'omit', conflictForDay)" />
                            </div>
                            <div v-else class="flex p-2 justify-between items-center">
                                <p>{{ conflict.status == 'omit' ? 'Sin modificaciones' : 'Remplazada' }}</p>
                                <Button icon="fa-solid fa-rotate-left" text severity="danger" v-tooltip="'Deshacer'"
                                    @click="conflict.status = null" rounded />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <template #footer>
            <Button label="Reemplazar todas las coliciones" severity="danger"
                @click="confirm1($event, null, 'remplaceAll',conflicts)" />
            <Button label="Omitir todas las coliciones" severity="success"
                @click="confirm1($event, null, 'omitAll',conflicts)" />
        </template>
    </CustomModal>
    <!-- #endregion -->
</template>

<style scoped>
.custom-image {
    width: 200px;
    height: 50px;
    /* object-position: 50% 30%; */
    border-radius: 5px 10px;
    object-fit: cover;
    /* Opciones: 'cover', 'contain', 'fill', etc. */
}

.info-resto {
    font-size: 15px;
    text-wrap: balance;
    opacity: .5;
}
</style>