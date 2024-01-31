<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import CustomDataTable from '@/Components/CustomDataTable.vue';
import CustomModal from '@/Components/CustomModal.vue';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import CustomInput from '@/Components/CustomInput.vue';
import Toast from 'primevue/toast';
import { useToast } from "primevue/usetoast";
const toast = useToast();

const props = defineProps({
    categories: {
        type: Array,
        default: []
    },
    subgroups: {
        type: Array,
        default: []
    }, groups: {
        type: Array,
        default: []
    }
});

const modalVisible = ref(false)

const form = ref({
    errors: {}
})

const columnas = [
    { field: 'name', header: 'Nombre', filter:true },
    { field: 'padre.name', header: 'Sub Grupo' },
    { field: 'padre.letter', header: 'Letra' },
    { field: 'padre.padre.name', header: 'Grupo', sortable: true },
    { field: 'padre.padre.letter', header: 'Letra' },
]

const actions = [
    { event: 'edit', severity: 'warning', icon: 'fa-solid fa-pencil', text: true, outlined: false, rounded: false },
]

const modalType = ref('Nuevo')

const showModal = (event, data) => {
    if (data == null) {
        modalType.value = 'Nuevo'
        form.value = { error: false, errors: {} }
        form.value.level = 'Grupo'
        modalVisible.value = true
    } else {
        form.value = { error: false, errors: {} }
        modalType.value = 'Editar'
        form.value.id=data.id
        form.value.name = data.name
        form.value.level = data.level
        form.value.group = data.padre.padre
        form.value.letter = data.letter
        form.value.sub_group = data.padre
        delete form.value.sub_group.padre
        form.calibration = data.calibration == 0 ? false : true
        modalVisible.value = true
    }
}

const save = () => {
    form.value.error = false
    form.value.errors = {}
    if (form.value.name == null || form.value.name == '') {
        form.value.error = true
        form.value.errors.name = 'Debe escribir un nombre'
    }
    if ((form.value.letter == null || form.value.letter == '') && form.value.level != 'Descripcion') {
        form.value.error = true
        form.value.errors.letter = 'Debe escribir una letra'
    }
    if (form.value.level == 'Sub Grupo' && form.value.group == null) {
        form.value.error = true
        form.value.errors.group = 'Debe escoger un grupo'
    }
    if (form.value.level == 'Descripcion' && (form.value.group == null || form.value.sub_group == null)) {
        form.value.error = true
        form.value.errors.group = form.value.group == null ? 'Debe escoger un grupo' : null
        form.value.errors.sub_group = form.value.sub_group == null ? 'Debe escoger un sub grupo' : null
    }
    if (!form.value.error) {
        form.value.loading = true
        form.value.category_id = form.value.level == 'Grupo' ? 0 : form.value.level == 'Sub Grupo' ? form.value.group.id : form.value.sub_group.id

        if (modalType.value == 'Nuevo') {
            router.post(route('categories.store'), form.value, {
                onSuccess: () => {
                    form.value.loading = false
                    toast.add({ summary: 'Guardado', life: 2000 });
                    modalVisible.value = false
                },
                onError: (e) => {
                    form.value.loading = false
                    toast.add({ summary: 'Error al guardar', life: 2000 });
                    form.value.error = true
                    form.value.errors = e
                }
            })
        } else {
            router.put(route('categories.update', form.value.id),form.value, {
                onSuccess: () => {
                    form.value.loading = false
                    toast.add({ summary: 'Actualizado', life: 2000 });
                    modalVisible.value = false
                },
                onError: () => {
                    form.value.loading = false
                    toast.add({ summary: 'Error al actualizar', life: 2000 });
                    form.value.error = true
                    form.value.errors = e
                }
            })
        }
    } else {
        toast.add({ summary: 'Error al guardar', life: 1500 });
    }
}

</script>

<template>
    <AppLayout>
        <div class="h-[89vh] overflow-y-auto">
            <CustomDataTable :rowsDefault="20" :data="categories" title="Categorias" :columnas="columnas" :actions="actions"
                @edit="showModal">
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
                {{ modalType }}
            </span>
        </template>
        <template #body>
            <span class="grid grid-cols-2 gap-2">
                <CustomInput label="Tipo" v-model:input="form.level" type="dropdown" placeholder="Selecciona una categoria"
                    :options="['Grupo', 'Sub Grupo', 'Descripcion']"></CustomInput>
                <CustomInput label="Grupo" id="group" v-if="form.level != 'Grupo'" v-model:input="form.group" type="dropdown"
                    placeholder="Selecciona un grupo" :options="groups" optionLabel="name"
                    :invalid="form.errors.group ? true : false" :errorMessage="form.errors.group"></CustomInput>
                <CustomInput label="Sub Grupo" id="subgroup" v-if="form.level != 'Grupo' && form.level != 'Sub Grupo'"
                    v-model:input="form.sub_group" type="dropdown" placeholder="Selecciona una categoria"
                    :options="subgroups.filter((subgroup)=>subgroup.category_id==form.group.id)" optionLabel="name" :invalid="form.errors.sub_group ? true : false"
                    :errorMessage="form.errors.sub_group"></CustomInput>
                <CustomInput label="Nombre" id="name" v-model:input="form.name" placeholder="Nombre para mostrar"
                    :invalid="form.errors.name ? true : false" :errorMessage="form.errors.name"></CustomInput>
                <CustomInput label="Letra" id="letter" v-if="form.level == 'Grupo' || form.level == 'Sub Grupo'"
                    v-model:input="form.letter" :invalid="form.errors.letter ? true : false"
                    :errorMessage="form.errors.letter" placeholder="Letra que lo identificara">
                </CustomInput>
                <CustomInput label="Requiere calibracion?" v-if="form.level == 'Descripcion'" type="tooglebutton"
                    v-model:input="form.calibration"></CustomInput>
            </span>
        </template>
        <template #footer>
            <Button severity="primary" outlined :label="modalType=='Nuevo'?'Guardar':'Actualizar'" icon="fa-solid fa-floppy-disk"
                @click="save()" :loading="form.loading" />
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