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
const toast = useToast();
const confirm = useConfirm();
// const { hasRole, hasPermission } = usePermissions()

const openConflict = ref(false)

//#region Draggable
const listaDatos = ref({})
const date = ref(new Date().toISOString().split("T")[0])
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
        await axios.post(route('programming.store'), { task_id: collection.id, employee_id: payload.Num_SAP, name: payload.Nombres_Apellidos, fecha: date.value }).then((res) => {
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
    axios.get(route('get.assignment.hours', [date.value, employee_id])).then((res) => {
        personalHours.value[(employee_id)] = res.data;
    })
}

const getAssignmentHoursAll = () => {
    personalHours.value = {}
    personal.value.forEach(
        element => {
            axios.get(route('get.assignment.hours', [date.value, (element.Num_SAP)])).then((res) => {
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
            date.value = new Date().toISOString().split("T")[0];
            break;
        case 'tomorrow':
            var tomorrow = new Date()
            date.value = new Date(tomorrow.setDate(tomorrow.getDate() + 1)).toISOString().split("T")[0];
            break;
        default:
            break;
    }
    tasks.value = [];
    await axios.get(route('actividadesDeultimonivel', { date: date.value })).then((res) => {
        tasks.value = res.data
        loadingProgram.value = false;
    })
    tasks.value.forEach(element => {
        loadingTask.value[element.id] = true
        axios.get(route('get.schedule.task', { task_id: element.id, date: date.value })).then((res) => {
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
    return new Date("1970-01-01T" + hora).toLocaleString('es-CO',
        { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' })
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
    axios.get(route('get.times.employees', { date: date.value, employee_id: item.Num_SAP }))
        .then((res) => {
            events.value = res.data.times
            rendersCalendars.value++;
        })
        .catch(error => {
            console.log(error);
        })
}

const formColision = ref({
    actionType: null, // accion a realizar: 1: reemplazar tarea, 2: reemplazar todo en la fecha, 3: reemplazar todas las colisiones
    idUser: null,//employee_id
    idTask: null, //id de la tabla tasks
    idSchedule: null, // id de la actividad (schedules)
    idScheduleTime: null, // id del detalle de la actividad
    startShift: null, // hora de inicio de la nueva tarea
    endShift: null, // hora final de la nueva tarea
    details: [] //arreglo especificando el id de la tabla schedule_times EJ: [1,2,3,4]
});

const form = ref({
    userName: null, //nombre de la persona
    timeName: null, // nombre del horario personalizado
    startShift: null,// hora inicio
    endShift: null,// hora fin
    timeBreak: null,
    schedule_time: null,
    schedule: null, // id del schedule/cronograma (TABLA SCHEDULES)
    idUser: null, // Id de la persona seleccionada (COLUMNA EMPLOYEE_ID DE LA TABLA SCHEDULES)
    date: date, // fecha seleccionada en el calendario
    personalized: false, // Seleccionar turno => false, Seleccionar Horario Personalizado => false, Nuevo horario personalizado =>true
    type: 1,// Solo el => 1; Resto de la actividad => 2; Rango de fechas => 3; Fechas específicos => 4
    details: [],
    loading: false,
    type: ''
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

const toggle = (horario, data, option) => {
    editHorario.value = horario
    editHorario.value.data = data
    editHorario.value.option = option
    form.value.idUser = data.employee_id
    form.value.schedule = data.id
    form.value.schedule_time = horario.id
    form.value.userName = data.name
    nuevoHorario.value = {}
    modhours.value = true
}
//#endregion

const filter = ref('');

const selectDays = ref()
const tabActive = ref()
const save = async () => {
    form.value.loading = true
    if (!form.value.personalized) {
        form.value.startShift = new Date(nuevoHorario.value.startShift).toLocaleString('es-CO',
            { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' })
        form.value.endShift = new Date(nuevoHorario.value.endShift).toLocaleString('es-CO',
            { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' })
        form.value.timeBreak = parseFloat(nuevoHorario.value.timeBreak)
    } else {
        form.value.startShift = new Date(form.value.startShift).toLocaleString('es-CO',
            { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' })
        form.value.endShift = new Date(form.value.endShift).toLocaleString('es-CO',
            { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' })
        form.value.timeBreak = parseFloat(form.value.timeBreak)
    }

    if (form.value.type == 1) {
        form.value.details[0] = date.value
    } else if (form.value.type == 2) {
        form.value.details[0] = form.value.schedule
    }
    else {
        selectDays.value.forEach((day, index) => {
            form.value.details[index] = new Date(day).toISOString().split("T")[0]
        })
    }
    //aplicar validaciones de campos requeridos (TODOS LOS CAMPOS SON REQUERIDOS)
    /*
    NOTA:
    Se debe cambiar el campo de hora inicio y hora fin a un formato de 24 horas.
    */
    await axios.post(route(editHorario.value.option != 'delete' ? 'programming.saveCustomizedSchedule' : 'programming.removeSchedule'), form.value)
        .then((res) => {
            if (res.data.status) {
                modhours.value = false
                listaDatos.value[form.value.schedule] = res.data.task
                editHorario.value.data.hora_inicio = form.value.startShift
                editHorario.value.data.hora_fin = form.value.endShift
                getAssignmentHoursForEmployee((form.value.idUser))
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
            console.log(res);
            form.value.loading = false
        }).catch((error) => {
            console.log(error)
            toast.add({ severity: 'danger', group: "customToast", text: 'Error no controlado', life: 2000 })
        });

}


//# region popup confirmacion coliciones

const confirm1 = (event, data) => {
    confirm.require({
        target: event.currentTarget,
        message: '¿Esta totalmente seguro?',
        icon: 'pi pi-exclamation-triangle text-danger',
        rejectClass: 'p-button-secondary p-button-outlined p-button-sm',
        acceptClass: 'p-button-sm p-button-success',
        rejectLabel: 'No',
        acceptLabel: 'Sí',
        accept: () => {
            console.log(data)
            toast.add({ severity: 'info', group: "customToast", text: 'You have accepted', life: 3000 });
        },
        reject: () => {
            toast.add({ severity: 'error', group: "customToast", text: 'You have rejected', life: 3000 });
        }
    });
};

//#endregion

</script>

<template>
    <AppLayout>
        <div class="h-[89vh] overflow-y-auto">
            <span class="flex justify-between items-center px-4">
                <span class="text-xl font-bold text-primary h-full items-center flex">
                    Programación de Actividades por semana
                </span>
                <div class="flex items-center space-x-2">
                    <CustomInput type="week" v-model:input="date" @change="getTask('date'); getPersonalData()" />
                </div>
            </span>
            <!--#region LISTA PERSONAL-->
        </div>
    </AppLayout>





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