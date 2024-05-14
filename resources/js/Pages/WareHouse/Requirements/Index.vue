<script setup>
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import { useToast } from "primevue/usetoast"
import AppLayout from '@/Layouts/AppLayout.vue'
import Button from 'primevue/button'
import CustomDataTable from '@/Components/CustomDataTable.vue'
import CustomInput from '@/Components/CustomInput.vue'
import CustomModal from '@/Components/CustomModal.vue'
import RequirementSlideOver from './RequirementSlideOver.vue'
import { usePermissions } from '@/composable/permission'
import axios from 'axios'
const { hasRole, hasPermission } = usePermissions()

// const emit = defineEmits(['materialsLoaded'])

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
    id: 0,
    requirement: {
        materials: [
            {
                codigo_material: '',
                cantidad: '',
                material: ''
            }
        ]
    }
})

const searchMaterial = (index) => {
    if (formData.value.requirement.materials[index].codigo_material) {
        axios.get(route('get.materialsap', {
            codigo: formData.value.requirement.materials[index].codigo_material
        })).then((res) => {
            formData.value.requirement.materials[index].material = res.data.material
        })
    }

    // console.log('En busqueda del material: ' + formData.value.requirement.materials[index].codigo_material)
}

const addItem = () => {
    // toast.add({ severity: 'success', group: 'customToast', text: 'Atividad Eliminada', life: 2000 });
    formData.value.requirement = {
        materials: [
            {
                codigo_material: '',
                cantidad: '',
                material: ''
            }
        ]
    }
    open.value = true
}

const process = [
    {
        id: 1,
        name: 'Proceso 1'
    },
    {
        id: 2,
        name: 'Proceso 2'
    },
    {
        id: 3,
        name: 'Proceso 3'
    },
]

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

const SWBS = [
    {
        id: 0,
        name: '100 - Casco y Estructura',
    },
    {
        id: 1,
        name: '200 - Propulsión',
    },
    {
        id: 2,
        name: '300 - Electricidad y Electrónica',
    },
    {
        id: 3,
        name: '400 - Electricidad y Electrónica',
    },
    {
        id: 4,
        name: '500 - Sistemas Auxiliares',
    },
    {
        id: 5,
        name: '600 - Habitabilidad y Matcom',
    },
    {
        id: 6,
        name: '800 - Maniobras y Soporte a la Producción',
    }
]

const columns = [
    { field: 'consecutivo', header: 'Consecutivo', filter: "true", rowClass: "underline !text-left", filter: true, sortable: true, type: 'button', event: 'showSlide', severity: 'info', text: true, },
    { field: 'proyecto', header: 'Proyecto', filter: 'true', filterType: 'dropdown', filterLabel: 'name', filterValue: 'name', filterOptions: props.projects },
    { field: 'bloque', header: 'Bloque', filter: true },
    { field: 'grupo', header: 'Sistema/grupo', filter: true, filterOptions: options, filterLabel: 'name', filterValue: 'value', filterType: 'dropdown' },
    { field: 'dibujante', header: 'Dibujante', filter: true },
    { field: 'fecha', header: 'Fecha', filter: true, },
    {
        field: 'estado', header: 'Estado', filter: true, type: 'tag', filtertype: 'EQUALS',
        severitys: [
            { text: 'Por Aprobar', severity: 'danger', class: 'text-red-800' },
            { text: 'Aprobado DEIPR', severity: 'primary', class: '' },
            { text: 'Oficial', severity: 'success', class: '' },
        ]
    },
];

const gestion = (event, data) => {
    console.log(data)
    router.get(route('manage.requirements', { requirements: data.map((x) => x.id) }))
}

