<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, onMounted } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import '../../../sass/dataTableCustomized.scss'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Calendar from 'primevue/calendar'
import Tag from 'primevue/tag'
import Combobox from '@/Components/Combobox.vue'
import { FilterMatchMode, FilterOperator } from 'primevue/api'
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import DownloadExcelIcon from '@/Components/DownloadExcelIcon.vue'
import { MagnifyingGlassIcon, PencilIcon, TrashIcon, PlusIcon } from '@heroicons/vue/24/outline'
import { useSweetalert } from '@/composable/sweetAlert'
import { useConfirm } from "primevue/useconfirm"
import axios from 'axios'
// import plural from 'pluralize-es'
import TextInput from '../../Components/TextInput.vue'
import Textarea from 'primevue/textarea'
import Button from '../../Components/Button.vue'
import ShipCardMinimal from "@/Components/ShipCardMinimal.vue"
import Listbox from 'primevue/listbox'
import Card from 'primevue/card'
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

//#region Referencias (v-model)
const open = ref(false)
const contractSelect = ref()
const authorizationSelect = ref()
const quoteSelect = ref()
const shiftSelect = ref([])
// const shipSelect = ref()
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

const props = defineProps({
    'project': Object,
    'contracts': Array,
    'authorizations': Array,
    'quotes': Array,
})

