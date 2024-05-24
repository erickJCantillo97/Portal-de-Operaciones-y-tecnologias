<script setup>
const { confirmDelete } = useSweetalert()
import { ref, onMounted } from 'vue'
import { useConfirm } from 'primevue/useconfirm'
import { useForm } from '@inertiajs/vue3'
import { useSweetalert } from '@/composable/sweetAlert'
import { useToast } from 'primevue/usetoast'
import CustomDataTable from '@/Components/CustomDataTable.vue'
import CustomInput from '@/Components/CustomInput.vue'
import CustomModal from '@/Components/CustomModal.vue'

const confirm = useConfirm()
const toast = useToast()

const openSystemModal = ref(false)
const customInputLoading = ref(false)
const loading = ref(false)
const saveLoading = ref(false)
const groups = ref([])

const formData = useForm({
  constructive_group_id: '',
  code: '',
  name: ''
})

//#region CustomDatatable
const columns = [
  { field: 'constructive_group_id', header: 'Grupo Constructivo', filter: true },
  { field: 'code', header: 'Código', filter: true },
  { field: 'name', header: 'Nombre', filter: true }
]

const buttons = [
  { event: 'edit', severity: 'warning', class: '', icon: 'fa-solid fa-pencil', text: true, outlined: false, rounded: false },
  { event: 'delete', severity: 'danger', icon: 'fa-regular fa-trash-can', class: '!h-8', text: true, outlined: false, rounded: false },
]
//#endregion

const getConstructivesGroups = () => {
  loading.value = true
  customInputLoading.value = true
  axios.get(route('gruposConstructivos.index'))
    .then((res) => {
      groups.value = res.data[0].map((value) => {
        return {
          ...value,
          name: value.name + '. ' + value.descripcion,
        }
      })
      loading.value = false
      customInputLoading.value = false
    })
}

//#region CRUD
const submit = () => {
  try {
    formData.post(route('systems.store'), formData, {
      preserveScroll: true,
      onSuccess: () => {
        toast.add({
          severity: 'success',
          group: "customToast",
          text: `Se ha guardado el sistema ${formData.name} correctamente`,
          life: 2000
        })
        openSystemModal.value = false
        saveLoading.value = true
        formData.reset()
      },
      onError: (error) => {
        toast.add({
          severity: 'error',
          group: "customToast",
          text: `Ha ocurrido un error al intentar guardar el sistema ${formData.name}, error: ` + error,
          life: 2000
        })
      }
    })
  } catch (error) {
    console.log('Error: ' + error)
  }
}

const editSystem = (event, data) => {
  formData.constructive_group_id = data.id || ''
  formData.code = data.code || ''
  formData.name = data.name || ''
  openSystemModal.value = true
}

const deleteSystem = (event, system_id) => {
  confirm.require({
    target: event.currentTarget,
    message: {
      message: '¿Está seguro de eliminar este sistema?',
      subMessage: 'No se puede deshacer esta acción'
    },
    icon: 'pi pi-exclamation-triangle text-danger',
    rejectClass: 'p-button-secondary p-button-outlined p-button-sm',
    acceptClass: 'p-button-sm p-button-success',
    rejectLabel: 'No',
    acceptLabel: 'Sí',
    accept: () => {
      formData.delete(route('systems.destroy', system_id.id), {
        preserveScroll: true,
        onSuccess: () => {
          toast.add({
            severity: 'success',
            group: "customToast",
            text: `Se ha eliminado el sistema ${formData.name} correctamente`,
            life: 2000
          })
        },
        onError: (error) => {
          toast.add({
            severity: 'error',
            group: "customToast",
            text: `Ha ocurrido un error al intentar eliminar el sistema ${formData.name}, error: ` + error,
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

const addSystem = () => {
  formData.reset()
  openSystemModal.value = true
  getConstructivesGroups()
}

</script>
<template>
  <div class="size-full overflow-y-auto">
    <CustomDataTable route-data="systems.index" :rows-default="100" :columnas="columns" :actions="buttons"
      @edit="editSystem" @delete="deleteSystem" :loading>
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
      <p class="text-white">
        {{ formData.constructive_group_id != null ? 'Editar' : 'Guardar' }} Sistema
      </p>
    </template>
    <template #body>
      <div class="flex flex-col gap-4">
        <!--Grupo Constructivo-->
        <CustomInput type="dropdown" optionLabel="name" optionValue="id" :options="groups" label="Grupo Constructivo"
          placeholder="Selecione un Grupo Constructivo" v-model:input="formData.constructive_group_id"
          :loading="customInputLoading" />
        <!--Código-->
        <CustomInput type="number" optionLabel="name" optionValue="id" label="Código"
          placeholder="Escriba código para el Sistema" v-model:input="formData.code" />
        <!--Nombre-->
        <CustomInput label="Nombre" placeholder="Escriba Nombre del Sistema" v-model:input="formData.name" />
      </div>
    </template>
    <template #footer>
      <Button label="Cancelar" severity="danger" icon="fa fa-circle-xmark" @click="openSystemModal = false" />
      <Button :label="formData.constructive_group_id != null ? 'Actualizar' : 'Guardar'" severity="success"
        icon="pi pi-save" :loading="saveLoading" @click="submit()" />
    </template>
  </CustomModal>
</template>
