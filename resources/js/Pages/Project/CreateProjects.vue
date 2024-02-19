<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, onMounted } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import { ClockIcon } from '@heroicons/vue/24/outline'
import axios from 'axios'
import ToggleButton from 'primevue/togglebutton'
import Listbox from 'primevue/listbox';
import Loading from '@/Components/Loading.vue'
import CustomUpload from "@/Components/CustomUpload.vue";
import NoContentToShow from '@/Components/NoContentToShow.vue'
import CustomDataTable from '@/Components/CustomDataTable.vue'
import CustomInput from '@/Components/CustomInput.vue'
import CustomModal from '@/Components/CustomModal.vue'
import { FormWizard, TabContent } from 'vue3-form-wizard'
import 'vue3-form-wizard/dist/style.css'
import Toast from 'primevue/toast';
import { useToast } from "primevue/usetoast";
import Empty from '@/Components/Empty.vue'
import { useSweetalert } from '@/composable/sweetAlert'
const { confirmDelete } = useSweetalert();
const toast = useToast();

const props = defineProps({
    project: Object,
    contracts: Array,
    authorizations: Array,
    quotes: Array,
    ships: Array,
    milestones: {
        type: Array,
        default: []
    },
    tools: {
        type: Array,
        default: []
    }
    // 'typeShips': Array,
})

//#region Referencias (v-model)
const checked = ref(true)
const contractSelect = ref()
const authorizationSelect = ref()
const quoteSelect = ref()
const selectedShips = ref([])
const API_Ships = ref(props.ships)
const filteredShips = ref(props.ships)
const keyword = ref('')
const shiftSelect = ref('1')
const shiftOptions = ref()
const showLoading = ref(true)
const showNoContent = ref(false)
//#endregion

const searchShips = () => {
    const searchWord = keyword.value.toLowerCase().trim()

    filteredShips.value = API_Ships.value.filter(ship =>
        ship.name.toLowerCase().includes(searchWord) ||
        ship.idHull.toLowerCase().includes(searchWord) ||
        ship.type_ship.name.toLowerCase().includes(searchWord)
    )
}

//#region CustomDataTable
const columnas = [
    { field: 'title', header: 'Nombre', sortable: true, filter: true, },
    { field: 'value', header: 'Valor', filter: true, sortable: true, type: 'currency', class: 'w-64' },
    { field: 'type', header: 'Tipo de Hito', filter: true, sortable: true },
    { field: 'end_date', header: 'Fecha de terminación', filter: true, sortable: true },
    { field: 'advance', header: 'Avance', type: 'number', filter: true, sortable: true },
]


const actions = [
    { event: 'edit', severity: 'warning', icon: 'fa-solid fa-pencil', text: true, outlined: false, rounded: false },
    { event: 'delete', severity: 'danger', icon: 'fa-solid fa-trash-can', text: true, outlined: false, rounded: false },]

//#endregion

//#region ENUMS
//Tipo de Proyecto
const typeSelect = ref()
const typeOptions = ref([
    { name: 'PROYECTO DE VENTA (ARTEFACTO NAVAL)' },
    { name: 'PROYECTO DE VENTA (SERV. INDUSTRIA)' },
    { name: 'PROYECTO DE VENTA (SUMINISTRO/SERVICIO)' },
    { name: 'PROYECTO DE INVERSION INTERNA' },
    { name: 'PROYECTO DE INVERSIÓN (ARTEFACTO NAVAL)' }
])

//Estado de Proyecto
const statusSelect = ref()
const statusOptions = ref([
    { name: 'DISEÑO Y CONSTRUCCIÓN' },
    { name: 'CONSTRUCCIÓN' },
    { name: 'DISEÑO' },
    { name: 'GARANTIA' },
    { name: 'SERVICIO POSTVENTA' }
])

//Alcance de Proyecto
const scopeSelect = ref()
const scopeOptions = ref([
    { name: 'ADQUISICIÓN Y ENTREGA' },
    { name: 'CO DESARROLLO DISEÑO Y CONSTRUCCIÓN' },
    { name: 'CO PRODUCCIÓN' },
    { name: 'CONSTRUCCIÓN' },
    { name: 'DISEÑO BUQUE' },
    { name: 'DISEÑO Y CONSTRUCCIÓN' },
    { name: 'SERVICIOS INDUSTRIALES' }
])
//#endregion