//#region UseForm
const formData = useForm({
    id: props.project?.id ?? '0',
    name: props.project?.name ?? '',
    contract_id: props.project?.contract_id ?? '0',
    authorization_id: props.project?.authorization_id ?? '0',
    quote_id: props.project?.quote_id ?? '0',
    type: props.project?.type ?? '0', //ENUMS
    SAP_code: props.project?.SAP_code ?? '',
    status: props.project?.status ?? '0', //ENUMS
    scope: props.project?.scope ?? '0', //ENUMS
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

//Cancelar Creación de Proyectos
const cancelCreateProject = () => {
    router.get(route('projects.index'))
}

//#region if
// if (formData.id == 0) {
//     //Validaciones de Formulario de Contratos
//     if (selectedForm.value == 'form1' && !contractSelect.value) {
//         toast('Por favor, seleccione un contrato', 'error')
//         return
//     }

//     if (selectedForm.value == 'form1') {
//         formData['contract_id'] = contractSelect.value.id
//     }

//     //Validaciones de Formulario de Autorizaciones
//     if (selectedForm.value == 'form2' && !authorizationSelect.value) {
//         toast('Por favor, seleccione una autorización', 'error')
//         return
//     }

//     if (selectedForm.value == 'form2') {
//         formData['authorization_id'] = authorizationSelect.value.id
//     }

//     //Validaciones de Formulario de Estimaciones
//     if (selectedForm.value == 'form3' && !quoteSelect.value) {
//         toast('Por favor, seleccione una estimación', 'error')
//         return
//     }

//     if (selectedForm.value == 'form3') {
//         formData['quote_id'] = quoteSelect.value.id
//     }

//     //Validaciones de Formulario de Comunicaciones Internas
//     if (selectedForm.value == 'form4' && !shipSelect.value && !customerSelect.value) {
//         toast('Por favor, seleccione un ' ? 'Buque' : 'Cliente', 'error')
//         return
//     }

//     if (selectedForm.value == 'form4') {
//         formData['ship_id'] = ship.value.id
//         formData['customer_id'] = customer.value.id
//     }
// }
//endregion

/* SUBMIT*/
const beforeChange = () => {
    alert('Antes de pasar al otro tab')
    return true
}

const submit = () => {
    alert('Completado!')
    //Validación de Fechas de Finalización de Documentos Contractuales
    try {
        router.post(route('projects.store'), formData, {
            preserveScroll: true,
            onSuccess: (res) => {
                open.value = false
                toast('Proyecto creado exitosamente', 'success')
            },
            onError: (errors) => {
                toast('Ya existe un proyecto con este contrato.', 'error')
            },
            onFinish: () => {
                loading.value = false
            }
        })
    } catch (error) {
        toast(error.message)
    }
    return 'creado'
}

const shiftOptions = ref()
const getShift = () => {
    axios.get(route('shift.index'))
        .then(response => {
            shiftOptions.value = response.data.name
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

const formatDate = (value) => {
    return value.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    })
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
                    Agregar proyecto
                </h2>
            </header>

            <section class="grid grid-cols-1">
                <!-- AQUÍ VA EL CONTENIDO DEL FORMULARIO-->
                <form-wizard @on-complete="submit()" stepSize="md" color="#2E3092" nextButtonText="Siguiente"
                    backButtonText="Regresar" finishButtonText="Guardar">
                    <!--DOCUMENTOS CONTRACTUALES-->
                    <tab-content title="Información Contractual" icon="fa-solid fa-file-signature">
                        <section
                            class="sm:col-span-1 md:col-span-1 border gap-4 border-gray-200 rounded-lg p-4 grid grid-cols-2">

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
                    <tab-content title="Datos del Proyecto" icon="fa-solid fa-diagram-project">
                        <section
                            class="grid grid-cols-2 sm:col-span-1 md:col-span-1 border gap-4 border-gray-200 rounded-lg p-4 mb-2">
                            <!--CAMPO NOMBRE DEL PROYECTO (name)-->
                            <TextInput type="text" label="Nombre del Proyecto" placeholder="Escriba el nombre del proyecto"
                                v-model="formData.name" :error="$page.props.errors.name">
                            </TextInput>

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
                            <div class="">
                                <label class="text-sm font-bold text-gray-900">Descripción</label>
                                <Textarea class="col-span-2 text-sm text-gray-500 placeholder:text-sm italic"
                                    placeholder="Descripción del proyecto..." v-model="formData.description" autoResize
                                    rows="2" cols="67" />
                            </div>
                        </section>
                    </tab-content>

                    <!--PLANEACIÓN DEL PROYECTO-->
                    <tab-content title="Planeación del Proyecto" icon="fa-solid fa-calendar-check">
                        <section class="flex sm:col-span-1 md:col-span-1 border gap-6 border-gray-200 rounded-lg p-4">
                            <div class="grid grid-cols-6 gap-6">
                                <div class="col-span-3">
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
                                    <label class="text-sm font-bold text-gray-900">Turno</label>
                                    <div class="card flex justify-content-center">
                                        <Listbox v-model="shiftSelect" :options="shiftOptions" optionLabel="name"
                                            :virtualScrollerOptions="{ itemSize: 38 }" class="w-full md:w-14rem"
                                            listStyle="height:182px" />
                                    </div>
                                </div>
                            </div>
                        </section>
                    </tab-content>

                    <!--BUQUES-->
                    <tab-content title="Buques" icon="fa-solid fa-ship">
                        <section
                            class="sm:col-span-1 md:col-span-1 border gap-4 border-gray-200 rounded-lg p-4 mb-2 grid grid-cols-2">
                            <!--CAMPO TIPO DE PROYECTO (type)-->
                            <Combobox class="text-left text-gray-900" label="Tipo de Proyecto"
                                placeholder="Seleccione Tipo de Proyecto" :options="typeOptions" v-model="typeSelect">
                            </Combobox>

                            <ShipCardMinimal :ship="ships" :activo="false" :menu="false" :avance="false" />
                            <!-- <Card>
                                <template #title> Simple Card </template>
                                <template #content>
                                    <p class="m-0">
                                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Inventore sed consequuntur
                                        error repudiandae numquam deserunt quisquam repellat libero asperiores earum nam
                                        nobis, culpa ratione quam perferendis esse, cupiditate neque
                                        quas!
                                    </p>
                                </template>
                            </Card> -->
                        </section>
                    </tab-content>
                </form-wizard>
            </section>
        </main>
    </AppLayout>
</template>