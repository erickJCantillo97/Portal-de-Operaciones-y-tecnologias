<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, onMounted } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import '../../../sass/dataTableCustomized.scss';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Calendar from 'primevue/calendar';
import Tag from 'primevue/tag';
import Combobox from '@/Components/Combobox.vue';
import { FilterMatchMode, FilterOperator } from 'primevue/api';
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';
import DownloadExcelIcon from '@/Components/DownloadExcelIcon.vue';
import { MagnifyingGlassIcon, PencilIcon, TrashIcon, PlusIcon } from '@heroicons/vue/24/outline';
import { useSweetalert } from '@/composable/sweetAlert';
import { useConfirm } from "primevue/useconfirm";
import axios from 'axios';
// import plural from 'pluralize-es'
import TextInput from '../../Components/TextInput.vue';
import Button from '../../Components/Button.vue';
import FileUpload from 'primevue/fileupload';
// import Button from 'primevue/button';

const confirm = useConfirm();
const { toast } = useSweetalert();
const loading = ref(false);
const { confirmDelete } = useSweetalert();
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
})

const open = ref(false)
const selectedForm = ref('form1')
const contractSelect = ref()
const authorizationSelect = ref()
const quoteSelect = ref()
const intern_communicationsSelect = ref()
const customerSelect = ref()
const shipSelect = ref()

const props = defineProps({
    'projects': Array,
    'contracts': Array,
    'authorizations': Array,
    'quotes': Array,
})

//#region UseForm
const formData = useForm({
    id: props.projects?.id ?? '0',
    name: props.projects?.name ?? '0',
    contract_id: props.projects?.contract_id ?? '0',
    authorization_id: props.projects?.authorization_id ?? '0',
    quote_id: props.projects?.quote_id ?? '0',
    customer_id: props.projects?.customer_id ?? '0',
    name: props.projects?.name ?? '',
    start_date: props.projects?.start_date ?? '',
    end_date: props.projects?.end_date ?? '',
    hoursPerDay: props.projects?.hoursPerDay ?? '8.5',
    daysPerWeek: props.projects?.daysPerWeek ?? '5',
    daysPerMonth: props.projects?.daysPerMonth ?? '20',
    pdf: null
});
//#endregion

onMounted(() => {
    initFilters();
})

//Cancelar Creación de Proyectos
const cancelCreateProject = () => {
    router.get(route('projects.index'))
}

/* SUBMIT*/
const submit = () => {
    if (formData.id == 0) {
        //Validaciones de Formulario de Contratos
        if (selectedForm.value == 'form1' && !contractSelect.value) {
            toast('Por favor, seleccione un contrato', 'error');
            return;
        }

        if (selectedForm.value == 'form1') {
            formData['contract_id'] = contractSelect.value.id;
        }

        //Validaciones de Formulario de Autorizaciones
        if (selectedForm.value == 'form2' && !authorizationSelect.value) {
            toast('Por favor, seleccione una autorización', 'error');
            return;
        }

        if (selectedForm.value == 'form2') {
            formData['authorization_id'] = authorizationSelect.value.id;
        }

        //Validaciones de Formulario de Estimaciones
        if (selectedForm.value == 'form3' && !quoteSelect.value) {
            toast('Por favor, seleccione una estimación', 'error');
            return;
        }

        if (selectedForm.value == 'form3') {
            formData['quote_id'] = quoteSelect.value.id;
        }

        //Validaciones de Formulario de Comunicaciones Internas
        if (selectedForm.value == 'form4' && !shipSelect.value && !customerSelect.value) {
            toast('Por favor, seleccione un ' ? 'Buque' : 'Cliente', 'error');
            return;
        }

        if (selectedForm.value == 'form4') {
            formData['ship_id'] = ship.value.id;
            formData['customer_id'] = customer.value.id;
        }

        //Validación de Fechas de Finalización de Documentos Contractuales
        try {
            router.post(route('projects.store'), formData, {
                preserveScroll: true,
                onSuccess: (res) => {
                    open.value = false;
                    toast('Proyecto creado exitosamente', 'success');
                },
                onError: (errors) => {
                    toast('Ya existe un proyecto con este contrato.', 'error');
                },
                onFinish: () => {
                    loading.value = false;
                }
            });
        } catch (error) {
            toast(error.message)
        }
        return 'creado';
    }
}
// router.put(route('projects.update', formData.id), formData, {
//     preserveScroll: true,
//     onSuccess: (res) => {
//         open.value = false;
//         toast('¡Proyecto actualizado exitosamente!', 'success');
//     },
//     onError: (errors) => {
//         toast('¡Ups! Ha surgido un error', 'error');
//     },
//     onFinish: visit => {
//         loading.value = false;
//     }
// })