//#region UseForm
const formData = useForm({
    id: props.project?.id ?? null,
    name: props.project?.name ?? '',
    contract_id: props.project?.contract_id ?? null,
    authorization_id: props.project?.authorization_id ?? null,
    quote_id: props.project?.quote_id ?? null,
    type: props.project?.type ?? null, //ENUMS
    SAP_code: props.project?.SAP_code ?? null,
    status: props.project?.status ?? null, //ENUMS
    scope: props.project?.scope ?? null, //ENUMS
    supervisor: props.project?.supervisor ?? null,
    cost_sale: props.project?.cost_sale ?? [0, 'COP'],
    observations: props.project?.observations ?? null,
    start_date: props.project?.start_date ?? null,
    end_date: props.project?.end_date ?? null,
    hoursPerDay: parseFloat(props.project?.hoursPerDay ?? 8.5),
    daysPerWeek: parseInt(props.project?.daysPerWeek ?? 5),
    daysPerMonth: parseInt(props.project?.daysPerMonth ?? 20),
    shift: props.project != null ? props.project.shift : null
})
//#endregion

onMounted(() => {
    getShift()
    getProjectsPropsForEdit()
})

const toggleSelectShip = (shipId) => {
    if (selectedShips.value.includes(shipId)) {
        selectedShips.value = selectedShips.value.filter(id => id !== shipId)
    } else {
        selectedShips.value = [...selectedShips.value, shipId]
    }
}

const selectShiftList = (shiftId) => {
    shiftSelect.value = shiftId
}

const selectAllShips = () => {
    if (selectedShips.value.length == filteredShips.value.length) {
        selectedShips.value = []
    } else {
        selectedShips.value = filteredShips.value.map(ship => ship.id)
    }
}

//Cancelar Creación de Proyectos
const cancelCreateProject = () => {
    router.get(route('projects.index'))
}

/* SUBMIT*/
// const isSaved = ref(false)
const projectIdRef = ref(props.project?.id ?? null)

const beforeChange = async () => {
    formData.type = typeSelect.value?.name ?? null
    formData.status = statusSelect.value?.name ?? null
    formData.scope = scopeSelect.value?.name ?? null
    formData.contract_id = contractSelect.value?.id ?? null
    formData.ships = selectedShips.value
    let switchTabsStates = false

    try {
        if (!projectIdRef.value) {
            await axios.post(route('projects.store'), formData)
                .then((res) => {
                    projectIdRef.value = res.data.project_id
                    toast.add({ summary: 'Guardado', life: 2000 });
                    switchTabsStates = true
                })
            return switchTabsStates
        } else {
            formData.shift = shiftSelect.value
            await axios.put(route('projects.update', projectIdRef.value), formData)
                .then((res) => {
                    toast.add({ summary: 'Guardado', life: 2000 });
                    switchTabsStates = true
                })
            return switchTabsStates
            // return true
        }
    } catch (error) {
        toast.add({ summary: 'Error', life: 2000 });
    }
}

const submit = async () => {
    router.post(route('project.add.ships', projectIdRef.value), {
        ships: selectedShips.value
    });
    router.get(route('projects.index'));

}

const openDialogHito = ref(false)
const formMilestone = useForm({
    id: null,
    title: null,
    value: null,
    end_date: null,
    type: null,
    project_id: props.project?.id ?? null,
    invoiced: false,
    advance: false
});

const showModal = (event, data) => {
    if (data) {
        Object.assign(formMilestone, data)
        formMilestone.advance = formMilestone.advance == 0 ? false : true
    } else {
        formMilestone.reset()
    }
    openDialogHito.value = true
}

const delMilestone = (event, data) => {
    confirmDelete(data.id, 'hito', 'milestones')
}

