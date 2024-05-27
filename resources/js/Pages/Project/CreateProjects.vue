<script setup>
const { confirmDelete } = useSweetalert()
import 'vue3-form-wizard/dist/style.css'
import { FormWizard, TabContent } from 'vue3-form-wizard'
import { ref, onMounted } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import { useSweetalert } from '@/composable/sweetAlert'
import AppLayout from '@/Layouts/AppLayout.vue'
import CustomDataTable from '@/Components/CustomDataTable.vue'
import CustomInput from '@/Components/CustomInput.vue'
import CustomModal from '@/Components/CustomModal.vue'
import CustomShiftSelector from '@/Components/CustomShiftSelector.vue';
import CustomUpload from '@/Components/CustomUpload.vue';
import Empty from '@/Components/Empty.vue'
import Listbox from 'primevue/listbox'
import ToggleButton from 'primevue/togglebutton'
import { useToast } from "primevue/usetoast";
const toast = useToast()

const props = defineProps({
    project: Object,
    contracts: Array,
    gerentes: Array,
    weekTasks: Array,
    milestones: {
        default: []
    }
})

//#region Referencias (v-model)
const loading = ref(false)
//#endregion

//#region CustomDataTable hitos
const columnas = [
    { field: 'title', header: 'Nombre', sortable: true, filter: true, },
    { field: 'value', header: 'Valor', filter: true, sortable: true, type: 'currency', class: 'w-64' },
    { field: 'type', header: 'Tipo de Hito', filter: true, sortable: true },
    { field: 'end_date', header: 'Fecha de terminación', filter: true, sortable: true },
    { field: 'advance', header: 'Avance', type: 'number', filter: true, sortable: true },
]

const actions = [
    { event: 'edit', severity: 'warning', icon: 'fa-solid fa-pencil', text: true, outlined: false, rounded: false },
    { event: 'delete', severity: 'danger', icon: 'fa-solid fa-trash-can', text: true, outlined: false, rounded: false },
]
//#endregion

//#region ENUMS
//Tipo de Proyecto
const typeOptions = [
    'PROYECTO DE VENTA (ARTEFACTO NAVAL)',
    'PROYECTO DE VENTA (SERV. INDUSTRIA)',
    'PROYECTO DE VENTA (SUMINISTRO/SERVICIO)',
    'PROYECTO DE INVERSION INTERNA',
    'PROYECTO DE INVERSIÓN (ARTEFACTO NAVAL)'
]

//Estado de Proyecto
const statusOptions = [
    'REPARACIÓN Y MANTENIMIENTO',
    'CONSTRUCCIÓN',
    'DISEÑO',
    'GARANTIA',
    'SERVICIO POSTVENTA'
]

//Alcance de Proyecto
const scopeOptions = [
    "ADQUISICIÓN Y ENTREGA",
    "CO DESARROLLO DISEÑO Y CONSTRUCCIÓN",
    "CO PRODUCCIÓN",
    "CONSTRUCCIÓN",
    "DISEÑO BUQUE",
    "DISEÑO Y CONSTRUCCIÓN",
    "SERVICIOS INDUSTRIALES"
]
//#endregion

//#region formData
const formData = ref({
    id: props.project?.id ?? null,
    name: props.project?.name ?? null,
    contract_id: parseInt(props.project?.contract_id) ?? null,
    authorization_id: parseInt(props.project?.authorization_id) ?? null,
    quote_id: parseInt(props.project?.quote_id) ?? null,
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
    shift: parseInt(props.project?.shift) ?? null,
    ships: props.project?.ships ?? []
})
//#endregion

const wizard = ref()
onMounted(() => {
    // getAuthorizations()
    // getContracts()
    // getMilestones()
    // getProjectShips()
    // getQuotes()
    // getShift()
    getShips()
    // getWeekTasks()
    contractsOptions.value = props.contracts
    if (props.project) {
        wizard.value.activateAll()
    }
})

//#region Requests
//Turnos
const shipsOptions = ref([])