const editItem = (project) => {
    formData.id = project.id;
    formData.name = project.name;
    formData.gerencia = project.gerencia;
    formData.start_date = project.start_date;
    formData.end_date = project.end_date;
    formData.hoursPerDay = project.hoursPerDay;
    formData.daysPerWeek = project.daysPerWeek;
    formData.daysPerMonth = project.daysPerMonth;
    formData.pdf = project.pdf
    open.value = true;
};

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
};


const clearFilter = () => {
    initFilters();
};

const formatDate = (value) => {
    return value.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};

//#region COMPOSABLES
// Formatear el número en moneda (USD)
const formatCurrency = (value) => {
    return parseFloat(value).toLocaleString('es-CO', {
        style: 'currency',
        currency: 'COP'
    });
};

const getContractStatusSeverity = (project) => {
    switch (project.status) {
        case 'INICIADO':
            return 'info';

        case 'PROCESO':
            return 'warning';

        case 'PENDIENTE':
            return 'danger';

        case 'FINALIZADO':
            return 'success';

        default:
            return null;
    }
};

const exportarExcel = () => {
    //console.log(dt.value)
    // Acquire Data (reference to the HTML table)
    var table_elt = document.getElementById("tabla");

    var workbook = XLSX.utils.table_to_book(table_elt);

    var ws = workbook.Sheets["Sheet1"];
    XLSX.utils.sheet_add_aoa(ws, [
        ["Creado " + new Date().toISOString()]
    ], { origin: -1 });

    // Package and Release Data (`writeFile` tries to write and save an XLSB file)
    XLSX.writeFile(workbook, 'Lista de Contratos_' + project.nit + '_' + project.name + ".xlsb");
};
//#endregion
</script>

