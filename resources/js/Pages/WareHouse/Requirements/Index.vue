<script setup>
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { useToast } from "primevue/usetoast";
import AppLayout from '@/Layouts/AppLayout.vue';
import Button from 'primevue/button';
import CustomDataTable from '@/Components/CustomDataTable.vue';
import CustomInput from '@/Components/CustomInput.vue';
import CustomModal from '@/Components/CustomModal.vue';
import RequirementSlideOver from './RequirementSlideOver.vue'

const emit = defineEmits(['materialsLoaded'])

const toast = useToast()

const props = defineProps({
    projects: Array,
    requirements: Array,
    requirement_id: Object
})

const open = ref(false)
const openSlideOver = ref(false)
const materialsLoaded = ref(false)
const requirement = ref({})

const formData = ref({
    requirement: {}
})

const materials = ref([])

const getMaterial = (requirement) => {
    axios.get(route('materials.index', requirement)).then((res) => {
        materials.value = res.data.material

        materialsLoaded.value = true
    })
}

const addItem = () => {
    // toast.add({ severity: 'success', group: 'customToast', text: 'Atividad Eliminada', life: 2000 });
    formData.value.requirement = {}
    open.value = true
}

const options = [
    {
        name: 'GRUPO 100',
        value: 100
    },
    {
        name: 'GRUPO 200',
        value: 200
    },
    {
        name: 'GRUPO 300',
        value: 300
    },
    {
        name: 'GRUPO 400',
        value: 400
    },
    {
        name: 'GRUPO 500',
        value: 500
    },
    {
        name: 'GRUPO 600',
        value: 600
    },
    {
        name: 'GRUPO 700',
        value: 700
    },
    {
        name: 'GRUPO 800',
        value: 700
    },
    {
        name: 'GRUPO 900',
        value: 900
    },
]

const columns = [
    { field: 'consecutivo', header: 'Consecutivo', filter: "true", rowClass: "underline !text-left !text-sm", filter: true, sortable: true, type: 'button', event: 'showSlide', severity: 'info', text: true, },
    { field: 'proyecto', header: 'Proyecto', filter: 'true', filterType: 'dropdown', filterLabel: 'name', filterValue: 'name', filterOptions: props.projects },
    { field: 'bloque', header: 'Bloque', filter: true },
    { field: 'grupo', header: 'Sistema/grupo', filter: true, filterOptions: options, filterLabel: 'name', filterValue: 'value', filterType: 'dropdown' },
    { field: 'dibujante', header: 'Dibujante', filter: true },
    { field: 'fecha', header: 'Fecha', filter: true, },
];

const gestion = (event, data) => {
    router.get(route('manage.requirements', { requirements: data.map((x) => x.id) }))
}

const submit = () => {
    router.post(route('requirements.store'), formData.value.requirement, {
        preserveScroll: true,
        onSuccess: (res) => {
            open.value = false
            toast.add({ severity: 'success', group: 'customToast', text: 'Atividad Eliminada', life: 2000 });

        },
        onError: (errors) => {
            toast.add({ severity: 'error', group: 'customToast', text: 'Ocurrio un Error', life: 2000 });
            // toast('Hubo un error al crear el contrato', 'error')
        },
        onFinish: visit => {
            loading.value = false
        }
    })
}

const showClick = (event, data) => {
    // console.log(data)
    requirement.value = data;
    openSlideOver.value = true
    getMaterial(data.id)
}

const url = [
    {
        ruta: 'requirements.index',
        label: 'Requerimientos',
        active: true
    }
]

onMounted(() => {
    if (props.requirement_id) {
        requirement.value = props.requirements.filter(requirement => requirement.id == props.requirement_id)[0]
        openSlideOver.value = true
        getMaterial(props.requirement_id)
    }
});


</script>

<template>
    <AppLayout :href="url">
        <div class="size-full">
            <!-- <div class="flex justify-between items-center px-4">
                <span class="text-xl font-bold text-primary  items-center flex">
                    <p> Requerimientos de Materiales </p>
                </span>
                <div class="flex items-center space-x-2">

                </div>
            </div> -->
            <CustomDataTable :data="requirements" title="Requerimiento de Materiales" @selectionAction="gestion"
                :columnas="columns" :rowsDefault="10" selectionMode="multiple" @showSlide="showClick">
                <template #buttonHeader>
                    <Button label="Importar Requerimientos" severity="success" icon="fa-solid fa-plus"
                        @click="addItem" />
                </template>
            </CustomDataTable>
        </div>

        <CustomModal v-model:visible="open" width="100vh">
            <template #icon>
                <i class="fa-solid fa-file-contract"></i>
            </template>

            <template #titulo>
                <p>Importar Requerimientos</p>
            </template>

            <template #body>
                <span class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <CustomInput type="dropdown" optionLabel="name" optionValue="id" :options="projects"
                        label="Proyecto" placeholder="Selecione un proyecto" id="bloque"
                        v-model:input="formData.requirement.project_id" :invalid="$attrs.errors.project_id != null"
                        :errorMessage="$attrs.errors.project_id">
                    </CustomInput>
                    <CustomInput label="Bloque" placeholder="Escriba Bloque" id="bloque" type="number"
                        v-model:input="formData.requirement.bloque" :invalid="$attrs.errors.bloque != null"
                        :errorMessage="$attrs.errors.bloque">
                    </CustomInput>
                    <CustomInput label="Grupo/Sistema" placeholder="Escriba El grupo o sistema" id="grupo"
                        v-model:input="formData.requirement.sistema_grupo"
                        :invalid="$attrs.errors.sistema_grupo != null" :errorMessage="$attrs.errors.sistema_grupo">
                    </CustomInput>
                    <CustomInput label="Documento de Referencia" placeholder="Escriba Documento de Referencia"
                        id="document" v-model:input="formData.requirement.document"
                        :invalid="$attrs.errors.document != null" :errorMessage="$attrs.errors.document">
                    </CustomInput>
                    <CustomInput class="col-span-2" v-model:input="formData.requirement.data"
                        label="Adjuntar Requerimientos" type="file"
                        acceptFile="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                        id="data" :invalid="$attrs.errors.data != null" :errorMessage="$attrs.errors.data">
                    </CustomInput>
                    <CustomInput class="mt-2 col-span-3" label="Notas" placeholder="Escriba la Nota del requerimiento"
                        type="textarea" v-model:input="formData.requirement.note" :invalid="$attrs.errors.note != null"
                        :errorMessage="$attrs.errors.note">
                    </CustomInput>
                </span>
            </template>

            <template #footer>
                <Button severity="danger" @click="open = false">Cancelar</Button>
                <Button severity="success" :loading="false" @click="submit()">
                    Guardar
                </Button>
            </template>
        </CustomModal>

        <RequirementSlideOver :requirement="requirement" :materials :key="requirement.id" :show="openSlideOver"
            @closeSlideOver="openSlideOver = false" :materialsLoaded />
    </AppLayout>
</template>