const getShips = async () => {
    await axios.get(route('ships.index'))
        .then(response => {
            shipsOptions.value = response.data.ships
        })
}

//Clases (typeShips)
const projectShipsOptions = ref([])

const getProjectShips = async () => {
    await axios.get(route('projectShips.index'))
        .then(response => {
            projectShipsOptions.value = response.data.ships
        })
}

//Hitos (Milestones)
const milestonesOptions = ref([])

const getMilestones = async () => {
    await axios.get(route('milestones.index'))
        .then(response => {
            milestonesOptions.value = response.data[0]
        })
}

//Turnos
const shiftOptions = ref([])

const getShift = async () => {
    await axios.get(route('shift.index'))
        .then(response => {
            shiftOptions.value = response.data[0]
        })
}

//Contratos
const contractsOptions = ref([])

const getContracts = async () => {
    await axios.get(route('contracts.index'))
        .then(response => {
            contractsOptions.value = response.data[0]
        })
}

//Estimaciones (Quotes)
const quotesOptions = ref([])

const getQuotes = async () => {
    await axios.get(route('quotes.index'))
        .then(response => {
            quotesOptions.value = response.data[0]
        })
}

//Autorizaciones (Quotes)
const authorizationSelect = ref()
const authorizationsOptions = ref([])

const getAuthorizations = async () => {
    await axios.get(route('authorizations.index'))
        .then(response => {
            authorizationsOptions.value = response.data[0]
        })
}

//Tareas Semanales (weekTasks)
const weekTasksOptions = ref([])

const getWeekTasks = async () => {
    await axios.get(route('week_tasks.index'))
        .then(response => {
            weekTasksOptions.value = response.data[0]
        })
}
//#endregion

const errors = ref({})
const beforeChange = async () => {
    if (formData.value.name != null && formData.value.SAP_code != null) {
        let switchTabsStates = false
        loading.value = true
        try {
            if (!formData.value.id) {
                await axios.post(route('projects.store'), formData.value)
                    .then((res) => {
                        formData.value.id = res.data.project_id
                        toast.add({ severity: 'success', group: 'customToast', text: 'Guardado', life: 2000 });
                        switchTabsStates = true
                    })
            } else {
                await axios.put(route('projects.update', formData.value.id), formData.value)
                    .then((res) => {
                        toast.add({ severity: 'success', group: 'customToast', text: 'Actualizado', life: 2000 });
                        switchTabsStates = true
                    })
            }
            loading.value = false
            return switchTabsStates
        } catch (error) {
            errors.value = error.response.data.errors
            toast.add({ severity: 'error', group: 'customToast', text: errors.value.message, life: 2000 });
        }
    } else {
        // errors.value.name = 'El campo nombre es obligatorio'
        toast.add({ severity: 'error', group: 'customToast', text: 'Rellene todos los campos', life: 2000 });
    }
}

const finish = (msg) => {
    toast.add({ severity: 'success', group: 'customToast', text: msg, life: 2000 });
    router.get(route('projects.index'))
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
                toast.add({ severity: 'success', group: 'customToast', text: 'Hito actualizado', life: 2000 });
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
                toast.add({ severity: 'success', group: 'customToast', text: 'Hito guardado', life: 2000 });
                openDialogHito.value = false;
            },
            onError: (e) => {
                console.log(e)
            }
        })
    }
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
            console.log(e)
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
    week: null,
    project_id: null

})

const saveweekTask = () => {
    weekTask.project_id = props.project.id
    loading.value = true
    if (weekTask.id) {
        weekTask.put(route('weektasks.update', weekTask.id), {
            onSuccess: () => {
                weekTask.reset()
                toast.add({ severity: 'success', group: 'customToast', text: 'Actividad Guardada', life: 2000 });
                openDialogHito.value = false;
            },
            onError: (e) => {
                toast.add({ severity: 'error', group: 'customToast', text: 'Hubo un error, reintente', life: 2000 });
                console.log(e)
            }
        })
    } else {
        weekTask.post(route('weektasks.store'), {
            onSuccess: () => {
                weekTask.reset()
                toast.add({ summary: 'Hito Guardado', life: 2000 });
                openDialogHito.value = false;
            },
            onError: (e) => {
                toast.add({ severity: 'error', group: 'customToast', text: 'Hubo un error, reintente', life: 2000 });
                console.log(e)
            }
        })
    }
}

