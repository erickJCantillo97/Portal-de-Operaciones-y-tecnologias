<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, onMounted, computed } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import { FilterMatchMode, FilterOperator } from 'primevue/api'
import { ClockIcon } from '@heroicons/vue/24/outline'
import axios from 'axios'
import Dropdown from 'primevue/dropdown'
import TextInput from '../../Components/TextInput.vue'
import Textarea from 'primevue/textarea'
import ToggleButton from 'primevue/togglebutton'
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
const toast = useToast();


const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
})

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
const checked = ref(false)
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

//#regio CustomDataTable
const columnas = [
    { field: 'title', header: 'Nombre', sortable: true, filter: true, },
    { field: 'value', header: 'Valor', filter: true, sortable: true, type: 'currency', class: 'w-64' },
    { field: 'type', header: 'Tipo de Hito', filter: true, sortable: true },
    { field: 'end_date', header: 'Fecha de terminación', filter: true, sortable: true },
]


const actions = [
    { event: 'edit', severity: 'warning', icon: 'fa-solid fa-pencil', text: true, outlined: false, rounded: false },]

const openDialog = ref(false)

const showModal = () => {
    openDialog.value = true
}
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
    contract_id: props.project?.contract_id ?? '0',
    authorization_id: props.project?.authorization_id ?? '0',
    quote_id: props.project?.quote_id ?? '0',
    type: props.project?.type ?? null, //ENUMS
    SAP_code: props.project?.SAP_code ?? null,
    status: props.project?.status ?? null, //ENUMS
    scope: props.project?.scope ?? null, //ENUMS
    supervisor: props.project?.supervisor ?? '',
    cost_sale: props.project?.cost_sale ?? '0',
    description: props.project?.description ?? '',
    start_date: props.project?.start_date ?? '',
    end_date: props.project?.end_date ?? '',
    hoursPerDay: props.project?.hoursPerDay ?? '8.5',
    daysPerWeek: props.project?.daysPerWeek ?? '5',
    daysPerMonth: props.project?.daysPerMonth ?? '20',
    shift: props.project != null ? props.project.shift : '0'
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
const projectIdRef = ref(null)

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

const formMilestone = useForm({
    title: '',
    value: '',
    end_date: new Date(),
    type: '',
    project_id: 12,
    invoiced: false,
    advance: 0
});

