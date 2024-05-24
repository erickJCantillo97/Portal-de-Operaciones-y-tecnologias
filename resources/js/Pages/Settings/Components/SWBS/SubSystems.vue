<script setup>
import { ref } from 'vue'
import { useConfirm } from 'primevue/useconfirm'
import { useForm } from '@inertiajs/vue3'
import { useToast } from 'primevue/usetoast'
import CustomDataTable from '@/Components/CustomDataTable.vue'
import CustomInput from '@/Components/CustomInput.vue'
import CustomModal from '@/Components/CustomModal.vue'

// const c = console.log.bind(document)

const confirm = useConfirm()
const toast = useToast()

const openSubSystemModal = ref(false)
const loading = ref(false)
const customInputLoading = ref(false)
const system = ref([])

const formData = useForm({
  system_id: '',
  code: '',
  name: ''
})

//#region CustomDatatable
const columns = [
  { field: 'system_id', header: 'Sistema', filter: true },
  { field: 'code', header: 'Código', filter: true },
  { field: 'name', header: 'Nombre', filter: true }
]

const buttons = [
  { event: 'edit', severity: 'warning', class: '', icon: 'fa-solid fa-pencil', text: true, outlined: false, rounded: false },
  { event: 'delete', severity: 'danger', icon: 'fa-regular fa-trash-can', class: '!h-8', text: true, outlined: false, rounded: false },
]
//#endregion

const getSystems = async () => {
  loading.value = true
  customInputLoading.value = true
  await axios.get(route('systems.index'))
    .then((res) => {
      system.value = res.data.map((value) => {
        return {
          ...value,
          name: value.code + '. ' + value.name
        }
      })
      loading.value = false
      customInputLoading.value = false
    })
}

//#region CRUD
const submit = () => {
  try {
    formData.post(route('subsystem.store'), formData, {
      preserveScroll: true,
      onSuccess: () => {
        toast.add({
          severity: 'success',
          group: "customToast",
          text: `Se ha guardado el subsistema ${formData.name} correctamente`,
          life: 2000
        })
        openSubSystemModal.value = false
        saveLoading.value = true
        formData.reset()
      },
      onError: (error) => {
        toast.add({
          severity: 'error',
          group: "customToast",
          text: `Ha ocurrido un error al intentar guardar el subsistema ${formData.name}, error: ` + error,
          life: 2000
        })
      }
    })
  } catch (error) {
    console.log('Error: ' + error)
  }
}

const editSubSystem = (event, data) => {
  console.log(data)
  console.log(formData.system_id)
  formData.system_id = parseInt(data.system_id) || ''
  console.log(formData.system_id)
  formData.code = data.code || ''
  formData.name = data.name || ''
  openSubSystemModal.value = true
}

const deleteSubSystem = (event, subsystem_id) => {
  confirm.require({
    target: event.currentTarget,
    message: {
      message: '¿Está seguro de eliminar este subsistema?',
      subMessage: 'No se puede deshacer esta acción'
    },
    icon: 'pi pi-exclamation-triangle text-danger',
    rejectClass: 'p-button-secondary p-button-outlined p-button-sm',
    acceptClass: 'p-button-sm p-button-success',
    rejectLabel: 'No',
    acceptLabel: 'Sí',
    accept: () => {
      formData.delete(route('subsystem.destroy', subsystem_id.id), {
        preserveScroll: true,
        onSuccess: () => {
          toast.add({
            severity: 'success',
            group: "customToast",
            text: `Se ha eliminado el subsistema ${formData.name} correctamente`,
            life: 2000
          })
        },
        onError: (error) => {
          toast.add({
            severity: 'error',
            group: "customToast",
            text: `Ha ocurrido un error al intentar eliminar el subsistema ${formData.name}, error: ` + error,
            life: 2000
          })
        }
      })
    }, reject: () => {
      console.error('Error')
    }
  })
}
//endregion

const addSubSystem = () => {
  getSystems()
  openSubSystemModal.value = true
}
</script>
<template>
  <div class="size-full overflow-y-auto">
    <CustomDataTable route-data="subsystem.index" :rows-default="100" :columnas="columns" :actions="buttons"
      @edit="editSubSystem" @delete="deleteSubSystem" :loading>
      <template #buttonHeader>
        <Button @click="addSubSystem" severity="success" icon="fa-solid fa-plus" label="Agregar" outlined />
      </template>
    </CustomDataTable>
  </div>

  <CustomModal v-model:visible="openSubSystemModal" width="30vw">
    <template #icon>
      <i class="fa-solid fa-cloud-arrow-up text-white text-xl"></i>
    </template>
    <template #titulo>
      <p class="text-white">Añadir Subsistema</p>
    </template>
    <template #body>
      <div class="flex flex-col gap-4">
        <!--Sistema-->
        <CustomInput type="dropdown" optionLabel="name" optionValue="id" :options="system" label="Sistema"
          placeholder="Selecione un Sistema" v-model:input="formData.system_id" :loading="customInputLoading" />
        <!--Código-->
        <CustomInput type="number" optionLabel="name" optionValue="id" label="Código"
          placeholder="Escriba código para el subsistema" v-model:input="formData.code" />
        <!--Nombre-->
        <CustomInput label="Nombre" placeholder="Escriba Nombre del SubSistema" v-model:input="formData.name" />
      </div>
    </template>
    <template #footer>
      <Button label="Cancelar" severity="danger" icon="fa fa-circle-xmark" @click="openSubSystemModal = false" />
      <Button label="Guardar" severity="success" icon="pi pi-save" :loading="false" @click="submit()" />
    </template>
  </CustomModal>
</template>
