<script setup>
const { confirmDelete } = useSweetalert()
const { currencyFormat } = commonUtilities()
const { eventListener } = useBroadcastNotifications()
const { hasRole, hasPermission } = usePermissions();
const { toast } = useSweetalert()
import { commonUtilities } from '@/composable/commonUtilities';
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { useBroadcastNotifications } from '@/composable/broadcastNotifications'
import { usePermissions } from '@/composable/permission';
import { useSweetalert } from '@/composable/sweetAlert'
import AppLayout from '@/Layouts/AppLayout.vue'
import ContractsSlideOver from '@/Components/ContractsSlideOver.vue'
import CustomDataTable from '@/Components/CustomDataTable.vue'
import CustomInput from '@/Components/CustomInput.vue'
import CustomModal from '@/Components/CustomModal.vue'

const loading = ref(false)

//Props
const props = defineProps({
    contracts: Array,
    customers: Array,
    quotes: Object
})

const typeOfSaleOptions = ref(['VENTA DIRECTA', 'FINANCIADA', 'LEASING'])
const stateOptions = ref(['LIQUIDADO', 'EN EJECUCIÓN'])

//Abrir Modal
const open = ref(false)
//#endregion

//#region UseForm
const formData = ref({
    contract: {}
})
//#endregion

/*SUBMIT*/
const submit = () => {
    loading.value = true
    formData.value.contract.quote_id = formData.value.contract.quote?.id ?? null
    if (!formData.value.contract.id) {
        router.post(route('contracts.store'), formData.value.contract, {
            preserveScroll: true,
            onSuccess: (res) => {
                open.value = false
                toast(' Contrato creado exitosamente', 'success')
            },
            onError: (errors) => {
                toast('Hubo un error al crear el contrato', 'error')
            },
            onFinish: visit => {
                loading.value = false
            }
        })
        return 'creado'
    }
    router.put(route('contracts.update', formData.value.contract.id), formData.value.contract, {
        preserveScroll: true,
        onSuccess: (res) => {
            open.value = false
            toast('Contrato actualizado exitosamente!', 'success')
        },
        onError: (errors) => {
            console.log(errors)
            toast('¡Ups! Ha surgido un error', 'error')
        },
        onFinish: visit => {
            loading.value = false
        }
    })
}

const addItem = () => {
    formData.value.contract = {}
    open.value = true
}

const editItem = (event, contract) => {
    console.log(contract)
    formData.value.contract = contract
    open.value = true
}

const del = (event, data) => {
    confirmDelete(data.id, 'Contrato', 'contracts')
}

const columnas = [
    { field: 'contract_id', header: 'Contrato ID', filter: true, sortable: true },
    { field: 'project_count', header: '# Proyectos', filter: true, sortable: true, rowclass: "underline", type: 'button', severity: 'info', text: true },
    { field: 'quote.consecutive', header: 'Estimacion', filter: true, sortable: true },
    // { field: 'quote.customer.name', header: 'Cliente', filter: true, sortable: true },
    { field: 'start_date', header: 'Fecha Inicio', filter: true, sortable: true, type: 'date' },
    { field: 'end_date', header: 'Fecha Finalización', filter: true, sortable: true, type: 'date' },
    { field: 'total_cost', header: 'Costo', filter: true, sortable: true, type: 'currency' },
    // { field: 'state', header: 'Estado del contrato', filter: true, sortable: true },
    {
        field: 'state', header: 'Estado', filter: true, sortable: true, type: 'tag', filtertype: 'EQUALS',
        severitys: [
            { text: 'EN EJECUCIÓN', severity: 'primary', class: '' },
            { text: 'LIQUIDADO', severity: 'success', class: '' },
        ]
    },
    { field: 'subject', header: 'Objeto del Contrato', filter: true, sortable: true },
]

const buttons = [
    { event: 'edit', severity: 'primary', class: '', icon: 'fa-solid fa-pencil', text: true, outlined: false, rounded: false, show: hasPermission('contract edit') },
    { event: 'delete', severity: 'danger', class: '', icon: 'fa-regular fa-trash-can', text: true, outlined: false, rounded: false, show: hasPermission('contract delete') },
]

const contractData = ref({})
const openSlideOver = ref(false)

const showClic = (event) => {
    console.log(event.data)
    contractData.value = event.data;
    openSlideOver.value = true
}
//#region broadcast
// eventListener('contracts', '.ContractsEvent')
//#endregion
</script>

