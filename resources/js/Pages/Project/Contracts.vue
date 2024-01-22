<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import { webNotifications } from '@/composable/webNotifications'
import { useSweetalert } from '@/composable/sweetAlert'
import CustomDataTable from '@/Components/CustomDataTable.vue'
import CustomModal from '@/Components/CustomModal.vue'
import CustomInput from '@/Components/CustomInput.vue'
const { toast } = useSweetalert()
const loading = ref(false)
const { confirmDelete } = useSweetalert()

const { contractNotification } = webNotifications()

//Props
const props = defineProps({
    contracts: Array,
    customers: Array
})

//#region v-models (ref())
const customerSelect = ref({})
const managerSelect = ref()
const managerOptions = ref([])

//Tipo de Venta
const typeOfSaleSelect = ref()
const typeOfSaleOptions = ref(['VENTA DIRECTA', 'FINANCIADA', 'LEASING'])

//Moneda
const currencySelect = ref()
const currencyOptions = ref(['COP', 'USD', 'EUR'])

//Estado de la venta
const stateSelect = ref()
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
    if (!formData.value.contract.id) {
        router.post(route('contracts.store'), formData.value.contract, {
            preserveScroll: true,
            onSuccess: (res) => {
                open.value = false
                toast(' Contrato creado exitosamente', 'success')
            },
            onError: (errors) => {
                toast('Por favor diligencie todos los campos', 'error')
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
            toast('¡Ups! Ha surgido un error', 'error')
        },
        onFinish: visit => {
            loading.value = false
        }
    })
}

const addItem = () => {
    formData.value.contract = {}
    clearErrors()
    open.value = true
}

const editItem = (event, contract) => {
    clearErrors()
    formData.value.contract = contract
    open.value = true
}

const clearErrors = () => {
    router.page.props.errors = {}
}

const del = (event, data) => {
    confirmDelete(data.id, 'Contrato', 'contracts')
}

const columnas = [
    { field: 'contract_id', header: 'Contrato ID', filter: true, sortable: true },
    { field: 'customer.name', header: 'Cliente', filter: true, sortable: true },
    { field: 'start_date', header: 'Fecha Inicio', filter: true, sortable: true, type: 'date' },
    { field: 'end_date', header: 'Fecha Finalización', filter: true, sortable: true, },
    { field: 'cost', header: 'Costo', filter: true, sortable: true, type: 'currency' },
]
const buttons = [
    { event: 'edit', severity: 'primary', class: '', icon: 'fa-solid fa-pencil', text: true, outlined: false, rounded: false },
    { event: 'delete', severity: 'danger', class: '', icon: 'fa-regular fa-trash-can', text: true, outlined: false, rounded: false },
]

</script>

