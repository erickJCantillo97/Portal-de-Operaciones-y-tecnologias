<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, onMounted, computed } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import Combobox from '@/Components/Combobox.vue'
import Moment from 'moment'
import { FilterMatchMode, FilterOperator } from 'primevue/api'
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import DownloadExcelIcon from '@/Components/DownloadExcelIcon.vue'
import { ClockIcon, CalendarDaysIcon } from '@heroicons/vue/24/outline'
import { useSweetalert } from '@/composable/sweetAlert'
import { useConfirm } from "primevue/useconfirm"
import axios from 'axios'
// import plural from 'pluralize-es'
import TextInput from '../../Components/TextInput.vue'
import Textarea from 'primevue/textarea'
import Button from 'primevue/button'
// import Button from '../../Components/Button.vue'
import ShipCardMinimal from "@/Components/ShipCardMinimal.vue"
import Listbox from 'primevue/listbox'
import { FormWizard, TabContent } from 'vue3-form-wizard'
import 'vue3-form-wizard/dist/style.css'
import FileUpload from 'primevue/fileupload'
// import Button from 'primevue/button'

const confirm = useConfirm()
const { toast } = useSweetalert()
const loading = ref(false)
const { confirmDelete } = useSweetalert()
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
})

const props = defineProps({
    'project': Object,
    'contracts': Array,
    'authorizations': Array,
    'quotes': Array,
    'ships': Array,
    // 'typeShips': Array,
})

//#region Referencias (v-model)
const open = ref(false)
const contractSelect = ref()
const authorizationSelect = ref()
const quoteSelect = ref()
const selectedShips = ref([])
const API_Ships = ref(props.ships)
const filteredShips = ref(props.ships)
const keyword = ref('')
//#endregion

const searchShips = () => {
    const searchWord = keyword.value.toLowerCase().trim()

    filteredShips.value = API_Ships.value.filter(ship =>
        ship.name.toLowerCase().includes(searchWord) ||
        ship.idHull.toLowerCase().includes(searchWord) ||
        ship.type_ship.name.toLowerCase().includes(searchWord)
    )
}

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
    id: props.project?.id ?? '0',
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
    initFilters()
})

const toggleSelectShip = (shipId) => {
    if (selectedShips.value.includes(shipId)) {
        selectedShips.value = selectedShips.value.filter(id => id !== shipId)
    } else {
        selectedShips.value = [...selectedShips.value, shipId]
    }
}

const selectShiftList = (shiftId) => {
    if (shiftSelect.value.length === 1 && shiftSelect.value[0] === shiftId) {
        shiftSelect.value = []
    } else {
        shiftSelect.value = [shiftId]
    }
}

//Cancelar Creación de Proyectos
const cancelCreateProject = () => {
    router.get(route('projects.index'))
}


/* SUBMIT*/
const isSaved = ref(false)
const projectIdRef = ref(null)

const beforeChange = async () => {
    formData.ships = selectedShips.value
    let switchStates = false

    try {
        console.log(projectIdRef.value)
        if (!projectIdRef.value) {
            await axios.post(route('projects.store'), formData)
                .then((res) => {
                    toast('Proyecto creado exitosamente!', 'success')
                    projectIdRef.value = res.data.project_id
                    switchStates = true
                })
            return switchStates
        } else {
            formData.shift = selectedShift.value
            await axios.put(route('projects.update', projectIdRef.value), formData)
                .then((res) => {
                    toast('Proyecto actualizado exitosamente!', 'success')
                    switchStates = true
                })
            return switchStates
            // return true
        }
    } catch (error) {
        toast(error.message)
    }
}

const submit = async () => {
    // TO DO store onComplete()
    // return false
    try {
        formData.ships = selectedShips.value
        if (!projectIdRef) {
            await axios.post(route('projects.store', projectIdRef), formData)
                .then((res) => {
                    toast('Proyecto creado exitosamente!', 'success')
                    switchStates = true
                })
            return switchStates
        } else {
            await axios.put(route('projects.update', projectIdRef), formData)
                .then((res) => {
                    toast('Proyecto actualizado exitosamente!', 'success')
                    switchStates = true
                })
            return switchStates
            // return true
        }
    } catch (error) {
        toast(error.message)
    }
    // return false
}

const shiftSelect = ref([])
const shiftOptions = ref()

const getShift = () => {
    axios.get(route('shift.index'))
        .then(response => {
            shiftOptions.value = response.data[0]
        })
}

// router.put(route('projects.update', formData.id), formData, {
//     preserveScroll: true,
//     onSuccess: (res) => {
//         open.value = false
//         toast('¡Proyecto actualizado exitosamente!', 'success')
//     },
//     onError: (errors) => {
//         toast('¡Ups! Ha surgido un error', 'error')
//     },
//     onFinish: visit => {
//         loading.value = false
//     }
// })

