<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import CustomDataTable from '@/Components/CustomDataTable.vue'
import CustomModal from '@/Components/CustomModal.vue'
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import CustomInput from '@/Components/CustomInput.vue'
import Toast from 'primevue/toast'
import { useToast } from "primevue/usetoast"

const toast = useToast()
const props = defineProps({
    tools: {
        type: Array,
        default: []
    },
    categories: {
        type: Array,
        default: []
    },
})

const modalVisible = ref(false)
const modalType = ref('Nuevo')

const form = ref({
    errors: {}
})

const columnas = [
    { field: 'name', header: 'Nombre', rowClass: "underline !text-left", sortable: true, filter: true, type: 'button', event: 'goToToolOverview', severity: 'info', text: true },
    { field: 'code', header: 'Codigo', filter: true, sortable: true },
    { field: 'gerencia', header: 'Gerencia', filter: true, sortable: true },
    { field: 'serial', header: 'Serial', filter: true, sortable: true },
    { field: 'estado_operativo', header: 'Operatividad', filter: true, sortable: true },
    { field: 'estado', header: 'Disponibilidad', filter: true, sortable: true },
]

const filterButtons = [
    { field: 'estado_operativo', label: 'OPERATIVA', data: 'OPERATIVA', severity: 'success' },
    { field: 'estado_operativo', label: 'CON LIMITACIONES', data: 'CON LIMITACIONES', severity: 'warning' },
    { field: 'estado_operativo', label: 'FUERA DE SERVICIO', data: 'FUERA DE SERVICIO', severity: 'danger' },
    { field: 'estado_operativo', label: 'BAJA ', data: 'BAJA', severity: 'danger' },
]

const actions = [
    { event: 'edit', severity: 'warning', icon: 'fa-solid fa-pencil', text: true, outlined: false, rounded: false },]

const showModal = (event, data) => {
    if (data == null) {
        modalType.value = 'Nuevo'
        form.value = { error: false, errors: {} }
        form.value.is_small = false
        form.value.estado = 'DISPONIBLE'
        form.value.estado_operativo = 'OPERATIVA'
        modalVisible.value = true
    } else {
        form.value = { error: false, errors: {} }
        modalType.value = 'Editar'
        form.value.id = data.id
        form.value.category = data.category
        form.value.is_small = data.is_small == 0 ? false : true
        form.value.serial = data.serial
        form.value.SAP_code = data.SAP_code
        form.value.value = parseInt(data.value)
        form.value.brand = data.brand
        form.value.entry_date = data.entry_date
        form.value.estado_operativo = data.estado_operativo
        form.value.description = data.description
        form.value.imagen = data.imagen
        modalVisible.value = true
    }
}

const goToToolOverview = (event, data) => {
    try {
        router.get(route('tools.show', data.id))
    } catch (error) {
        console.log(error)
    }
}

const save = () => {
    form.value.error = false
    form.value.errors = {}
    form.value.loading = true
    if (form.value.category) {
        form.value.category_id = form.value.category.id
        if (modalType.value == 'Nuevo') {
            router.post(route('tools.store'), form.value, {
                onSuccess: () => {
                    form.value.loading = false
                    toast.add({ summary: 'Guardado', life: 2000 })
                    modalVisible.value = false
                },
                onError: (e) => {
                    form.value.loading = false
                    toast.add({ summary: 'Error al guardar', life: 2000 })
                    form.value.error = true
                    form.value.errors = e
                }
            })
        } else {
            router.put(route('tools.update', form.value.id), form.value, {
                onSuccess: () => {
                    form.value.loading = false
                    toast.add({ summary: 'Actualizado', life: 2000 })
                    modalVisible.value = false
                },
                onError: (e) => {
                    form.value.loading = false
                    toast.add({ summary: 'Error al actualizar', life: 2000 })
                    form.value.error = true
                    form.value.errors = e
                }
            })
        }
    } else {
        form.value.error = true
        form.value.errors.category = "Seleccione una categoria"
        toast.add({ summary: 'Seleccione una categoria', life: 2000 })
        form.value.loading = false
    }
}

</script>