const save = () => {
    formMilestone.project_id = projectIdRef.value;
    formMilestone.post(route('milestones.store'), {
        onSuccess: () => {
            toast.add({ summary: 'Hito Guardado', life: 2000 });
            openDialog.value = false;
        }
    })
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
</script>
<style scoped>
.form-wizard {
    padding-left: 20%;
}
</style>
<template>
    <AppLayout>

        <Head title="Agregar Proyecto" />
        <main class="px-8 min-h-full overflow-y-scroll custom-scroll">
            <header class="w-full">
                <h2 class="text-lg font-semibold mb-4 text-primary text-center lg:text-2xl">
                    Agregar Proyecto
                </h2>
            </header>
            <div v-if="props.project" class="space-x-4 justify-end flex w-full my-4">

                <CustomUpload mode="advanced" titleModal="Subir Estructura de SAP" :multiple="true"
                    icon-button="fa-solid fa-chart-bar" labelButton="Subir Estructura" accept=".xlsx,.xls" url="prueba" />

                <CustomUpload mode="advanced" :multiple="true" titleModal="Subir Presupuesto del proyecto"
                    icon-button="fa-solid fa-hand-holding-dollar" labelButton="Subir Presupuesto" accept=".xlsx,.xls"
                    url="prueba" severity="success" />

                <CustomUpload mode="advanced" :multiple="true" titleModal="Subir el avance planeado del proyecto"
                    labelButton="Subir Curva S" accept=".xlsx,.xls" url="prueba" severity="info" />

                <CustomUpload mode="advanced" :multiple="true" titleModal="Subir Costos ejecutados por el proyecto"
                    icon-button="fa-solid fa-money-bill-trend-up" labelButton="Subir Costos Ejecutados" accept=".xlsx,.xls"
                    url="prueba" severity="danger" />
            </div>
            <section class="grid grid-cols-1 p-2">
                <!-- AQUÍ VA EL CONTENIDO DEL FORMULARIO-->
                <form-wizard @on-complete="submit()" stepSize="md" color="#2E3092" nextButtonText="Siguiente"
                    backButtonText="Regresar" finishButtonText="Guardar">
                    <!--INFORMACIÓN CONTRACTUAL-->
                    <tab-content title="Información Contractual" icon="fa-solid fa-file-signature"
                        :before-change="beforeChange">
                        <section class="border gap-4 border-gray-200 rounded-lg p-4 grid grid-cols-2">
                            <!--CAMPO NOMBRE DEL PROYECTO (name)-->
                            <TextInput type="text" label="Nombre del Proyecto" placeholder="Escriba el nombre del proyecto"
                                v-model="formData.name" :error="$page.props.errors.name">
                            </TextInput>

                            <!--CAMPO CÓDIGO DE SAP (SAP_code)-->
                            <TextInput type="text" label="Código SAP" placeholder="Escriba el código de SAP"
                                v-model="formData.SAP_code" :error="$page.props.errors.SAP_code">
                            </TextInput>

                            <!--CAMPO ALCANCE DEL PROYECTO (scope)-->
                            <div>
                                <label class="text-sm font-medium">Alcance del Proyecto</label>
                                <Dropdown class="h-10" :options="scopeOptions" v-model="scopeSelect" showClear
                                    optionLabel="name" placeholder="Seleccione Alcance del Proyecto" :pt="{
                                        root: '!border !w-full !border-gray-400 !shadow-sm !focus:outline-0 !rounded-md',
                                        input: '!text-sm',
                                        filterInput: '!text-gray-300',
                                        item: ({ context }) => ({
                                            class: context.selected ? 'bg-primary' : context.focused ? 'bg-blue-100' : undefined
                                        })
                                    }">
                                </Dropdown>
                            </div>

                            <!--CAMPO CONTRATO (contract)-->
                            <div>
                                <label class="text-sm font-medium">Contrato</label>
                                <Dropdown class="h-10" :options="contracts" v-model="contractSelect" showClear
                                    optionLabel="contract_id" placeholder="Seleccione Contrato" :pt="{
                                        root: '!border !w-full !border-gray-400 !shadow-sm !focus:outline-0 !rounded-md',
                                        input: '!text-sm',
                                        filterInput: '!text-gray-300',
                                        item: ({ context }) => ({
                                            class: context.selected ? 'bg-primary' : context.focused ? 'bg-blue-100' : undefined
                                        })
                                    }">
                                </Dropdown>
                            </div>

                            <!--CAMPO AUTORIZACIONES (authorization)-->
                            <div>
                                <label class="text-sm font-medium">Autorizaciones</label>
                                <Dropdown class="h-10" :options="authorizations" v-model="authorizationSelect" showClear
                                    optionLabel="name" placeholder="Seleccione Autorización" :pt="{
                                        root: '!border !w-full !border-gray-400 !shadow-sm !focus:outline-0 !rounded-md',
                                        input: '!text-sm',
                                        filterInput: '!text-gray-300',
                                        item: ({ context }) => ({
                                            class: context.selected ? 'bg-primary' : context.focused ? 'bg-blue-100' : undefined
                                        })
                                    }">
                                </Dropdown>
                            </div>

                            <!--CAMPO ESTIMACIÓN (quote)-->
                            <div>
                                <label class="text-sm font-medium">Estimaciones</label>
                                <Dropdown class="h-10" :options="quotes" v-model="quoteSelect" showClear optionLabel="name"
                                    placeholder="Seleccione Estimación" :pt="{
                                        root: '!border !w-full !border-gray-400 !shadow-sm !focus:outline-0 !rounded-md',
                                        input: '!text-sm',
                                        filterInput: '!text-gray-300',
                                        item: ({ context }) => ({
                                            class: context.selected ? 'bg-primary' : context.focused ? 'bg-blue-100' : undefined
                                        })
                                    }">
                                </Dropdown>
                            </div>
                        </section>
                    </tab-content>

                    <!--DATOS DEL PROYECTO-->
                    <tab-content title="Datos del Proyecto" icon="fa-solid fa-diagram-project"
                        :before-change="beforeChange">
                        <section class="grid grid-cols-2 border gap-4 border-gray-200 rounded-lg p-4">
                            <!--CAMPO SUPERVISOR (supervisor)-->
                            <TextInput label="Supervisor" type="text" :placeholder="'Nombre del supervisor'"
                                v-model="formData.supervisor" :error="router.page.props.errors.supervisor">
                            </TextInput>

                            <!--CAMPO TIPO DE PROYECTO (type)-->
                            <div>
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
                            </div>

                            <!--CAMPO ESTADO DEL PROYECTO (state)-->
                            <div>
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
                            </div>

                            <!--CAMPO COSTO DE VENTA (cost_sale)-->
                            <TextInput label="Costo de Venta" type="number" :placeholder="'Escriba el costo de venta'"
                                v-model="formData.cost_sale" :error="router.page.props.errors.cost_sale">
                            </TextInput>

                            <!--CAMPO DESCRIPCIÓN (description)-->
                            <div class="col-span-2">
                                <label class="text-sm font-bold text-gray-900">Descripción</label>
                                <Textarea class="text-sm text-gray-500 placeholder:text-sm italic"
                                    placeholder="Descripción del proyecto..." v-model="formData.description" rows="1"
                                    cols="143" autoResize :pt="{
                                        root: '!w-full'
                                    }" />
                            </div>
                        </section>
                    </tab-content>

                    <!--PLANEACIÓN DEL PROYECTO-->
                    <tab-content title="Planeación del Proyecto" icon="fa-solid fa-calendar-check"
                        :before-change="beforeChange">
                        <section class="flex border gap-6 border-gray-200 rounded-lg p-4">
                            <div class="grid grid-cols-6 gap-6 w-full">
                                <div class="col-span-3 space-y-4">
                                    <!--CAMPO FECHA INICIO-->
                                    <TextInput class="text-left" type="date" label="Fecha De Inicio"
                                        v-model="formData.start_date" :error="$page.props.errors.start_date"
                                        :disabled="!contractSelect">
                                    </TextInput>

                                    <!--CAMPO FECHA FINALIZACIÓN-->
                                    <TextInput class="text-left" type="date" label="Fecha de Finalización"
                                        v-model="formData.end_date" :error="$page.props.errors.end_date"
                                        :disabled="!contractSelect">
                                    </TextInput>

                                    <div class="flex col-span-3 gap-6">
                                        <!--CAMPO HORAS POR DÍA (hoursPerDay)-->
                                        <TextInput class="text-left" label="Horas por Dia"
                                            :placeholder="'Escriba Número de Horas por Dia'" v-model="formData.hoursPerDay"
                                            :error="router.page.props.errors.hoursPerDay">
                                        </TextInput>

                                        <!--CAMPO DIAS POR SEMANA (daysPerWeek)-->
                                        <TextInput class="text-left" label="Dias por Semana"
                                            :placeholder="'Escriba Numero de Horas por Dia'" v-model="formData.daysPerWeek"
                                            :error="router.page.props.errors.daysPerWeek">
                                        </TextInput>

                                        <!--CAMPO DIAS POR MES (daysPerMonth)-->
                                        <TextInput class="text-left" label="Dias por Mes"
                                            :placeholder="'Escriba Número de Horas por Dia'" v-model="formData.daysPerMonth"
                                            :error="router.page.props.errors.daysPerMonth">
                                        </TextInput>
                                    </div>
                                </div>

                                <!--CAMPO TURNO (shift)-->
                                <div class="col-span-3">
                                    <label class="text-sm font-bold text-gray-900">Seleccione el Turno</label>
                                    <div
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
                                                    <p class=" text-xs font-bold">{{ shift.name }}:</p>
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
                                    </div>
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
                                    root: '!w-56 !h-full !border-blue-800 !bg-transparent',
                                    label: '!text-blue-900',
                                    icon: '!text-blue-900',
                                    // label: props.text ? 'Seleccionar Todo' : 'Deseleccionar Todo'
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
                        <div class="w-full h-[89vh] overflow-y-auto">
                            <CustomDataTable :rowsDefault="100" :data="milestones" :columnas="columnas" :actions="actions"
                                @edit="showModal" :filter="false" :showHeader="false">

                                <template #buttonHeader>
                                    <Button label="Nuevo" severity="success" icon="fa-solid fa-plus" @click="showModal()" />
                                </template>
                            </CustomDataTable>
                        </div>
                    </tab-content>
                </form-wizard>
            </section>
        </main>
        <CustomModal v-model:visible="openDialog" width="30rem" :closable="false">
            <template #icon>
                <i class="fa-solid text-white fa-list-check"></i>
            </template>
            <template #titulo>
                <span class="text-lg font-bold text-white">
                    Agregar Hito
                </span>
            </template>
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
                        <ToggleButton v-model="formMilestone.invoiced" onLabel="100%" offLabel="0%" :pt="{
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
                <Button severity="success" outlined label="Guardar" icon="fa-solid fa-floppy-disk" @click="save()" />
                <Button severity="danger" outlined label="Cancelar" icon="fa-regular fa-circle-xmark"
                    @click="openDialog = false" />
            </template>
        </CustomModal>
    </AppLayout>
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