<template>
    <AppLayout>
        <div class="px-8 min-h-screen">
            <div class="w-full">
                <h2 class="text-lg font-semibold mb-4 text-primary text-center lg:text-2xl">
                    Agregar proyecto
                </h2>
            </div>
            <label class="block capitalize text-sm font-bold text-gray-900 text-center">
                Documento Contractual
            </label>

            <!-- RADIO BUTTON CONTRATO -->
            <div
                class="space-y-2 flex flex-col sm:flex-row sm:space-x-6 sm:items-center px-4 sm:px-12 md:flex-row md:space-x-6 md:items-center md:px-16 lg:flex-row lg:space-x-6 lg:items-center lg:px-20 xl:px-24 2xl:px-32">
                <label class="flex items-center space-x-2 cursor-pointer rounded-lg p-3 w-full sm:w-auto"
                    :class="[selectedForm == 'form1' ? 'bg-blue-100' : '']">
                    <input type="radio" v-model="selectedForm" value="form1" class="form-radio mr-4">
                    <div>
                        <span>Contrato</span>
                        <p class="text-xs italic text-gray-600">
                            Seleccione esta opción si solo cuenta con el Contrato.</p>
                    </div>
                </label>

                <!-- RADIO BUTTON AUTORIZACIÓN -->
                <label class="flex items-center space-x-2 cursor-pointer rounded-lg p-3 w-full sm:w-auto"
                    :class="[selectedForm == 'form2' ? 'bg-blue-100' : '']">
                    <input type="radio" v-model="selectedForm" value="form2" class="form-radio mr-4">
                    <div>
                        <span>Autorización</span>
                        <p class="text-xs italic text-gray-600">
                            Seleccione esta opción si solo cuenta con la Autorización.
                        </p>
                    </div>
                </label>

                <!-- RADIO BUTTON ESTIMACIÓN -->
                <label class="flex items-center space-x-2 cursor-pointer rounded-lg p-3 w-full sm:w-auto"
                    :class="[selectedForm == 'form3' ? 'bg-blue-100' : '']">
                    <input type="radio" v-model="selectedForm" value="form3" class="form-radio mr-4">
                    <div>
                        <span>Estimación</span>
                        <p class="text-xs italic text-gray-600">
                            Seleccione esta opción si es una <br> Estimación.
                        </p>
                    </div>
                </label>

                <!-- RADIO BUTTON COMUNICACIONES INTERNAS -->
                <label class="flex items-center space-x-2 cursor-pointer rounded-lg p-3 w-full sm:w-auto"
                    :class="[selectedForm == 'form4' ? 'bg-blue-100' : '']">
                    <input type="radio" v-model="selectedForm" value="form4" class="form-radio mr-4">
                    <div>
                        <span>Comunicación Interna</span>
                        <p class="text-xs italic text-gray-600">
                            Seleccione esta opción si es una <br> Comunicación Interna.
                        </p>
                    </div>
                </label>
            </div>


            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Columna izquierda del formulario -->
                <!-- Aquí va el contenido del Formulario 1 -->
                <div class="md:col-span-1">
                    <div class="space-y-2 border border-gray-200 rounded-lg p-4">
                        <!--CONFIGURACIÓN DE PROYECTO-->
                        <div class="" v-if="selectedForm == 'form1'">
                            <Combobox class="text-left text-gray-900" label="Contrato" placeholder="Seleccione Contrato"
                                :options="contracts" v-model="contractSelect" @update:modelValue="loadContractDates()">
                            </Combobox>
                        </div>

                        <div class=" " v-if="selectedForm == 'form2'">
                            <Combobox class="text-left text-gray-900" label="Autorizaciones"
                                placeholder="Seleccione Autorización" :options="authorizations"
                                v-model="authorizationSelect" @update:modelValue="loadAuthorizationDates()">
                            </Combobox>
                        </div>

                        <div class="" v-if="selectedForm == 'form3'">
                            <Combobox class="text-left text-gray-900" label="Estimaciones"
                                placeholder="Seleccione Estimación" :options="quotes" v-model="quoteSelect"
                                @update:modelValue="loadQuoteDates()">
                            </Combobox>
                        </div>

                        <TextInput class=" text-left" type="text" v-if="selectedForm == 'form4'" label=" Código de
                        Comunicación Interna" :placeholder="'Escriba el Tipo de Cliente'"
                            v-model="formData.intern_communications" :error="$page.props.errors.intern_communications">
                        </TextInput>

                        <div class="p-1 " v-if="selectedForm == 'form4'">
                            <Combobox class="text-left" label="Buque" placeholder="Seleccione Buque" :options="ships"
                                v-model="shipSelect">
                            </Combobox>

                            <Combobox class="text-left" label="Cliente" placeholder="Seleccione Cliente"
                                :options="customers" v-model="customerSelect">
                            </Combobox>

                        </div>

                        <!--CAMPO FECHA INICIO-->
                        <TextInput class="p-1 pb-1 text-left" type="date" label="Fecha De Inicio"
                            v-model="formData.start_date" :error="$page.props.errors.start_date"
                            :disabled="!contractSelect">
                        </TextInput>

                        <!--CAMPO FECHA FINALIZACIÓN-->
                        <TextInput class="p-1 pb-1 text-left" type="date" label="Fecha de Finalización"
                            v-model="formData.end_date" :error="$page.props.errors.end_date" :disabled="!contractSelect">
                        </TextInput>

                        <!--ADJUNTAR ARCHIVO PDF-->
                        <!-- <FileUpload chooseLabel="Adjuntar PDF" mode="basic" name="demo[]" :multiple="false" accept=".pdf"
                            :maxFileSize="1000000" @input="formData.pdf = $event.target.files[0]">
                        </FileUpload> -->
                    </div>
                </div>

                <!--CONFIGURACIÓN DE CRONOGRAMA-->
                <div class="md:col-span-1 space-y-4 border border-gray-200 rounded-lg p-4">
                    <TextInput class="mt-2 text-left" label="Horas por Dia" :placeholder="'Escriba Número de Horas por Dia'"
                        v-model="formData.hoursPerDay" :error="router.page.props.errors.hoursPerDay"></TextInput>

                    <TextInput class="mt-2 text-left" label="Dias por Semana"
                        :placeholder="'Escriba Numero de Horas por Dia'" v-model="formData.daysPerWeek"
                        :error="router.page.props.errors.daysPerWeek"></TextInput>

                    <TextInput class="mt-2 text-left" label="Dias por Mes" :placeholder="'Escriba Número de Horas por Dia'"
                        v-model="formData.daysPerMonth" :error="router.page.props.errors.daysPerMonth"></TextInput>
                    <div class="flex px-2 mt-2 space-x-4">
                        <Button class="hover:bg-danger text-danger border-danger" severity="danger"
                            @click="cancelCreateProject()">Cancelar</Button>
                        <Button severity="success" :loading="false" class="text-success hover:bg-success border-success"
                            @click="submit()">
                            {{ formData.id != 0 ? 'Actualizar ' : 'Guardar' }}
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>