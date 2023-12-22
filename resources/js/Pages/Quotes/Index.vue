<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, onMounted } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import Combobox from '@/Components/Combobox.vue';
import { FilterMatchMode, FilterOperator } from 'primevue/api';
import { useSweetalert } from '@/composable/sweetAlert';
import { useConfirm } from "primevue/useconfirm";
// import plural from 'pluralize-es'
import FileUpload from 'primevue/fileupload';
import Button from 'primevue/button';
import CustomModal from '@/Components/CustomModal.vue';
import CustomDataTable from '@/Components/CustomDataTable.vue';
import InputText from 'primevue/inputtext';

const confirm = useConfirm();
const { toast } = useSweetalert();
const loading = ref(false);
const { confirmDelete } = useSweetalert();
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
})
const open = ref(false)
const shipSelect = ref()

const props = defineProps({
    quotes: Array,
    gerencias: Array,
    ships: Array
})

//#region UseForm
const formData = useForm({
    id: props.quotes?.id ?? '0',
    ship_id: props.quotes?.ship_id ?? '0',
    code: props.quotes?.code ?? '0',
    //gerencia: props.contracts?.gerencia ?? '',
    cost: props.quotes?.cost ?? '0',
    start_date: props.quotes?.start_date ?? '',
    end_date: props.quotes?.end_date ?? '',
    pdf: null
});
//#endregion

onMounted(() => {
    initFilters();
})

/* SUBMIT*/
const submit = () => {
    loading.value = true;

    if (!shipSelect.value) {
        toast('Por favor seleccione un buque.', 'error')
        return
    }

    formData.ship_id = shipSelect.value.id

    if (formData.id == 0) {
        router.post(route('quotes.store'), formData, {
            preserveScroll: true,
            onSuccess: (res) => {
                open.value = false;
                toast(' Cotizacion creado exitosamente', 'success');
            },
            onError: (errors) => {
                toast('Por favor diligencie todos los campos.', 'error');
            },
            onFinish: visit => {
                loading.value = false;
            }
        })
        return 'creado';
    }
    router.put(route('quotes.update', formData.id), formData, {
        preserveScroll: true,
        onSuccess: (res) => {
            open.value = false;
            toast('¡Cotización actualizada exitosamente!', 'success');
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
    clearErrors();
    shipSelect.value = {};
    open.value = true;
}

const editItem = (quote) => {
    clearErrors();

    formData.id = quote.id;
    // formData.gerencia = quote.gerencia;
    shipSelect.value = quote.ship;
    formData.code = quote.code;
    formData.cost = quote.cost;
    formData.start_date = quote.start_date;
    formData.end_date = quote.end_date;
    open.value = true;
};


const initFilters = () => {
    filters.value = {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS },
        name: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
    }
};

const clearErrors = () => {
    router.page.props.errors = {};
};

//#region DataTable
const columnas = ref([
    { field: 'id', header: 'Id', frozen:true, filter: true, sortable: true },
    { field: 'gerencia', header: 'Gerencia', filter: true, sortable: true },
    { field: 'costumer_id', header: 'Cliente' },
    { field: 'consecutive', header: 'Consecutivo' },
    { field: 'version', header: 'Version', filter: false },
    { field: 'expeted_answer_date', header: 'Fecha maxima respuesta', type: 'date', filter: true, },
    { field: 'estimador_anaswer_date', header: 'Fecha respuesta', type: 'date', filter: true },
    { field: 'route', header: 'Ruta', filter: false },
    { field: 'file', header: 'Documento', filter: false },
    { field: 'observation', header: 'Observacion', filter: false },
    { field: 'created_at', header: 'Fecha creacion', filter: false },
]);
//#endregion
//#region BotonesDatatable
const buttons = ref([
    { event: 'showClic', severity: 'success', class: '', icon: 'fa-solid fa-eye', text: true, outlined: false, rounded: false },
    { event: 'deleteClic', severity: 'danger', icon: 'fa-solid fa-trash', class: '!h-8', text: true, outlined: false, rounded: false },
]);
//#endregion

const showClic =(event, data)=>{
    console.log(data)
}

const deleteClic =(event)=>{
    console.log(event)
}

</script>

<template>
    <AppLayout>
        <CustomDataTable :data="quotes" :columnas="columnas" :actions="buttons" @showClic="showClic" @deleteClic="deleteClic">
            <template #header>
                <h1 class="text-xl font-semibold leading-6 capitalize text-primary">
                    Estimaciones
                </h1>
                <Button title="Agregar Estimación" @click="addItem()" severity="success" label="Agregar" outlined
                    icon="fa-solid fa-plus" class="!h-8" />
            </template>
        </CustomDataTable>
        <!--MODAL DE FORMULARIO-->
    </AppLayout>
    <CustomModal :visible="open">
        <template #titulo>
            <p class="text-white">{{ formData.id != 0 ? 'Editar ' : 'Crear' }} solicitud de estimación </p>
        </template>
        <template #icon>
            <i class="fa-solid fa-file-circle-question text-white text-xl"></i>
        </template>
        <template #body>
            <div class="grid grid-cols-4 gap-2">
                <!--formData.id hace referencia si está activo el modal o no (0=activo; 1=inactivo)-->
                <InputText label="Consecutivo" :enabled="formData.id == 0"
                    :placeholder="'Escriba el consecutivo de la estimacion'" v-model="formData.code"
                    :error="router.page.props.errors.code">
                </InputText>

                <Combobox class="text-left text-gray-900" label="Buque" placeholder="Seleccione Buque" :options="ships"
                    v-model="shipSelect" :error="router.page.props.errors.ship">
                </Combobox>

                <InputText class="text-left" label="Costo" type="number" :placeholder="'Escriba el valor total estimado'"
                    v-model="formData.cost" :error="router.page.props.errors.cost">
                </InputText>

                <!--CAMPO FECHA INICIO-->
                <InputText class="text-left" type="date" label="Fecha de inicio" :placeholder="'Escriba el Tipo de Cliente'"
                    v-model="formData.start_date" :error="$page.props.errors.start_date">
                </InputText>

                <!--CAMPO FECHA FINALIZACIÓN-->
                <InputText class="mt-2 text-left" type="date" label="Fecha de Finalización"
                    :placeholder="'Escriba el Tipo de Cliente'" v-model="formData.end_date"
                    :error="$page.props.errors.end_date">
                </InputText>
                <FileUpload chooseLabel="Adjuntar PDF" mode="basic" name="demo[]" :multiple="false" accept=".pdf"
                    :maxFileSize="1000000" @input="formData.pdf = $event.target.files[0]">
                </FileUpload>
            </div>
        </template>
        <template #footer>
            <Button severity="danger" label="Cancelar" @click="open = false" class="!h-8" icon="fa-solid fa-floppy-disk" />
            <Button severity="success" :label="formData.id != 0 ? 'Actualizar ' : 'Guardar'" :loading="false" class="!h-8"
                @click="submit()" />
        </template>
    </CustomModal>
</template>
