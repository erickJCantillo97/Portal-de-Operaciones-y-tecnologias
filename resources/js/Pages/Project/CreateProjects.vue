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

const props = defineProps({
    'projects': Array,
    'contracts': Array,
    'authorizations': Array,
    'quotes': Array,
    'ships': Array,
})

//#region UseForm
const formData = useForm({
    id: props.projects?.id ?? '0',
    contract_id: props.projects?.contract_id ?? '0',
    authorization_id: props.projects?.authorization_id ?? '0',
    quote_id: props.projects?.quote_id ?? '0',
    ship_id: props.projects?.ship_id ?? '0',
    intern_communications: props.projects?.intern_communications ?? '0',
    // name: props.projects?.name ?? '',
    start_date: props.projects?.start_date ?? '',
    end_date: props.projects?.end_date ?? '',
    hoursPerDay: props.projects?.hoursPerDay ?? '8.5',
    daysPerWeek: props.projects?.daysPerWeek ?? '5',
    daysPerMonth: props.projects?.daysPerMonth ?? '20'
});
//#endregion

onMounted(() => {
    initFilters();
})

/* SUBMIT*/
const submit = () => {
    if (formData.id == 0) {
        router.post(route('projects.store'), formData, {
            preserveScroll: true,
            onSuccess: (res) => {
                open.value = false;
                toast('Proyecto creado exitosamente', 'success');
            },
            onError: (errors) => {
                toast('¡Ups! Ha surgido un error', 'error');
            },
            onFinish: visit => {
                loading.value = false;
            }
        })
        return 'creado';
    }
    router.put(route('projects.update', formData.id), formData, {
        preserveScroll: true,
        onSuccess: (res) => {
            open.value = false;
            toast('¡Proyecto actualizado exitosamente!', 'success');
        },
        onError: (errors) => {
            toast('¡Ups! Ha surgido un error', 'error');
        },
        onFinish: visit => {
            loading.value = false;
        }
    })
}

const addItem = () => {
    formData.reset();
    open.value = true;
}