<template>
    <AppLayout>
        <div class="h-[89vh] overflow-y-auto">
            <CustomDataTable :data="contracts" :rowsDefault="20" title="Contratos" :columnas="columnas"
                :actions="buttons" @edit="editItem" @delete="del" @rowClic="showClic">
                <template #buttonHeader>
                    <Button @click="addItem()" severity="success" icon="fa-solid fa-plus" outlined label="Nuevo"
                        v-if="hasPermission('contract create')" />
                </template>
            </CustomDataTable>
        </div>

        <!--MODAL DE FORMULARIO-->
        <CustomModal v-model:visible="open">

            <template #icon>
                <i class="fa-solid fa-file-contract"></i>
            </template>

            <template #titulo>
                <p>{{ formData.contract.id != 0 ? 'Editar ' : 'Crear ' }} Contrato</p>
            </template>

            <template #body>
                <span class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <CustomInput label="Contrato ID" placeholder="Escriba ID del Contrato" id="contrato_id"
                        v-model:input="formData.contract.contract_id" :invalid="$attrs.errors.contract_id != null"
                        :errorMessage="$attrs.errors.contract_id">
                    </CustomInput>

                    <CustomInput label="Tipo de Venta" :options="typeOfSaleOptions" type="dropdown" id="tipo_venta"
                        placeholder="Seleccione un Tipo de Venta" v-model:input="formData.contract.type_of_sale"
                        :invalid="$attrs.errors.type_of_sale != null" :errorMessage="$attrs.errors.type_of_sale">
                    </CustomInput>

                    <CustomInput label="Supervisor" placeholder="Escriba nombre del supervisor" id="supervisor"
                        v-model:input="formData.contract.supervisor" :invalid="$attrs.errors.supervisor != null"
                        :errorMessage="$attrs.errors.supervisor">
                    </CustomInput>

                    <CustomInput type="date" label="Fecha de inicio" placeholder="Escriba el Tipo de Cliente"
                        id="fecha_inicio" v-model:input="formData.contract.start_date"
                        :invalid="$attrs.errors.start_date != null" :errorMessage="$attrs.errors.start_date">
                    </CustomInput>

                    <CustomInput type="date" label="Fecha de Finalización" placeholder="Fecha fin del contrato"
                        id="fecha_final" v-model:input="formData.contract.end_date"
                        :invalid="$attrs.errors.end_date != null" :errorMessage="$attrs.errors.end_date">
                    </CustomInput>

                    <CustomInput label="Estado del Contrato" :options="stateOptions" type="dropdown"
                        id="estado_contrato" placeholder="Seleccione un Tipo de Venta"
                        v-model:input="formData.contract.state" :invalid="$attrs.errors.state != null"
                        :errorMessage="$attrs.errors.state">
                    </CustomInput>

                    <CustomInput label="Oferta" placeholder="Seleccione la oferta" type="dropdown" id="oferta"
                        optionLabel="consecutive" :options="Object.values(quotes)"
                        v-model:input="formData.contract.quote" :invalid="$attrs.errors.quote != null"
                        :errorMessage="$attrs.errors.quote">
                    </CustomInput>

                    <CustomInput label="Adjuntar PDF" type="file" v-model:input="formData.contract.pdf"
                        acceptFile=".pdf" id="pdf" :invalid="$attrs.errors.pdf != null"
                        :errorMessage="$attrs.errors.pdf">
                    </CustomInput>
                    <span v-if="formData.contract.quote">
                        <span class="flex justify-between">
                            <p class="font-bold">Valor venta: </p>
                            <p class="text-right">{{ currencyFormat(formData.contract.quote.total_cost,
                formData.contract.quote.coin) }}</p>
                        </span>
                        <span class="flex justify-between">
                            <p class="font-bold">Cliente: </p>
                            <p class="text-right">{{ formData.contract.quote.customer?.name ?? 'Sin asignar' }}</p>
                        </span>
                    </span>
                </span>
                <CustomInput class="mt-2" label="Objeto del Contrato" placeholder="Escriba Objeto del Contrato"
                    type="textarea" v-model:input="formData.contract.subject" :invalid="$attrs.errors.subject != null"
                    :errorMessage="$attrs.errors.subject">
                </CustomInput>
            </template>

            <template #footer>
                <Button severity="danger" @click="open = false">Cancelar</Button>
                <Button severity="success" :loading="false" @click="submit()">
                    {{ formData.contract.id ? 'Actualizar ' : 'Guardar' }}
                </Button>
            </template>
        </CustomModal>
        <ContractsSlideOver :data="contractData" :show="openSlideOver" @closeSlideOver="openSlideOver = false" />
    </AppLayout>
</template>