<template>
    <AppLayout>
        <div class="w-full overflow-y-auto">
            <CustomDataTable :data="contracts" :rowsDefault="20" title="Contratos" :columnas="columnas" :actions="buttons"
                @edit="editItem" @delete="del">
                <template #buttonHeader>
                    <span>
                        <Button @click="addItem()" severity="success" icon="fa-solid fa-plus" outlined label="Nuevo">
                        </Button>
                    </span>
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
                <span class="grid grid-cols-1 md:grid-cols-3 pt-2 gap-4">
                    <CustomInput label="Contrato ID" placeholder="Escriba ID del Contrato"
                        v-model:input="formData.contract.contract_id" :errorMessage="$page.props.errors.contract_id">
                    </CustomInput>

                    <CustomInput label="Tipo de Venta" :options="typeOfSaleOptions" type="dropdown"
                        placeholder="Seleccione un Tipo de Venta" v-model:input="formData.contract.type_of_sale">
                    </CustomInput>

                    <CustomInput label="Supervisor" placeholder="Escriba nombre del supervisor"
                        v-model:input="formData.contract.supervisor" :errorMessage="$page.props.errors.supervisor">
                    </CustomInput>

                    <CustomInput type="date" label="Fecha de inicio" placeholder="Escriba el Tipo de Cliente"
                        v-model:input="formData.contract.start_date" :errorMessage="$page.props.errors.start_date">
                    </CustomInput>

                    <CustomInput type="date" label="Fecha de Finalización" placeholder="Fecha fin del contrato"
                        v-model:input="formData.contract.end_date" :errorMessage="$page.props.errors.end_date">
                    </CustomInput>

                    <CustomInput label="Estado del Contrato" :options="stateOptions" type="dropdown"
                        placeholder="Seleccione un Tipo de Venta" v-model:input="formData.contract.state"
                        :errorMessage="$page.props.errors.stateSelect">
                    </CustomInput>

                    <CustomInput label="Oferta" placeholder="Seleccione la oferta" type="dropdown" :options="stateOptions"
                        v-model:input="formData.contract.quote" :errorMessage="$page.props.errors.quote">
                    </CustomInput>

                    <CustomInput label="Adjuntar PDF" type="file" v-model:input="formData.contract.pdf" acceptFile=".pdf"
                        :errorMessage="$page.props.errors.pdf">
                    </CustomInput>
                </span>
                <CustomInput label="Objeto del Contrato" placeholder="Escriba Objeto del Contrato" type="textarea"
                    v-model:input="formData.contract.subject" :errorMessage="$page.props.errors.subject">
                </CustomInput>
            </template>
            <template #footer>
                <Button severity="danger" @click="open = false">Cancelar</Button>
                <Button severity="success" :loading="false" @click="submit()">
                    {{ formData.contract.id != 0 ? 'Actualizar ' : 'Guardar' }}
                </Button>
            </template>

        </CustomModal>

        <!-- <TransitionRoot as="template" :show="open">
            <Dialog as="div" class="relative z-30" @close="open = false">
                <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100"
                    leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                    <div class="fixed inset-0 z-30 w-screen h-screen transition-opacity bg-gray-500 bg-opacity-75" />
                </TransitionChild>
                <div class="fixed inset-0 z-50 h-screen overflow-y-auto">
                    <div class="flex items-end justify-center min-h-full p-4 text-center sm:items-center sm:p-0">
                        <TransitionChild as="template" enter="ease-out duration-300"
                            enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                            enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200"
                            leave-from="opacity-100 translate-y-0 sm:scale-100"
                            leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                            <DialogPanel
                                class="w-full px-2 pt-2 pb-4 overflow-hidden text-left transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:w-full sm:max-w-[800px]">
                                <div>
                                    <div class="px-2 mt-2 text-center">
                                        <DialogTitle as="h3" class="text-xl font-semibold text-center text-primary">
                                            {{ formData.id != 0 ? 'Editar ' : 'Crear' }}
                                            Contrato
                                        </DialogTitle>
                                        <div class="grid grid-cols-1 gap-4 p-2 md:grid-cols-2 space-y-2 rounded-lg">
                                            <div class="col-span-1 p-2 mt-2 space-y-2 text-left">
                                                <TextInput label="Contrato ID" placeholder="Escriba ID del Contrato"
                                                    v-model="formData.contract_id" :error="$page.props.errors.contract_id">
                                                </TextInput>

                                                <TextInput label="Objeto del Contrato"
                                                    placeholder="Escriba Objeto del Contrato" v-model="formData.subject"
                                                    :error="$page.props.errors.subject">
                                                </TextInput>
                                                <Combobox label="Tipo de Venta" :options="typeOfSaleOptions"
                                                    placeholder="Seleccione un Tipo de Venta" v-model="typeOfSaleSelect">
                                                </Combobox>
                                                <TextInput label="Supervisor" placeholder="Escriba nombre del supervisor"
                                                    v-model="formData.supervisor" :error="$page.props.errors.supervisor">
                                                </TextInput>
                                            </div>

                                            <div class="col-span-1 p-2 mt-2 space-y-2 text-left">
                                                <TextInput type="date" label="Fecha de inicio"
                                                    :placeholder="'Escriba el Tipo de Cliente'"
                                                    v-model="formData.start_date" :error="$page.props.errors.start_date">
                                                </TextInput>

                                                <TextInput type="date" label="Fecha de Finalización"
                                                    :placeholder="'Escriba el Tipo de Cliente'" v-model="formData.end_date"
                                                    :error="$page.props.errors.end_date">
                                                </TextInput>

                                                <Combobox label="Estado del Contrato" :options="stateOptions"
                                                    placeholder="Seleccione un Tipo de Venta" v-model="stateSelect">
                                                </Combobox>

                                                <div class="flex justify-center align-content-center pt-6">
                                                    <FileUpload chooseLabel="Adjuntar PDF" mode="basic" name="demo[]"
                                                        :multiple="false" accept=".pdf" :maxFileSize="1000000"
                                                        @input="formData.pdf = $event.target.files[0]">
                                                    </FileUpload>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="">
                                        </div>
                                    </div>
                                </div>
                                <div class="flex px-2 mt-2 space-x-4">
                                    <Button class="hover:bg-danger text-danger border-danger" severity="danger"
                                        @click="open = false">Cancelar</Button>
                                    <Button severity="success" :loading="false"
                                        class="text-success hover:bg-success border-success" @click="submit()">
                                        {{ formData.id != 0 ? 'Actualizar ' : 'Guardar' }}
                                    </Button>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot> -->
    </AppLayout>
</template>