const deleteweekTask = (id) => {
    router.delete(route('weektasks.destroy', id), {
        onSuccess: () => {
            weekTask.reset()
            toast.add({ severity: 'success', group: 'customToast', text: 'Atividad Eliminada', life: 2000 });
            openDialogHito.value = false;
        },
        onError: (e) => {
            toast.add({ severity: 'error', group: 'customToast', text: 'Hubo un error, reintente', life: 2000 });
            console.log(e)
        }
    })
}

const active = ref(0)

const url = [
    {
        ruta: 'projects.index',
        label: 'Proyectos'
    },
    {
        ruta: 'projects.create',
        label: props.project ? 'Editar' : 'Crear' + ' Proyecto',
        active: true
    }
]
</script>

<template>
    <AppLayout :href="url">
        <div class="h-full w-full overflow-y-auto flex flex-col ">
            <div class="flex justify-between items-center px-2 h-[8vh]">
                <span class="w-full flex space-x-1">
                    <Button v-tooltip="'Volver a proyectos'" text raised rounded icon="fa-solid fa-arrow-left"
                        @click="finish('Saliendo')" />
                    <h2 class="text-lg font-semibold w-full text-primary lg:text-2xl">
                        {{ project ? project.name : 'Nuevo proyecto' }}
                    </h2>
                </span>
                <div v-if="project && $page.props.auth.user.gerencia == 'GECON'"
                    class="space-x-6 justify-end flex w-full">
                    <Button icon="fa-solid fa-list-check" severity="help" v-tooltip.left="'Tareas de la semana'"
                        @click="modalWeekTask = true" />
                    <Button icon="fa-solid fa-gauge-high" severity="secondary" v-tooltip.left="'Avance del proyecto'"
                        @click="modalProgress = true" />

                    <CustomUpload mode="advanced" titleModal="Subir Estructura de SAP"
                        icon-button="fa-solid fa-chart-bar" tooltip="Subir Estructura" accept=".xlsx,.xls"
                        :url="route('upload.estructure', project.id)" />

                    <CustomUpload mode="advanced" titleModal="Subir Curva S" icon-button="fa-solid fa-chart-line"
                        tooltip="Subir Curva S" accept=".xlsx,.xls"
                        :url="route('progressProjectWeek.upload', project.id)" severity="info" />

                    <CustomUpload mode="advanced" titleModal="Subir Presupuesto del proyecto"
                        icon-button="fa-solid fa-hand-holding-dollar" tooltip="Subir Presupuesto" accept=".xlsx,.xls"
                        :url="route('upload.budget', project.id)" severity="success" />

                    <CustomUpload mode="advanced" titleModal="Subir Costos Ejecutados"
                        icon-button="fa-solid fa-money-bill-transfer" tooltip="Subir ejecutado" accept=".xlsx,.xls"
                        :url="route('upload.execute', project.id)" severity="info" />
                </div>
            </div>
            <div class="p-2">
                <!-- AQUÍ VA EL CONTENIDO DEL FORMULARIO-->
                <form-wizard @onComplete="finish('Guardado y saliendo')" stepSize="md" class="flex flex-col h-[75vh]"
                    color="#2E3092" nextButtonText="Siguiente" backButtonText="Regresar"
                    finishButtonText="Guardar y salir" ref="wizard">
                    <!--INFORMACIÓN CONTRACTUAL-->
                    <tab-content
                        :title="$page.props.auth.user.gerencia == 'GECON' ? 'Información Contractual' : 'INFORMACION DEL PROYECTO'"
                        class="h-[45vh] overflow-y-auto" icon="fa-solid fa-file-signature" :beforeChange="beforeChange">
                        <div class="border gap-4 border-gray-200 rounded-lg p-4 md:grid md:grid-cols-3">
                            <!--CAMPO NOMBRE DEL PROYECTO (name)-->
                            <CustomInput label="Nombre del Proyecto" placeholder="Escriba el nombre del proyecto"
                                v-model:input="formData.name" :errorMessage="errors.name"
                                :invalid="errors.name ? true : false" />
                            <!--CAMPO CÓDIGO DE SAP (SAP_code)-->
                            <CustomInput label="Código SAP" placeholder="Escriba el código de SAP"
                                v-model:input="formData.SAP_code" :errorMessage="$page.props.errors.SAP_code"
                                :invalid="$page.props.errors.SAP_code ? true : false" />
                                <CustomInput type="date" label="Fecha De Inicio" v-model:input="formData.start_date"
                                    :disabled="!formData.contract_id" />
                            <!--CAMPO ALCANCE DEL PROYECTO (scope)-->
                            <CustomInput label="Alcance del Proyecto" type="dropdown"
                                placeholder="Seleccione Alcance del Proyecto" v-model:input="formData.scope" showClear
                                :options="scopeOptions" :errorMessage="$page.props.errors.scope"
                                :invalid="$page.props.errors.scope ? true : false"
                                v-if="$page.props.auth.user.gerencia == 'GECON'" />
                           
                            <CustomInput label="Contrato" type="dropdown" placeholder="Seleccione Alcance del Proyecto"
                                optionValue="id" optionLabel="contract_id" v-model:input="formData.contract_id"
                                showClear :options="contractsOptions"
                                v-if="$page.props.auth.user.gerencia == 'GECON'" />

                            <CustomInput label="Autorizaciones" type="dropdown" placeholder="Seleccione Autorización"
                                optionLabel="name" v-model:input="authorizationSelect" showClear
                                :options="authorizationsOptions" :errorMessage="$page.props.errors.authorization_id"
                                :invalid="$page.props.errors.authorization_id ? true : false"
                                v-if="$page.props.auth.user.gerencia == 'GECON'" />

                            <CustomInput label="Estimaciones" type="dropdown" placeholder="Seleccione Estimación"
                                optionLabel="consecutive" v-model:input="formData.quote_id" optionValue="id" showClear
                                :options="Object.values(quotesOptions)" :errorMessage="$page.props.errors.quote_id"
                                :invalid="$page.props.errors.quote_id ? true : false"
                                v-if="$page.props.auth.user.gerencia == 'GECON'" />
                        </div>
                    </tab-content>
                    <!--DATOS DEL PROYECTO-->
                    <tab-content title="Datos del Proyecto" icon="fa-solid fa-diagram-project"
                        v-if="$page.props.auth.user.gerencia == 'GECON'" class="h-[45vh] overflow-y-auto"
                        :before-change="beforeChange">
                        <!--CAMPO SUPERVISOR (supervisor)-->
                        <div class="sm:grid sm:grid-cols-2 border gap-4 rounded-lg p-4">

                            <CustomInput label="Gerente de Proyecto" placeholder="Nombre del Gerente de Proyecto"
                                optionLabel="name" optionValue="name" :options="Object.values(gerentes)" type="dropdown"
                                v-model:input="formData.supervisor" @change="console.log($event)" />

                            <!--CAMPO TIPO DE PROYECTO (type)-->

                            <CustomInput label="Tipo de Proyecto" type="dropdown"
                                placeholder="Seleccione Alcance del Proyecto" v-model:input="formData.type" showClear
                                :options="typeOptions" />

                            <!--CAMPO ESTADO DEL PROYECTO (status)-->
                            <CustomInput label="Estado del Proyecto" type="dropdown"
                                placeholder="Seleccione Alcance del Proyecto" v-model:input="formData.status" showClear
                                :options="statusOptions" />

                            <!--CAMPO COSTO DE VENTA (cost_sale)-->
                            <CustomInput label="Valor Venta" type="number" mode="currency"
                                :currency="formData.cost_sale[1] == null ? 'COP' : formData.cost_sale[1]"
                                v-model:input="formData.cost_sale[0]" />

                            <!--CAMPO DESCRIPCIÓN (description)-->
                            <CustomInput type="textarea" v-model:input="formData.observations" class="col-span-2"
                                label="Descripción" :rowsTextarea="1" placeholder="Descripción del proyecto..." />
                        </div>
                    </tab-content>
                    <!--PLANEACIÓN DEL PROYECTO-->
                    <tab-content title="Planeación del Proyecto" v-if="$page.props.auth.user.gerencia == 'GECON'"
                        icon="fa-solid fa-calendar-check" :before-change="beforeChange"
                        class="h-[45vh] overflow-y-auto">
                        <div class="sm:grid sm:grid-cols-2 gap-6 w-full border p-4 rounded-lg ">
                            <div class="flex flex-col gap-4">
                                <!--CAMPO FECHA INICIO-->
                                <CustomInput type="date" label="Fecha De Inicio" v-model:input="formData.start_date"
                                    :disabled="!formData.contract_id" />
                                <!--CAMPO FECHA FINALIZACIÓN-->
                                <CustomInput type="date" label="Fecha de Finalización" v-model:input="formData.end_date"
                                    :disabled="!formData.contract_id" />
                                <div class="gap-6 grid grid-cols-3">
                                    <!--CAMPO HORAS POR DÍA (hoursPerDay)-->
                                    <CustomInput label="Horas por Dia" type="number"
                                        v-model:input="formData.hoursPerDay" :min="0" :maxFractionDigits="2" />

                                    <!--CAMPO DIAS POR SEMANA (daysPerWeek)-->
                                    <CustomInput label="Dias por Semana" type="number"
                                        v-model:input="formData.daysPerWeek" :min="0" :max="7" />

                                    <!--CAMPO DIAS POR MES (daysPerMonth)-->
                                    <CustomInput label="Dias por Mes" v-model:input="formData.daysPerMonth"
                                        type="number" :min="0" :max="31" />
                                </div>
                            </div>
                            <!--CAMPO TURNO (shift)-->
                            <div class="">
                                <!-- <label class="text-sm mb-0.5 font-bold text-gray-900">Seleccione el Turno</label>
                                <Listbox :options="shiftOptions" optionValue="id" v-model="formData.shift"
                                    optionLabel="name" filter :pt="{
                                        list: '!h-40 !p-1',
                                        item: '!h-10 !p-0 !rounded-md hover:!bg-primary-light',
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
</Listbox> -->
                                <CustomShiftSelector v-model:shift="formData.shift" optionValue="id" />
                            </div>
                        </div>
                    </tab-content>
                    <!--BUQUES-->
                    <tab-content title="Buques" icon="fa-solid fa-ship" v-if="$page.props.auth.user.gerencia == 'GECON'"
                        :before-change="beforeChange" class="h-[45vh] flex gap-2">
                        <span class="h-ful flex flex-col w-1/2">
                            <p class="font-bold text-lg">Buques agregados</p>
                            <Listbox :options="projectShipsOptions" optionValue="id"
                                :filterFields="['ship.name', 'ship.idHull']"
                                filterPlaceholder="Filtrar Buques agregados" multiple filter optionLabel="name" :pt="{
        list: projectShipsOptions.length > 0 ? 'sm:!grid sm:!grid-cols-2 !gap-1 !p-1 !max-h-[34vh] h-[34vh]' : '!max-h-[34vh] h-[34vh]',
        header: '!p-1',
        filterInput: '!h-8',
        item: '!h-min !rounded-lg'
    }">

                                <template #option="slotProps">
                                    <div class="flex items-center justify-between">
                                        <ol class="h-full flex flex-col">
                                            <li>
                                                <p class="">
                                                    {{ slotProps.option.ship.name }}
                                                </p>
                                            </li>
                                            <li>
                                                <p><span class="font-semibold">Casco:</span>
                                                    {{ slotProps.option.ship.idHull }}
                                                </p>
                                            </li>
                                        </ol>
                                        <Button v-tooltip="'Eliminar el buque del proyecto'"
                                            icon="fa-solid fa-trash-can" text rounded severity="danger"
                                            @click="confirmDelete(slotProps.option.id, 'Buque', 'projectships')" />
                                    </div>
                                </template>

                                <template #empty>
                                    <Empty message="Aun no se agregan buques" />
                                </template>
                            </Listbox>
                        </span>
                        <span class="flex flex-col w-1/2">
                            <p class="font-bold text-lg">Buques para agregar</p>
                            <Listbox :options="shipsOptions" v-model="formData.ships" optionValue="id"
                                :filterFields="['name', 'idHull']" filterPlaceholder="Filtrar Buques sin agregar"
                                multiple filter optionLabel="name" :pt="{
        list: shipsOptions.length > 0 ? 'sm:!grid sm:!grid-cols-2 !gap-1 !p-1 !max-h-[34vh] h-[34vh]' : '!max-h-[34vh] h-[34vh]',
        header: '!p-1',
        filterInput: '!h-8',
        item: '!h-min !rounded-lg'
    }">

                                <template #option="slotProps">
                                    <div class="flex">
                                        <div class="h-full flex flex-col">
                                            <li>
                                                <p class="truncate">
                                                    {{ slotProps.option.name }}
                                                </p>
                                            </li>
                                            <li>
                                                <p><span class="font-semibold">Casco:</span>
                                                    {{ slotProps.option.idHull }}
                                                </p>
                                            </li>
                                        </div>
                                    </div>
                                </template>

                                <template #empty>
                                    <Empty message="No hay buques para mostrar" />
                                </template>
                            </Listbox>
                        </span>
                    </tab-content>
                    <tab-content title="Hitos" v-if="$page.props.auth.user.gerencia == 'GECON'"
                        icon="fa-solid fa-list-check" class=" h-[45vh] w-full border rounded-lg p-1 overflow-y-auto">
                        <CustomDataTable :rowsDefault="5" :data="milestones" :columnas="columnas" :actions="actions"
                            @edit="showModal" :filter="false" :showHeader="false" :showAdd="true" @addClick="showModal"
                            @delete="delMilestone" />
                    </tab-content>

                    <template #prev>
                        <Button label="Regresar" :loading="loading" raised icon="fa-solid fa-arrow-left" />
                    </template>

                    <template #next>
                        <Button label="Siguiente" :loading="loading" raised iconPos="right"
                            icon="fa-solid fa-arrow-right" />
                    </template>

                    <template #finish>
                        <Button label="Guardar y salir" :loading="loading" raised icon="fa-solid fa-floppy-disk" />
                    </template>
                </form-wizard>

            </div>
        </div>
    </AppLayout>

    <CustomModal v-model:visible="openDialogHito" width="30rem"
        :titulo="formMilestone.id ? 'Editar hito' : 'Agregar Hito'" icon="fa-solid fa-list-check">

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

    <CustomModal v-model:visible="modalProgress" titulo="Avance del proyecto" width="30vw"
        icon="fa-solid fa-bars-progress">

        <template #body>
            <CustomInput label="Semana" type="week" v-model:input="avance.week" />
            <CustomInput label="Porcentaje de avance" :maxFractionDigits="4" v-model:input="avance.real_progress"
                type="number" :max="100" :min="0" suffix="%" />
            <CustomInput label="CPI" v-model:input="avance.CPI" :errorMessage="avance.errors.CPI"
                :invalid="avance.errors.CPI ? true : false" type="number" :maxFractionDigits="2" />
            <CustomInput label="SPI" v-model:input="avance.SPI" type="number" :maxFractionDigits="2" />
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
                    <div class="flex items-center p-1 justify-between border rounded-md" v-for="weekTask in weekTasks">
                        <p>{{ weekTask.task }}</p>
                        <div class="flex items-center">
                            <Button rounded text severity="danger" icon="fa-solid fa-trash-can"
                                @click="deleteweekTask(weekTask.id)" />
                            <!-- <Button rounded text severity="warning" icon="fa-solid fa-pen" /> -->
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
</template>