<template>
    <AppLayout>
        <div class="w-full h-[89vh] overflow-y-auto">
            <CustomDataTable :rowsDefault="100" title="Herramientas y equipos" :data="tools" :columnas="columnas"
                :actions="actions" @edit="showModal" @goToToolOverview="goToToolOverview" :filterButtons="filterButtons">
                <template #buttonHeader>
                    <Button label="Nuevo" severity="success" icon="fa-solid fa-plus" @click="showModal" />
                </template>
            </CustomDataTable>
        </div>
    </AppLayout>
    <CustomModal v-model:visible="modalVisible">
        <template #icon>
            <i class="fa-solid text-white fa-table-list"></i>
        </template>
        <template #titulo>
            <span class="text-lg font-bold text-white">
                {{ modalType }} Equipo
            </span>
        </template>
        <template #body>
            <section class="grid grid-cols-3 gap-2">
                <CustomInput label="Descripción" id="category" v-model:input="form.category" type="dropdown"
                    placeholder="Selecciona una Descripción" :options="categories" optionLabel="name"
                    :invalid="form.errors.category ? true : false" :errorMessage="form.errors.category" />
                <CustomInput label="Serial" id="serial" v-model:input="form.serial" placeholder="Serial del equipo"
                    :invalid="form.errors.serial ? true : false" :errorMessage="form.errors.serial" />
                <CustomInput label="Codigo SAP" id="SAP_code" v-model:input="form.SAP_code" placeholder="Codigo SAP"
                    :invalid="form.errors.SAP_code ? true : false" :errorMessage="form.errors.SAP_code" />
                <CustomInput label="Valor" id="value" type="number" mode="currency" v-model:input="form.value"
                    placeholder="Valor del equipo" :invalid="form.errors.value ? true : false"
                    :errorMessage="form.errors.value" />

                <CustomInput label="Marca" id="brand" v-model:input="form.brand" placeholder="Marca del equipo"
                    :invalid="form.errors.brand ? true : false" :errorMessage="form.errors.brand" />

                <CustomInput label="Fecha de entrada" id="entry_date" v-model:input="form.entry_date" type="date"
                    placeholder="Selecciona una categoria" :options="categories" optionLabel="name"
                    :invalid="form.errors.entry_date ? true : false" :errorMessage="form.errors.entry_date" />

                <CustomInput label="Es pequeño?" type="tooglebutton" id="is_small" v-model:input="form.is_small"
                    placeholder="Nombre para mostrar" :invalid="form.errors.is_small ? true : false"
                    :errorMessage="form.errors.is_small" />

                <CustomInput label="estado_operativo" id="estado_operativo" v-model:input="form.estado_operativo"
                    type="dropdown" placeholder="Selecciona un estado_operativo"
                    :options="['OPERATIVA', 'CON LIMITACIONES', 'FUERA DE SERVICIO', 'BAJA']"
                    :invalid="form.errors.estado_operativo ? true : false" :errorMessage="form.errors.estado_operativo" />

                <CustomInput label="Imagen" type="file" id="imagen" v-model:input="form.imagen"
                    placeholder="Nombre para mostrar" :invalid="form.errors.imagen ? true : false"
                    :errorMessage="form.errors.imagen" />
            </section>
            <CustomInput label="Descripción" type="textarea" id="description" v-model:input="form.description"
                placeholder="Descripcion de la herramienta" :invalid="form.errors.description ? true : false"
                :errorMessage="form.errors.description" />
        </template>
        <template #footer>
            <Button severity="primary" outlined :label="modalType == 'Nuevo' ? 'Guardar' : 'Actualizar'"
                icon="fa-solid fa-floppy-disk" @click="save()" :loading="form.loading" />
            <Button severity="danger" outlined label="Cancelar" icon="fa-regular fa-circle-xmark"
                @click="modalVisible = false" />
        </template>
    </CustomModal>
    <Toast position="bottom-center" :pt="{
        root: '!h-10 !w-64',
        container: {
            class: form.error ? '!bg-danger !h-10 !rounded-lg' : '!bg-primary !h-10 !rounded-lg'
        },
        content: '!h-10 !p-0 !flex !items-center !text-center !text-white ',
        buttonContainer: '!hidden',
        icon: '!hidden',
        detail: '!hidden'
    }" />
</template>