const loadContractDates = () => {

    const contract = contractSelect.value

    if (contract) {
        formData.start_date = contract.start_date
        formData.end_date = contract.end_date
    }
}

const loadAuthorizationDates = () => {

    const authorization = authorizationSelect.value

    if (authorization) {
        formData.start_date = authorization.start_date
        formData.end_date = authorization.end_date
    }
}

const loadQuoteDates = () => {

    const quote = quoteSelect.value

    if (quote) {
        formData.start_date = quote.start_date
        formData.end_date = quote.end_date
    }
}

const initFilters = () => {
    filters.value = {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS },
        name: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
    }
}

const clearFilter = () => {
    initFilters()
}

function formatDateTime24h(date) {
    return new Date(date).toLocaleString('es-CO',
        { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' })
}

//#region COMPOSABLES
// Formatear el número en moneda (USD)
const formatCurrency = (value) => {
    return parseFloat(value).toLocaleString('es-CO', {
        style: 'currency',
        currency: 'COP'
    })
}

const getContractStatusSeverity = (project) => {
    switch (project.status) {
        case 'INICIADO':
            return 'info'

        case 'PROCESO':
            return 'warning'

        case 'PENDIENTE':
            return 'danger'

        case 'FINALIZADO':
            return 'success'

        default:
            return null
    }
}

const exportarExcel = () => {
    //console.log(dt.value)
    // Acquire Data (reference to the HTML table)
    var table_elt = document.getElementById("tabla")

    var workbook = XLSX.utils.table_to_book(table_elt)

    var ws = workbook.Sheets["Sheet1"]
    XLSX.utils.sheet_add_aoa(ws, [
        ["Creado " + new Date().toISOString()]
    ], { origin: -1 })

    // Package and Release Data (`writeFile` tries to write and save an XLSB file)
    XLSX.writeFile(workbook, 'Lista de Contratos_' + project.nit + '_' + project.name + ".xlsb")
}
//#endregion
</script>
<template>
    <AppLayout>
        <main class="px-8 min-h-full overflow-y-scroll custom-scroll">
            <header class="w-full">
                <h2 class="text-lg font-semibold mb-4 text-primary text-center lg:text-2xl">
                    Agregar Proyecto
                </h2>
            </header>

            <section class="grid grid-cols-1">
                <!-- AQUÍ VA EL CONTENIDO DEL FORMULARIO-->
                <form-wizard @on-complete="submit()" stepSize="md" color="#2E3092" nextButtonText="Siguiente"
                    backButtonText="Regresar" finishButtonText="Guardar">
                    <!--DOCUMENTOS CONTRACTUALES-->
                    <tab-content title="Información Contractual" icon="fa-solid fa-file-signature"
                        :before-change="beforeChange">
                        <section
                            class="sm:col-span-1 md:col-span-1 border gap-4 border-gray-200 rounded-lg p-4 grid grid-cols-2">
                            <!--CAMPO NOMBRE DEL PROYECTO (name)-->
                            <TextInput type="text" label="Nombre del Proyecto" placeholder="Escriba el nombre del proyecto"
                                v-model="formData.name" :error="$page.props.errors.name">
                            </TextInput>

                            <!--CAMPO CÓDIGO DE SAP (SAP_code)-->
                            <TextInput type="text" label="Código SAP" placeholder="Escriba el código de SAP"
                                v-model="formData.SAP_code" :error="$page.props.errors.SAP_code">
                            </TextInput>

                            <!--CAMPO ALCANCE DEL PROYECTO (scope)-->
                            <Combobox class="text-left text-gray-900" label="Alcance del Proyecto"
                                placeholder="Seleccione Alcance del Proyecto" :options="scopeOptions" v-model="scopeSelect">
                            </Combobox>

                            <!--CAMPO CONTRATO (contract)-->
                            <Combobox class="text-left text-gray-900" label="Contrato" placeholder="Seleccione Contrato"
                                :options="contracts" v-model="contractSelect">
                            </Combobox>

                            <!--CAMPO AUTORIZACIONES (authorization)-->
                            <Combobox class="text-left text-gray-900" label="Autorizaciones"
                                placeholder="Seleccione Autorización" :options="authorizations"
                                v-model="authorizationSelect">
                            </Combobox>

                            <!--CAMPO ESTIMACIÓN (quote)-->
                            <Combobox class="text-left text-gray-900" label="Estimaciones"
                                placeholder="Seleccione Estimación" :options="quotes" v-model="quoteSelect">
                            </Combobox>
                        </section>
                    </tab-content>

                    <!--DATOS DEL PROYECTO-->
                    <tab-content title="Datos del Proyecto" icon="fa-solid fa-diagram-project" :before-change="beforeChange" >
                        <section
                            class="grid grid-cols-2 sm:col-span-1 md:col-span-1 border gap-4 border-gray-200 rounded-lg p-4">
                            <!--CAMPO SUPERVISOR (supervisor)-->
                            <TextInput label="Supervisor" type="text" :placeholder="'Nombre del supervisor'"
                                v-model="formData.supervisor" :error="router.page.props.errors.supervisor">
                            </TextInput>

                            <!--CAMPO TIPO DE PROYECTO (type)-->
                            <Combobox class="text-left text-gray-900" label="Tipo de Proyecto"
                                placeholder="Seleccione Tipo de Proyecto" :options="typeOptions" v-model="typeSelect">
                            </Combobox>

                            <!--CAMPO ESTADO DEL PROYECTO (state)-->
                            <Combobox class="text-left text-gray-900" label="Estado del Proyecto"
                                placeholder="Seleccione Estado del Proyecto" :options="statusOptions"
                                v-model="statusSelect">
                            </Combobox>

                            <!--CAMPO COSTO DE VENTA (cost_sale)-->
                            <TextInput label="Costo de Venta" type="number" :placeholder="'Escriba el costo de venta'"
                                v-model="formData.cost_sale" :error="router.page.props.errors.cost_sale">
                            </TextInput>

                            <!--CAMPO DESCRIPCIÓN (description)-->
                            <div>
                                <label class="text-sm font-bold text-gray-900">Descripción</label>
                                <Textarea class="col-span-4 text-sm text-gray-500 placeholder:text-sm italic"
                                    placeholder="Descripción del proyecto..." v-model="formData.description" rows="1"
                                    cols="143" autoResize />
                            </div>
                        </section>
                    </tab-content>

                    <!--PLANEACIÓN DEL PROYECTO-->
                    <tab-content title="Planeación del Proyecto" icon="fa-solid fa-calendar-check" :before-change="beforeChange">
                        <section class="flex sm:col-span-1 md:col-span-1 border gap-6 border-gray-200 rounded-lg p-4">
                            <div class="grid grid-cols-6 gap-6">
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
                                    <!-- <Listbox v-if="shiftOptions != null" v-model="shiftSelect" :options="shiftOptions" optionLabel="name"
                                        :key="showListbox" :virtualScrollerOptions="{ itemSize: 38 }"
                                        class="w-full md:w-14rem" listStyle="height:182px">
                                        <template #option="slotProps">
                                            <div class="grid grid-cols-4 align-items-center">
                                                <p class="col-span-1 text-xs font-bold">{{ slotProps.option.name }}</p>
                                                <p class="col-span-3 text-xs">
                                                    <div class="flex italic">
                                                        <ClockIcon class="w-4 h-4" />
                                                        <p><b>&nbsp Hora Inicio:</b> {{ formatDateTime24h(slotProps.option.startShift) }} - </p>
                                                        <p>&nbsp <b>Hora Fin:</b> {{ formatDateTime24h(slotProps.option.endShift) }} - </p>
                                                        <p>&nbsp <b>Descanso:</b> {{ slotProps.option.timeBreak }}h - </p>
                                                        <p>&nbsp <b>H. Laborales:</b> {{ parseFloat(slotProps.option.hours).toFixed(1) }}</p>
                                                    </div>
                                                </p>
                                            </div>
                                        </template>
                                    </Listbox> -->
                                    <div
                                        class="w-full h-52 overflow-y-auto custom-scroll border-2 border-gray-300 rounded-lg p-2 focus hover:border-blue-500">
                                        <ul v-for="shift in shiftOptions" :key="shift.id">
                                            <div @click="selectShiftList(shift.id)"
                                                :class="shiftSelect.includes(shift.id) ? 'bg-blue-900 text-white' : 'hover:bg-gray-200'"
                                                class="flex justify-between items-center text-xs space-x-6 p-2 w-full cursor-pointer rounded-lg">
                                                <div>
                                                    <p class=" text-xs font-bold">{{ shift.name }}:</p>
                                                </div>
                                                <div class="flex italic">
                                                    <ClockIcon class="w-4 h-4" />
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
                    <tab-content title="Buques" icon="fa-solid fa-ship">
                        <div class="flex w-full gap-2 pb-4">
                            <input type="search" v-model="keyword" @input="searchShips()"
                                class="rounded-lg border-2 border-gray-200 w-full placeholder:italic"
                                placeholder="Filtrar Buques" />
                        </div>
                        <section
                            class="grid grid-cols-4 h-60 overflow-y-auto custom-scroll snap-y snap-mandatory sm:col-span-1 md:col-span-1 border gap-4 border-gray-200 rounded-lg p-4 mb-2">
                            <ul v-for="ship in filteredShips" :key="ship.id"
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
                </form-wizard>
            </section>
        </main>
    </AppLayout>
</template>