const editItem = (project) => {
    formData.id = project.id;
    formData.name = project.name;
    formData.gerencia = project.gerencia;
    formData.start_date = project.start_date;
    formData.hoursPerDay = project.hoursPerDay;
    formData.daysPerWeek = project.daysPerWeek;
    formData.daysPerMonth = project.daysPerMonth;
    open.value = true;
};


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

            <!--RADIO BUTTON CONTRATO-->
            <div class="space-y-2 flex w-full justify-between px-12 space-x-6">
                <label class="flex items-center space-x-2 cursor-pointer rounded-lg p-3 w-full"
                    :class="[selectedForm == 'form1' ? 'bg-blue-100' : '']">
                    <input type="radio" v-model="selectedForm" value="form1" class="form-radio mr-4">
                    <div>
                        <span>Contrato</span>
                        <p class="text-xs italic text-gray-600 ">
                            Seleccione esta opción si solo cuenta con el Contrato.</p>
                    </div>
                </label>

                <!--RADIO BUTTON AUTORIZACIÓN-->
                <label class="flex items-center space-x-2 cursor-pointer rounded-lg p-3 w-full"
                    :class="[selectedForm == 'form2' ? 'bg-blue-100' : '']">
                    <input type="radio" v-model="selectedForm" value="form2" class="form-radio mr-4">
                    <div>
                        <span>Autorización</span>
                        <p class="text-xs italic text-gray-600 ">
                            Seleccione esta opción si solo cuenta con la Autorización.
                        </p>
                    </div>
                </label>

                <!--RADIO BUTTON ESTIMACIÓN-->
                <label class="flex items-center space-x-2 cursor-pointer rounded-lg p-3 w-full"
                    :class="[selectedForm == 'form3' ? 'bg-blue-100' : '']">
                    <input type="radio" v-model="selectedForm" value="form3" class="form-radio mr-4">
                    <div>
                        <span>Estimación</span>
                        <p class="text-xs italic text-gray-600 ">
                            Seleccione esta opción si es una <br> Estimación.
                        </p>
                    </div>
                </label>

                <!--RADIO BUTTON COMUNICACIONES INTERNAS-->
                <label class="flex items-center space-x-2 cursor-pointer rounded-lg p-3 w-full"
                    :class="[selectedForm == 'form4' ? 'bg-blue-100' : '']">
                    <input type="radio" v-model="selectedForm" value="form4" class="form-radio mr-4">
                    <div>
                        <span>Comunicación Interna</span>
                        <p class="text-xs italic text-gray-600 ">
                            Seleccione esta opción si es una <br> Comunicación Interna.
                        </p>
                    </div>
                </label>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Columna izquierda del formulario -->
                <!-- Aquí va el contenido del Formulario 1 -->
                <div>
                    <div class="space-y-2 border border-gray-200 rounded-lg p-4">
                        <div class="" v-if="selectedForm == 'form1'">
                            <Combobox class="text-left text-gray-900" label="Contrato" placeholder="Seleccione Contrato"
                                :options="contracts" v-model="contractSelect">
                            </Combobox>
                        </div>
                        <div class=" " v-if="selectedForm == 'form2'">
                            <Combobox class="text-left text-gray-900" label="Autorizaciones"
                                placeholder="Seleccione Autorización" :options="authorizations"
                                v-model="authorization_idSelect">
                            </Combobox>
                        </div>
                        <div class="" v-if="selectedForm == 'form3'">
                            <Combobox class="text-left text-gray-900" label="Estimaciones"
                                placeholder="Seleccione Estimación" :options="quotes" v-model="quoteSelect">
                            </Combobox>
                        </div>
                        <TextInput class=" text-left" type="text" v-if="selectedForm == 'form4'" label=" Codigo de
                        comunicación Interna" :placeholder="'Escriba el Tipo de Cliente'"
                            v-model="formData.intern_communications" :error="$page.props.errors.intern_communications">
                        </TextInput>

                        <div class="p-1 " v-if="selectedForm == 'form4'">
                            <Combobox class="text-left" label="Buque" placeholder="Seleccione Buque" :options="ships"
                                v-model="shipSelect"></Combobox>
                        </div>

                        <!--CAMPO FECHA INICIO-->
                        <TextInput class="p-1 pb-1 text-left" type="date" label="Fecha De Inicio"
                            :placeholder="'Escriba el Tipo de Cliente'" v-model="formData.start_date"
                            :error="$page.props.errors.start_date">
                        </TextInput>

                        <!--CAMPO FECHA FINALIZACIÓN-->
                        <TextInput class="p-1 pb-1 text-left" type="date" label="Fecha de Finalización"
                            :placeholder="'Escriba el Tipo de Cliente'" v-model="formData.end_date"
                            :error="$page.props.errors.end_date">
                        </TextInput>
                    </div>
                </div>





                <div class="space-y-4 border border-gray-200 rounded-lg p-4">
                    <TextInput class="mt-2 text-left" label="Horas por Dia" :placeholder="'Escriba Numero de Horas por Dia'"
                        v-model="formData.hoursPerDay" :error="router.page.props.errors.hoursPerDay"></TextInput>

                    <TextInput class="mt-2 text-left" label="Dias por Semana"
                        :placeholder="'Escriba Numero de Horas por Dia'" v-model="formData.daysPerWeek"
                        :error="router.page.props.errors.daysPerWeek"></TextInput>

                    <TextInput class="mt-2 text-left" label="Dias por Mes" :placeholder="'Escriba Numero de Horas por Dia'"
                        v-model="formData.daysPerMonth" :error="router.page.props.errors.daysPerMonth"></TextInput>
                    <div class="flex px-2 mt-2 space-x-4">
                        <Button class="hover:bg-danger text-danger border-danger" severity="danger"
                            @click="open = false">Cancelar</Button>
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