const save = () => {
    formMilestone.advance = formMilestone.advance == false ? 0 : 100
    if (formMilestone.id) {
        formMilestone.put(route('milestones.update', formMilestone.id), {
            onSuccess: () => {
                formMilestone.reset()
                toast.add({ summary: 'Hito Guardado', life: 2000 });
                openDialogHito.value = false;
            },
            onError: (e) => {
                console.log(e)
            }
        })
    } else {
        formMilestone.post(route('milestones.store'), {
            onSuccess: () => {
                formMilestone.reset()
                toast.add({ summary: 'Hito Guardado', life: 2000 });
                openDialogHito.value = false;
            },
            onError: (e) => {
                console.log(e)
            }
        })
    }
}

const getShift = () => {
    axios.get(route('shift.index'))
        .then(response => {
            shiftOptions.value = response.data[0]
            showLoading.value = false
            shiftOptions.value == 0 ? showNoContent.value = true : shiftOptions.value
        })
}

const getProjectsPropsForEdit = () => {
    projectIdRef.value = props.project?.id ?? null
    scopeSelect.value = { name: props.project?.scope ?? '' }
    statusSelect.value = { name: props.project?.status ?? '' }
    typeSelect.value = { name: props.project?.type ?? '' }
}

function formatDateTime24h(date) {
    return new Date(date).toLocaleString('es-CO',
        { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' })
}

//#region Avance proyectos
const modalProgress = ref(false)
const avance = useForm({
    project_id: props.project?.id ?? null,
    week: null,
    real_progress: null,
    CPI: null,
    SPI: null
})
const guardarAvance = () => {
    avance.post(route('ProgressProjectWeek.store'), {
        onSuccess: () => {
            toast.add({ severity: 'success', group: 'customToast', text: 'Avance guardado', life: 2000 });
        },
        onError: () => {
            toast.add({ severity: 'error', group: 'customToast', text: 'Hubo un error, reintente', life: 2000 });
        }
    })
}
//#endregion


//#region Tareas semanales

const modalWeekTask = ref()
const weekTask = useForm({
    id: null,
    task: null,
    week: null
})
const saveweekTask = () => {
    if (weekTask.id) {
        formMilestone.put(route('weektask.update', formMilestone.id), {
            onSuccess: () => {
                formMilestone.reset()
                toast.add({ summary: 'Hito Guardado', life: 2000 });
                openDialogHito.value = false;
            },
            onError: (e) => {
                console.log(e)
            }
        })
    } else {
        formMilestone.post(route('weektask.store'), {
            onSuccess: () => {
                formMilestone.reset()
                toast.add({ summary: 'Hito Guardado', life: 2000 });
                openDialogHito.value = false;
            },
            onError: (e) => {
                console.log(e)
            }
        })
    }
}


</script>
<template>
    <AppLayout>
        <main class="px-2 overflow-y-scroll">
            <header class="w-full flex justify-between">
                <h2 class="text-lg font-semibold mb-4 w-full text-primary lg:text-2xl">
                    {{ project ? 'Editar proyecto' : 'Nuevo proyecto' }}
                </h2>
                <div v-if="props.project" class="space-x-4 justify-end flex w-full">
                    <Button icon="fa-solid fa-list-check" severity="help" v-tooltip.top="'Tareas de la semana'"
                        @click="modalWeekTask = true" />
                    <Button icon="fa-solid fa-gauge-high" severity="secondary" v-tooltip.top="'Avance del proyecto'"
                        @click="modalProgress = true" />
                    <CustomUpload mode="advanced" titleModal="Subir Estructura de SAP" icon-button="fa-solid fa-chart-bar"
                        tooltip="Subir Estructura" accept=".xlsx,.xls" :url="route('upload.estructure', projectIdRef)" />
                    <!-- 
                    <CustomUpload mode="advanced" :multiple="true" titleModal="Subir Presupuesto del proyecto"
                        icon-button="fa-solid fa-hand-holding-dollar" tooltip="Subir Presupuesto" accept=".xlsx,.xls"
                        url="prueba" severity="success" />

                    <CustomUpload mode="advanced" :multiple="true" titleModal="Subir el avance planeado del proyecto"
                        tooltip="Subir Curva S" accept=".xlsx,.xls" url="prueba" severity="info" />

                    <CustomUpload mode="advanced" :multiple="true" titleModal="Subir Costos ejecutados por el proyecto"
                        icon-button="fa-solid fa-money-bill-trend-up" tooltip="Subir Costos Ejecutados" accept=".xlsx,.xls"
                        url="prueba" severity="danger" /> -->
                </div>
            </header>
            <section class="p-2">
                <!-- AQUÍ VA EL CONTENIDO DEL FORMULARIO-->
                <form-wizard @on-complete="submit()" stepSize="md" color="#2E3092" nextButtonText="Siguiente"
                    backButtonText="Regresar" finishButtonText="Guardar">
                    <!--INFORMACIÓN CONTRACTUAL-->
                    <tab-content title="Información Contractual" icon="fa-solid fa-file-signature"
                        :before-change="beforeChange">
                        <section class="border gap-4 border-gray-200 rounded-lg p-4 grid grid-cols-2">
                            <!--CAMPO NOMBRE DEL PROYECTO (name)-->
                            <CustomInput label="Nombre del Proyecto" placeholder="Escriba el nombre del proyecto"
                                v-model:input="formData.name" :errorMessage="$page.props.errors.name"
                                :invalid="$page.props.errors.name ? true : false" />
                            <!--CAMPO CÓDIGO DE SAP (SAP_code)-->
                            <CustomInput label="Código SAP" placeholder="Escriba el código de SAP"
                                v-model:input="formData.SAP_code" :errorMessage="$page.props.errors.SAP_code"
                                :invalid="$page.props.errors.SAP_code ? true : false" />
                            <!--CAMPO ALCANCE DEL PROYECTO (scope)-->
                            <CustomInput label="Alcance del Proyecto" type="dropdown"
                                placeholder="Seleccione Alcance del Proyecto" optionLabel="name" v-model:input="scopeSelect"
                                showClear :options="scopeOptions" :errorMessage="$page.props.errors.scope"
                                :invalid="$page.props.errors.scope ? true : false" />
                            <CustomInput label="Contrato" type="dropdown" placeholder="Seleccione Alcance del Proyecto"
                                optionLabel="contract_id" v-model:input="contractSelect" showClear :options="contracts"
                                :errorMessage="$page.props.errors.contract_id"
                                :invalid="$page.props.errors.contract_id ? true : false" />
                            <CustomInput label="Autorizaciones" type="dropdown" placeholder="Seleccione Autorización"
                                optionLabel="name" v-model:input="authorizationSelect" showClear :options="authorizations"
                                :errorMessage="$page.props.errors.authorization_id"
                                :invalid="$page.props.errors.authorization_id ? true : false" />
                            <CustomInput label="Estimaciones" type="dropdown" placeholder="Seleccione Estimación"
                                optionLabel="name" v-model:input="quoteSelect" showClear :options="quotes"
                                :errorMessage="$page.props.errors.quote_id"
                                :invalid="$page.props.errors.quote_id ? true : false" />
                        </section>
                    </tab-content>
                    <!--DATOS DEL PROYECTO-->
                    <tab-content title="Datos del Proyecto" icon="fa-solid fa-diagram-project"
                        :before-change="beforeChange">
                        <section class="grid grid-cols-2 border gap-4 border-gray-200 rounded-lg p-4">
                            <!--CAMPO SUPERVISOR (supervisor)-->
                            <CustomInput label="Supervisor" placeholder="Nombre del supervisor"
                                v-model:input="formData.supervisor" :errorMessage="router.page.props.errors.supervisor"
                                :invalid="router.page.props.errors.supervisor ? true : false" />

                            <!-- <TextInput label="Supervisor" type="text" :placeholder="'Nombre del supervisor'"
                                v-model="formData.supervisor" :error="router.page.props.errors.supervisor">
                            </TextInput> -->


                            <!--CAMPO TIPO DE PROYECTO (type)-->

                            <CustomInput label="Tipo de Proyecto" type="dropdown"
                                placeholder="Seleccione Alcance del Proyecto" optionLabel="name" v-model:input="typeSelect"
                                showClear :options="typeOptions" :errorMessage="$page.props.errors.type"
                                :invalid="$page.props.errors.type ? true : false" />

                            <!-- <div>
                                <p class="text-sm font-medium mb-0.5">Tipo de Proyecto</p>
                                <Dropdown class="h-10" :options="typeOptions" v-model="typeSelect" showClear
                                    optionLabel="name" placeholder="Seleccione Tipo de Proyecto" :pt="{
                                        root: '!border !w-full !border-gray-400 !shadow-sm !focus:outline-0 !rounded-md',
                                        input: '!text-sm',
                                        filterInput: '!text-gray-300',
                                        item: ({ context }) => ({
                                            class: context.selected ? 'bg-primary' : context.focused ? 'bg-blue-100' : undefined
                                        })
                                    }">
                                </Dropdown>
                            </div> -->

                            <!--CAMPO ESTADO DEL PROYECTO (status)-->
                            <CustomInput label="Estado del Proyecto" type="dropdown"
                                placeholder="Seleccione Alcance del Proyecto" optionLabel="name"
                                v-model:input="statusSelect" showClear :options="statusOptions"
                                :errorMessage="$page.props.errors.status"
                                :invalid="$page.props.errors.status ? true : false" />

                            <!-- <div>
                                <label class="text-sm font-medium">Estado del Proyecto</label>
                                <Dropdown class="h-10" :options="statusOptions" v-model="statusSelect" showClear
                                    optionLabel="name" placeholder="Seleccione Estado del Proyecto" :pt="{
                                        root: '!border !w-full !border-gray-400 !shadow-sm !focus:outline-0 !rounded-md',
                                        input: '!text-sm',
                                        filterInput: '!text-gray-300',
                                        item: ({ context }) => ({
                                            class: context.selected ? 'bg-primary' : context.focused ? 'bg-blue-100' : undefined
                                        })
                                    }">
                                </Dropdown>
                            </div> -->

                            <!--CAMPO COSTO DE VENTA (cost_sale)-->
                            <CustomInput label="Valor Venta" :disabled="true" type="number" mode="currency"
                                :currency="formData.cost_sale[1]" v-model:input="formData.cost_sale[0]"
                                :errorMessage="router.page.props.errors.supervisor"
                                :invalid="router.page.props.errors.supervisor ? true : false" />
                            <!-- <TextInput label="Valor Venta" type="number" :placeholder="'Escriba el valor de venta'"
                                v-model="formData.cost_sale" :error="router.page.props.errors.cost_sale">
                            </TextInput> -->

                            <!--CAMPO DESCRIPCIÓN (description)-->
                            <CustomInput type="textarea" v-model:input="formData.observations" class="col-span-2"
                                label="Descripción" :rowsTextarea="1" placeholder="Descripción del proyecto..."
                                :errorMessage="router.page.props.errors.observations"
                                :invalid="router.page.props.errors.observations ? true : false" />
                            <!-- <div class="col-span-2">
                                <label class="text-sm font-bold text-gray-900">Descripción</label>
                                <Textarea class="text-sm text-gray-500 placeholder:text-sm italic"
                                    placeholder="Descripción del proyecto..." v-model="formData.observations" rows="1"
                                    cols="143" autoResize :pt="{
                                        root: '!w-full'
                                    }" />
                            </div> -->
                        </section>
                    </tab-content>
                    <!--PLANEACIÓN DEL PROYECTO-->
                    <tab-content title="Planeación del Proyecto" icon="fa-solid fa-calendar-check"
                        :before-change="beforeChange">
                        <section class="flex border gap-6 border-gray-200 rounded-lg p-4">
                            <div class="grid grid-cols-2 gap-6 w-full">
                                <div class="space-y-4">
                                    <!--CAMPO FECHA INICIO-->
                                    <CustomInput type="date" label="Fecha De Inicio" v-model:input="formData.start_date"
                                        :errorMessage="$page.props.errors.start_date" :disabled="!contractSelect"
                                        :invalid="router.page.props.errors.start_date ? true : false" />
                                    <!--CAMPO FECHA FINALIZACIÓN-->
                                    <CustomInput type="date" label="Fecha de Finalización" v-model:input="formData.end_date"
                                        :errorMessage="$page.props.errors.end_date" :disabled="!contractSelect"
                                        :invalid="router.page.props.errors.end_date ? true : false" />

                                    <div class="gap-6 grid grid-cols-3">
                                        <!--CAMPO HORAS POR DÍA (hoursPerDay)-->
                                        <CustomInput label="Horas por Dia" type="number"
                                            v-model:input="formData.hoursPerDay" :min="0" :maxFractionDigits="2"
                                            :invalid="router.page.props.errors.hoursPerDay ? true : false"
                                            :errorMessage="router.page.props.errors.hoursPerDay" />

                                        <!--CAMPO DIAS POR SEMANA (daysPerWeek)-->
                                        <CustomInput label="Dias por Semana" type="number"
                                            v-model:input="formData.daysPerWeek" :min="0" :max="7"
                                            :invalid="router.page.props.errors.daysPerWeek ? true : false"
                                            :errorMessage="router.page.props.errors.daysPerWeek" />

                                        <!--CAMPO DIAS POR MES (daysPerMonth)-->
                                        <CustomInput label="Dias por Mes" v-model:input="formData.daysPerMonth"
                                            type="number" :min="0" :max="31"
                                            :invalid="router.page.props.errors.daysPerMonth ? true : false"
                                            :errorMessage="router.page.props.errors.daysPerMonth" />
                                    </div>
                                </div>
                                <!--CAMPO TURNO (shift)-->
                                <div class="">
                                    <label class="text-sm mb-0.5 font-bold text-gray-900">Seleccione el Turno</label>
                                    <Listbox :options="shiftOptions" optionLabel="name" filter :pt="{
                                        list: '!h-40 !p-1',
                                        item: ({ props, state, context }) => ({
                                            class: '!h-10 !p-0 !rounded-md hover:!bg-primary-light'
                                        }),
                                        filterInput: '!h-8',
                                        header: '!p-1'
                                    }">
                                        <template #option="slotProps">
                                            <div class=" h-full grid grid-cols-4 border px-1 rounded-md ">
                                                <span class="flex justify-between items-center overflow-hidden">
                                                    <p
                                                        class="text-overflow h-full overflow-y-auto flex text-sm font-bold items-center">
                                                        {{ slotProps.option.name }}</p>
                                                    <i class="fa-regular fa-clock"></i>
                                                </span>
                                                <div class="text-xs items-center text-center col-span-3 grid grid-cols-4">
                                                    <span>
                                                        <p class="font-bold">Hora Inicio</p>
                                                        <p>{{ formatDateTime24h(slotProps.option.startShift) }}
                                                        </p>
                                                    </span>
                                                    <span>
                                                        <p class="font-bold">Hora Fin</p>
                                                        <p>{{ formatDateTime24h(slotProps.option.endShift) }}</p>
                                                    </span>
                                                    <span>
                                                        <p class="font-bold">Descanso</p>
                                                        <p>{{ slotProps.option.timeBreak }}h</p>
                                                    </span>
                                                    <span>
                                                        <p class="font-bold">H. Laborales</p>
                                                        <p> {{ parseFloat(slotProps.option.hours).toFixed(1) }}h</p>
                                                    </span>
                                                </div>
                                            </div>
                                        </template>
                                        <template #empty>
                                            <Empty message="No hay turnos para mostrar"></Empty>
                                        </template>
                                        <template #emptyfilter>
                                            <Empty message="No se encuentran turnos"></Empty>
                                        </template>
                                    </Listbox>
                                    <!-- <div
                                        class="w-full h-52 overflow-y-auto custom-scroll border-2 border-gray-300 rounded-lg p-2 focus hover:border-blue-500">
                                        <div class="flex justify-center">
                                            <Loading v-if="showLoading" message="Cargando Turnos" class="mt-12" />
                                            <NoContentToShow v-if="showNoContent" subject="Turnos"
                                                class="!size-64 !mt-6 !h-[20vh]" />
                                        </div>
                                        <ul v-for="shift in shiftOptions" :key="shift.id">
                                            <div @click="selectShiftList(shift.id)"
                                                :class="shiftSelect == shift.id ? 'bg-blue-900 text-white' : 'hover:bg-gray-200'"
                                                class="flex justify-between items-center text-xs space-x-6 p-2 w-full cursor-pointer rounded-lg">
                                                <div>
                                                    <p class="text-xs font-bold">{{ shift.name }}:</p>
                                                </div>
                                                <div class="flex italic">
                                                    <ClockIcon class="size-4" />
                                                    <p><b>&nbsp Hora Inicio:</b> {{ formatDateTime24h(shift.startShift) }} -
                                                    </p>
                                                    <p>&nbsp <b>Hora Fin:</b> {{ formatDateTime24h(shift.endShift) }} - </p>
                                                    <p>&nbsp <b>Descanso:</b> {{ shift.timeBreak }}h - </p>
                                                    <p>&nbsp <b>H. Laborales:</b> {{ parseFloat(shift.hours).toFixed(1) }}
                                                    </p>
                                                </div>
                                            </div>
                                        </ul>
                                    </div> -->
                                </div>
                            </div>
                        </section>
                    </tab-content>
                    <!--BUQUES-->
                    <tab-content title="Buques" icon="fa-solid fa-ship" :before-change="beforeChange">
                        <div class="flex w-full gap-2 pb-4">
                            <input v-if="props.ships != 0" type="search" v-model="keyword" @input="searchShips()"
                                class="rounded-lg border-2 border-gray-200 w-full placeholder:italic"
                                placeholder="Filtrar Buques" />
                            <ToggleButton v-if="props.ships != 0" v-model="checked" onLabel="Seleccionar todo"
                                offLabel="Deseleccionar todo" onIcon="pi pi-check-square" offIcon="pi pi-stop"
                                aria-label="Do you confirm" @click="selectAllShips()" :pt="{
                                    root: ({ props }) => ({
                                        class:
                                            [
                                                '!w-56 !h-full !border-blue-800 !bg-transparent',
                                                props.modelValue ? '!bg-blue-900 !text-white' : '!bg-red-600 !text-white'
                                            ]
                                    }),
                                    icon: '!text-white'
                                }" />
                        </div>
                        <section
                            class="grid grid-cols-4 h-60 overflow-y-auto custom-scroll snap-y snap-mandatory sm:col-span-1 md:col-span-1 border gap-4 border-gray-200 rounded-lg p-4 mb-2">
                            <NoContentToShow v-if="props.ships == 0" subject="Buques" class="!mt-0" />
                            <ul v-for="ship in filteredShips" :key="ship.id" v-else
                                class="text-sm italic [&>li>p]:font-semibold snap-start">
                                <div @click="toggleSelectShip(ship.id)"
                                    :class="selectedShips.includes(ship.id) ? 'bg-blue-900 text-white' : 'hover:bg-blue-300'"
                                    class="flex space-x-4 shadow-md rounded-sm cursor-pointer transition-all duration-200 hover:scale-[1.01] ease-in-out hover:shadow-md">
                                    <div class="w-28">
                                        <img :src="ship.file" onerror="this.src='/images/generic-boat.png'"
                                            class="object-cover object-center w-full h-16 mr-1" />
                                    </div>
                                    <div>
                                        <li>
                                            <p><span class="font-semibold">Nombre:</span> {{ ship.name }}</p>
                                        </li>
                                        <li>
                                            <p><span class="font-semibold">Casco:</span> {{ ship.idHull }}</p>
                                        </li>
                                        <li>
                                            <p><span class="font-semibold">Clase:</span> {{ ship.type_ship.name }}</p>
                                        </li>
                                    </div>
                                </div>
                            </ul>
                        </section>
                    </tab-content>
                    <tab-content title="Hitos" icon="fa-solid fa-list-check">
                        <div class="w-full overflow-y-auto">
                            <CustomDataTable :rowsDefault="5" :data="milestones" :columnas="columnas" :actions="actions"
                                @edit="showModal" :filter="false" :showHeader="false" :showAdd="true" @addClic="showModal"
                                @delete="delMilestone" />
                        </div>
                    </tab-content>
                </form-wizard>
            </section>
        </main>
    </AppLayout>
    <CustomModal v-model:visible="openDialogHito" width="30rem" :titulo="formMilestone.id ? 'Editar hito' : 'Agregar Hito'"
        icon="fa-solid fa-list-check">
        <template #body>
            <section class="relative space-y-6 p-2">
                <CustomInput label="Título del Hito" id="category" type="text" v-model:input="formMilestone.title"
                    placeholder="Escriba título del hito" />
                <CustomInput label="Fecha de Hito" id="category" type="date" v-model:input="formMilestone.end_date"
                    placeholder="Escriba fecha de hito" />
                <CustomInput label="Valor del Hito" id="value" type="number" mode="currency"
                    v-model:input="formMilestone.value" placeholder="Escriba el valor del hito" />
                <CustomInput label="Seleccione tipo de Hito" id="category" type="dropdown"
                    v-model:input="formMilestone.type" placeholder="Escriba el tipo de hito"
                    :options="['Pago Anticipado', 'Avance Obra']" />

                <div class="flex space-x-4 items-center">
                    <label for="" class="mb-0.5 font-bold">Avance del HITO: </label>
                    <ToggleButton v-model="formMilestone.advance" onLabel="100%" offLabel="0%" :pt="{
                        root: ({ props }) => ({
                            class:
                                [
                                    '!p-1 !text-sm',
                                    props.modelValue ? '!bg-teal-700 !text-white' : '!bg-danger !text-white'
                                ]
                        })
                    }" />
                </div>
            </section>
        </template>
        <template #footer>
            <Button severity="success" outlined :label="formMilestone.id ? 'Actualizar' : 'Agregar'"
                icon="fa-solid fa-floppy-disk" @click="save()" />
            <Button severity="danger" outlined label="Cancelar" icon="fa-regular fa-circle-xmark"
                @click="openDialogHito = false" />
        </template>
    </CustomModal>

    <CustomModal v-model:visible="modalProgress" titulo="Avance del proyecto" width="30vw" icon="fa-solid fa-bars-progress">
        <template #body>
            <CustomInput label="Semana" type="week" v-model:input="avance.week" />
            <CustomInput label="Porcentaje de avance" :maxFractionDigits="2" v-model:input="avance.real_progress"
                type="number" :max="100" :min="0" suffix="%" />
            <CustomInput label="CPI" v-model:input="avance.CPI" :errorMessage="avance.errors.CPI"
                :invalid="avance.errors.CPI ? true : false" />
            <CustomInput label="SPI" v-model:input="avance.SPI" />
        </template>
        <template #footer>
            <Button label="Guardar" severity="success" :loading="avance.processing" @click="guardarAvance()" />
            <Button label="Cerrar" severity="danger" @click="modalProgress = false" />
        </template>
    </CustomModal>

    <CustomModal v-model:visible="modalWeekTask" titulo="Tareas semanales" width="70vw" icon="fa-solid fa-list-check">
        <template #body>
            <div class="grid grid-cols-4 gap-2">
                <span class="col-span-3 grid gap-2 grid-cols-2 h-min max-h-64 overflow-y-auto justify-center ">
                    <div class="flex items-center p-1 justify-between border rounded-md" v-for="task in weekTasks">
                        <p>{{ task.task }}</p>
                        <div class="min-w-20 flex items-center">
                            <Button rounded text severity="danger" icon="fa-solid fa-trash-can" />
                            <Button rounded text severity="warning" icon="fa-solid fa-pen" />
                        </div>
                    </div>
                </span>
                <span class="flex flex-col justify-end">
                    <CustomInput label="Semana" type="week" v-model:input="weekTask.week" />
                    <CustomInput label="Tarea" type="textarea" v-model:input="weekTask.task" />
                    <Button label="Añadir" severity="success" :loading="weekTask.processing" @click="saveweekTask()" />
                </span>
            </div>
        </template>
    </CustomModal>

    <Toast position="bottom-center" :pt="{
        root: '!h-10 !w-64',
        container: {
            class: '!bg-primary !h-10 !rounded-lg'
        },
        content: '!h-10 !p-0 !flex !items-center !text-center !text-white ',
        buttonContainer: '!hidden',
        icon: '!hidden',
        detail: '!hidden'
    }" />
</template>
