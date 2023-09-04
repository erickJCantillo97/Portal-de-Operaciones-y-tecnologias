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
    projects: Array,
    ships: Array,
    contracts: Array
})

//#region UseForm
const formData = useForm({
    id: props.projects?.id ?? '0',
    ship_id: props.projects?.ship_id ?? '0',
    name: props.projects?.name ?? '',
    gerencia: props.projects?.gerencia ?? '',
    start_date: props.projects?.start_date ?? '',
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
    loading.value = true;
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
        <div class="flex">
            <div>
                
            </div>
            <!-- Columna izquierda con radio buttons -->
            <div class="w-1/2 p-4 bg-gray-100">
                <h2 class="text-lg font-semibold mb-4">Seleccione como crear el Proyecto</h2>
                <div class="space-y-2">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="radio" v-model="selectedForm" value="form1" class="form-radio">
                        <span>Contrato</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="radio" v-model="selectedForm" value="form2" class="form-radio">
                        <span>Autorización</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="radio" v-model="selectedForm" value="form3" class="form-radio">
                        <span>Comunicación Interna</span>
                    </label>
                </div>
            </div>

            <!-- Columna derecha con el formulario seleccionado -->
            <div class="w-1/2 p-4">
                <div v-if="selectedForm === 'form1'">
                    <h3 class="text-lg font-semibold mb-4">Formulario 1</h3>
                    <!-- Aquí va el contenido del Formulario 1 -->
                </div>
                <div v-else-if="selectedForm === 'form2'">
                    <h3 class="text-lg font-semibold mb-4">Formulario 2</h3>
                    <!-- Aquí va el contenido del Formulario 2 -->
                </div>
                <div v-else-if="selectedForm === 'form3'">
                    <h3 class="text-lg font-semibold mb-4">Formulario 3</h3>
                    <!-- Aquí va el contenido del Formulario 3 -->
                </div>
            </div>
        </div>
    </AppLayout>
</template>