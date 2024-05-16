<script setup>
import { ref, onMounted } from 'vue'
import { useForm } from '@inertiajs/vue3'
import CustomDataTable from '@/Components/CustomDataTable.vue'
import CustomModal from '@/Components/CustomModal.vue'
import CustomInput from '@/Components/CustomInput.vue'

const openSystemModal = ref(false)
const loading = ref(false)
const groups = ref([])

const formData = useForm({
  id: null,
  constructive_group: '',
  code: '',
  name: ''
})

const constructive_groups = [
  {
    id: 0,
    name: '000'
  },
  {
    id: 1,
    name: '100'
  },
  {
    id: 2,
    name: '200'
  },
  {
    id: 3,
    name: '300'
  },
  {
    id: 4,
    name: '400'
  },
  {
    id: 5,
    name: '500'
  },
  {
    id: 6,
    name: '600'
  },
  {
    id: 7,
    name: '700'
  },
  {
    id: 8,
    name: '800'
  },
  {
    id: 9,
    name: '900'
  }
]

//#region CustomDatatable
const columns = [
  { field: 'consecutivo', header: 'Grupo Constructivo', filter: true },
  { field: 'proyecto', header: 'Código', filter: true },
  { field: 'bloque', header: 'Nombre', filter: true }
]

const buttons = [
  { event: 'editClic', severity: 'warning', class: '', icon: 'fa-solid fa-pencil', text: true, outlined: false, rounded: false },
  { event: 'deleteClic', severity: 'danger', icon: 'fa-regular fa-trash-can', class: '!h-8', text: true, outlined: false, rounded: false },
]
//#endregion

const getConstructivesGroups = () => {
  loading.value = true
  axios.get(route('gruposConstructivos.index'))
    .then((res) => {
      groups.value = res.data[0].map((value) => {
        return {
          ...value,
          name: value.name + '. ' + value.descripcion,
        }
      })
      loading.value = false;
    })
}

const addSystem = () => {
  openSystemModal.value = true
}

onMounted(() => {
  getConstructivesGroups()
})
</script>
<template>
  <div class="size-full overflow-y-auto">
    <CustomDataTable :data="systems" :rows-default="100" :columnas="columns" :actions="buttons" :loading>
      <template #buttonHeader>
        <Button @click="addSystem" severity="success" icon="fa-solid fa-plus" label="Agregar" outlined />
      </template>
    </CustomDataTable>
  </div>

  <CustomModal v-model:visible="openSystemModal" width="30vw">
    <template #icon>
      <i class="fa-solid fa-cloud-arrow-up text-white text-xl"></i>
    </template>
    <template #titulo>
      <p class="text-white">Añadir Sistema</p>
    </template>
    <template #body>
      <div class="flex flex-col gap-4">
        <!--Proyecto-->
        <CustomInput type="dropdown" optionLabel="name" optionValue="id" :options="groups" label="Grupo Constructivo"
          placeholder="Selecione un Grupo Constructivo" v-model:input="formData.constructive_group" />
        <!--Código-->
        <CustomInput type="number" optionLabel="name" optionValue="id" label="Código" placeholder="Escriba un código"
          v-model:input="formData.code" />
        <!--Descripción-->
        <CustomInput label="Nombre" placeholder="Escriba Nombre del Sistema" v-model:input="formData.name" />
      </div>
    </template>
    <template #footer>
      <Button label="Cancelar" severity="danger" icon="fa fa-circle-xmark" @click="openSystemModal = false" />
      <Button label="Guardar" severity="success" icon="pi pi-save" :loading="false" @click="submit()" />
    </template>
  </CustomModal>
</template>
