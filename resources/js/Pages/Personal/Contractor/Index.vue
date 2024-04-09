<script setup>
const { confirmDelete } = useSweetalert()
const { hasRole, hasPermission } = usePermissions()
const toast = useToast();
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import { usePermissions } from '@/composable/permission'
import { useSweetalert } from '@/composable/sweetAlert'
import { useToast } from "primevue/usetoast"
import AppLayout from '@/Layouts/AppLayout.vue'
import AutoComplete from 'primevue/autocomplete'
import CustomDataTable from '@/Components/CustomDataTable.vue'
import CustomInput from '@/Components/CustomInput.vue'
import CustomModal from '@/Components/CustomModal.vue'
import Empty from '@/Components/Empty.vue'
import Listbox from 'primevue/listbox'
import Toast from 'primevue/toast'

const props = defineProps({
  employees: Array
})

const openDialog = ref(false)
const selectedContractor = ref()

const items = ref([]);

const search = (event) => {
  let _items = [...Array(10).keys()];

  items.value = event.query ? [...Array(10).keys()].map((item) => event.query + '-' + item) : _items;
}

//#region CustomDataTable
const columnas = [
  { field: 'contractor', header: 'Empresa', filter: true, sortable: true },
  { field: 'name', header: 'Nombre', filter: true, sortable: true },
  { field: 'labor', header: 'Cargo', filter: true, sortable: true },
]

const actions = [
  { event: 'edit', severity: 'primary', icon: 'fa-solid fa-pencil', text: true, outlined: false, rounded: false },
  { event: 'delete', severity: 'danger', icon: 'fa-regular fa-trash-can', text: true, outlined: false, rounded: false },
  // { event: 'deleteClic', severity: 'danger', icon: 'fa-solid fa-trash', class: '!h-8', text: true, outlined: false, rounded: false },
]
//#endregion

//#region CRUD
const formData = useForm({
  contractor: null,
  name: null,
  labor: null,
})

const submit = () => {
  try {
    if (formData) {
      formData.post(route('contractorEmployees.store'), {
        preserveScroll: true,
        onSuccess: () => {
          toast.add({
            severity: 'success',
            group: 'customToast',
            text: 'Se ha creado un contratista satisfactoriamente',
            life: 10000
          })
          clearModal()
        },
        onError: (error) => {
          toast.add({
            severity: 'danger',
            group: 'customToast',
            text: 'No se ha podido crear el personal contratista',
            life: 10000
          })
        }
      })
    } else {
      formData.put(route('contractorEmployees.update', formData.id), {
        onSuccess: () => {
          toast.add({
            severity: 'success',
            group: 'customToast',
            text: 'Se ha editado el contratista satisfactoriamente',
            life: 10000
          })
          clearModal()
        },
        onError: (error) => {
          toast.add({
            severity: 'danger',
            group: 'customToast',
            text: 'No se ha podido actualizar el personal contratista',
            life: 10000
          })
        }
      })
    }
  } catch (error) {
    toast.add({
      severity: 'error',
      group: 'customToast',
      text: 'Hubo un error, reintente por favor',
      life: 2000
    })
    console.error(error)
  }
}

const editContractor = (event, contractor) => {
  try {
    openModal()
    formData.contractor = contractor.contractor
    formData.name = contractor.name
    formData.labor = contractor.labor
  } catch (error) {
    console.error('Error: ' + error)
  }
}

const deleteContractor = (event, contractor) => {
  formData.delete(route('contractorEmployees.destroy', contractor), {
    preserveScroll: true,
    onSuccess: () => {
      toast.add({
        severity: 'success',
        group: 'customToast',
        text: 'Se ha eliminado un contratista satisfactoriamente',
        life: 10000
      })
      clearModal()
    },
    onError: (error) => {
      toast.add({
        severity: 'danger',
        group: 'customToast',
        text: 'No se ha podido eliminar el personal contratista: ' + error,
        life: 10000
      })
    }
  })
}

const openModal = () => {
  openDialog.value = true
}

const clearModal = () => {
  openDialog.value = false
  formData.reset()
}
//#endregion

const urls = ref([
  {
    url: '/contractor-employees',
    label: 'Contratistas'
  }
])
</script>
<template>
  <AppLayout :urls="urls">
    <div class="w-full h-[89vh] overflow-y-auto">
      <CustomDataTable :data="employees" title="Contratistas" :rows-default="15" :columnas="columnas" :actions="actions"
        @edit="editContractor" @delete="deleteContractor">
        <template #buttonHeader>
          <Button @click="openModal()" label="Nuevo" icon="fa-solid fa-plus" outlined />
        </template>
      </CustomDataTable>
    </div>
  </AppLayout>

  <!--Modal Asignación de Equipos-->
  <CustomModal v-model:visible="openDialog" :closable="false" width="30vw">
    <template #icon>
      <span class="material-symbols-outlined font-semibold text-3xl">
        person_add
      </span>
    </template>
    <template #titulo>
      <p>Crear Empleado</p>
    </template>
    <template #body>
      <span class="text-md mb-0.5 font-bold text-gray-900">
        Nombre de la Empresa
      </span>
      <section class="flex flex-col gap-4">
        <AutoComplete v-model="formData.contractor" placeholder="Escriba nombre de la empresa" dropdown
          :suggestions="items" @complete="search" />

        <CustomInput type="text" v-model:input="formData.name" label="Nombre Empleado"
          placeholder="Escriba nombre del empleado" showClear />

        <CustomInput type="text" v-model:input="formData.labor" label="Cargo" placeholder="Escriba el cargo"
          showClear />
      </section>
    </template>
    <template #footer>
      <Button label="Cancelar" icon="fa-regular fa-circle-xmark" severity="danger" outlined @click="clearModal()" />
      <Button icon="fa-solid fa-floppy-disk" severity="success" outlined @click="submit()">
        {{ formData != null ? 'Actualizar ' : 'Guardar' }}
      </Button>
    </template>
  </CustomModal>
</template>