//#region Modal's Functions
const submit = () => {
    router.post(route('requirements.store'), formData.value.requirement, {
        preserveScroll: true,
        onSuccess: (res) => {
            open.value = false
            toast.add({ severity: 'success', group: 'customToast', text: 'Actividad Eliminada', life: 2000 });

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

const addMaterial = () => {
    formData.value.requirement.materials.push({
        codigo_material: '',
        cantidad: '',
    })
}

const removeMaterial = (material_id) => {
    if (formData.value.requirement.materials.length !== 1)
        formData.value.requirement.materials.splice(material_id, 1)
}
//#endregion

const showClick = (event, data) => {
    if (data !== undefined) {
        {
            requirement.value = data;
            openSlideOver.value = true
        }
    }
    else if (!hasPermission('gestionar materiales')) {
        // console.log(event);
        requirement.value = event.data;
        openSlideOver.value = true
    }
}

onMounted(() => {
    if (props.requirement_id) {
        requirement.value = props.requirements.filter(requirement => requirement.id == props.requirement_id)[0]
        openSlideOver.value = true
    }
})

const url = [
    {
        ruta: 'requirements.index',
        label: 'Requerimientos',
        active: true
    }
]
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
                :columnas="columns" :rowsDefault="10"
                :selectionMode="hasPermission('gestionar materiales') ? 'multiple' : 'single'" @rowClic="showClick"
                @showSlide="showClick">
                <template #buttonHeader>
                    <Button label="Importar Requerimientos" severity="success" icon="fa-solid fa-plus"
                        @click="addItem()" />
                </template>
            </CustomDataTable>
        </div>

        <CustomModal v-model:visible="open" width="80vw">
            <template #icon>
                <i class="fa-solid fa-file-contract"></i>
            </template>

            <template #titulo>
                <p class="text-xl font-semibold">Importar Requerimientos</p>
            </template>

            <template #body>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!--Proyecto-->
                    <CustomInput type="dropdown" optionLabel="name" optionValue="id" :options="projects"
                        label="Proyecto" placeholder="Selecione un proyecto" id="bloque"
                        v-model:input="formData.requirement.project_id" :invalid="$attrs.errors.project_id != null"
                        :errorMessage="$attrs.errors.project_id" />
                    <!--Bloque-->
                    <CustomInput type="number" label="Bloque" placeholder="Escriba Bloque" id="bloque"
                        v-model:input="formData.requirement.bloque" :invalid="$attrs.errors.bloque != null"
                        :errorMessage="$attrs.errors.bloque" />
                    <!--SWBS-->
                    <CustomInput type="dropdown" optionLabel="name" optionValue="id" :options="SWBS" id="swbs"
                        label="SWBS" placeholder="Selecione SWBS" v-model:input="formData.requirement.SWBS"
                        :invalid="$attrs.errors.sistema_grupo != null" :errorMessage="$attrs.errors.sistema_grupo" />
                    <!--Documento de Referencia-->
                    <CustomInput label="Documento de Referencia" placeholder="Escriba Documento de Referencia"
                        id="document" v-model:input="formData.requirement.document"
                        :invalid="$attrs.errors.document != null" :errorMessage="$attrs.errors.document" />
                    <CustomInput type="dropdown" optionLabel="name" optionValue="id" :options="process" id="proceso"
                        label="Proceso" placeholder="Selecione un proceso" v-model:input="formData.requirement.proceso"
                        :invalid="$attrs.errors.proceso != null" :errorMessage="$attrs.errors.proceso" />
                    <CustomInput type="dropdown" optionLabel="name" optionValue="id" :options="process" id="proceso"
                        label="Sistema" placeholder="Selecione un sistema" v-model:input="formData.requirement.sistema"
                        :invalid="$attrs.errors.sistema != null" :errorMessage="$attrs.errors.sistema" />
                    <div class="col-span-3 w-full">
                        <div class="grid grid-cols-3 gap-4">
                            <!--Notas-->
                            <CustomInput class="col-span-2" label="Notas"
                                placeholder="Escriba la Nota del requerimiento" type="textarea"
                                v-model:input="formData.requirement.note" :invalid="$attrs.errors.note != null"
                                :errorMessage="$attrs.errors.note" />
                            <!--File-->
                            <CustomInput type="file" v-model:input="formData.requirement.data"
                                label="Adjuntar Requerimientos"
                                acceptFile="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                                id="data" :invalid="$attrs.errors.data != null" :errorMessage="$attrs.errors.data" />
                        </div>
                    </div>
                    <div class="col-span-3 space-y-2 border border-slate-300 rounded-lg p-2 ">
                        <div class="flex space-x-4 justify-center items-center w-full bg-yellow-200 rounded-lg p-1">
                            <h3 class="text-lg text-gray-800 font-bold">Materiales</h3>
                            <Button @click="addMaterial()" severity="success" icon="fa-solid fa-plus" :pt="{
                                root: '!size-6'
                            }" />
                        </div>
                        <div class="h-80 overflow-y-auto space-y-2">
                            <div v-for="(material, index) in formData.requirement.materials" :key="index"
                                class="w-full border-b border-slate-300">
                                <div class="flex w-full space-x-2 items-end">
                                    <div class="w-full pr-2 border-r border-slate-300">
                                        <label v-if="index == 0" class="text-center">
                                            Codigo de material
                                        </label>
                                        <CustomInput @focusout="searchMaterial(index)"
                                            placeholder="Escriba Código del Material" :id="'codigo_material_' + index"
                                            v-model="material.codigo_material"
                                            :invalid="material.errors && material.errors.codigo_material != null"
                                            :errorMessage="material.errors && material.errors.codigo_material" />
                                    </div>
                                    <div class="flex space-x-4">
                                        <!-- <label v-if="index == 0" class="text-center">Cantidad</label> -->
                                        <CustomInput type="number" label="Cantidad" placeholder="0"
                                            :id="'cantidad_' + index" v-model="material.cantidad"
                                            :invalid="material.errors && material.errors.cantidad != null"
                                            :errorMessage="material.errors && material.errors.cantidad" />
                                        <CustomInput type="number" label="Valor Unitario" mode="currency"
                                            placeholder="0" :id="'cantidad_' + index" v-model="material.valor_unitario"
                                            :invalid="material.errors && material.errors.valor_unitario != null"
                                            :errorMessage="material.errors && material.errors.valor_unitario" />
                                    </div>
                                    <div v-if="formData.requirement.materials.length !== 1">
                                        <Button @click="removeMaterial(index)" severity="danger"
                                            icon="fa-solid fa-minus" class="h-6" />
                                    </div>
                                </div>
                                <span class="text-xs italic text-gray-500">
                                    {{ material.material?.MAKTX ?? '' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <template #footer>
                <Button label="Cancelar" severity="danger" icon="fa fa-circle-xmark" @click="open = false" />
                <Button label="Guardar" severity="success" icon="pi pi-save" :loading="false" @click="submit()" />
            </template>
        </CustomModal>

        <RequirementSlideOver :requirement :materials :key="requirement.id" :show="openSlideOver"
            @closeSlideOver="openSlideOver = false" />
    </AppLayout>
</template>
