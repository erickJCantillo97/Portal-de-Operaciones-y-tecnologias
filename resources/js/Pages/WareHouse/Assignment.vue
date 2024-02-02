<script setup>
import { ref, onMounted, computed } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import { useSweetalert } from '@/composable/sweetAlert'
import AppLayout from '@/Layouts/AppLayout.vue'
import CustomDataTable from '@/Components/CustomDataTable.vue'
import CustomModal from '@/Components/CustomModal.vue'
import CustomInput from '@/Components/CustomInput.vue'
import Listbox from 'primevue/listbox'

const { toast } = useSweetalert()
const { confirmDelete } = useSweetalert()

const props = defineProps({
  assignmentsTool: {
    type: Array,
    default: []
  },
  projects: Array,
  tools: Array
})

onMounted(() => {
  getPersonal()
})

const personal = ref()
const openDialog = ref(false)
const selectedEmployee = ref()
const selectedSupervisor = ref()
const selectedProject = ref()


const getPersonal = async () => {
  await axios.get(route('personal.activos'))
    .then(res => {
      personal.value = res.data.personal
    })
}

const columnas = [
  { field: 'tool_id', header: 'Equipo' },
  { field: 'employee_id', header: 'Empleado' },
  { field: 'supervisor_id', header: 'Supervisor' },
  { field: 'project_id', header: 'Proyecto' },
]

const actions = [
  { event: 'download', severity: 'info', icon: 'fa-solid fa-download', text: true, outlined: false, rounded: false },
  { event: 'delete', severity: 'danger', icon: 'fa-regular fa-trash-can', text: true, outlined: false, rounded: false },
  // { event: 'deleteClic', severity: 'danger', icon: 'fa-solid fa-trash', class: '!h-8', text: true, outlined: false, rounded: false },
]

const form = useForm({
  email: null,
  tools: null,
})

const createAssignmentsTool = () => {
  openDialog.value = true
}

const downloadAssignment = () => {
  alert('Descargando...')
  setTimeout(() => {
    alert('Archivo Descargado! 😁')
  }, 5000)
}

const deleteAssignment = (event, data) => {
  confirmDelete(data.id, 'Asignación', 'assignmentTool')
}

const submit = () => {
  try{
    router.post(route('assignmentTool.store'),
    {
      employee_id: selectedEmployee.value.Num_SAP,
      employee_name: selectedEmployee.value.Nombres_Apellidos,
      supervisor_id: selectedSupervisor.value.Num_SAP,
      project_id: selectedProject.value.id,
      email: form.email,
      tools: form.tools
    },
    {
      onSuccess: () => {
        toast(`¡Asignación creada exitosamente!`, 'success')
        clearModal()
      },
      onError: (error) => {
        toast(`Ha ocurrido un error al guardar las asignaciones; ERROR: ${error.message}`, 'error')
      }
    })
  } catch (e) {
    toast(e.message, 'error')
  }
}

const clearModal = () => {
  openDialog.value = false

  selectedEmployee.value = [],
  selectedSupervisor.value = [],
  selectedProject.value = [],
  form.reset()
}

</script>

<template>
  <AppLayout>
    <CustomDataTable :data="assignmentsTool" title="Asignaciones" :rows-default="15" :columnas="columnas"
      :actions="actions" @download="downloadAssignment()" @delete="deleteAssignment()">
      <template #buttonHeader>
        <Button @click="createAssignmentsTool()" label="Nuevo" icon="fa-solid fa-plus" outlined />
      </template>
    </CustomDataTable>
  </AppLayout>

  <CustomModal v-model:visible="openDialog" :closable="false">
    <template #icon>
      <span class="material-symbols-outlined font-semibold text-3xl">
        construction
      </span>
    </template>
    <template #titulo>
      <p>Asignar Equipo</p>
    </template>
    <template #body>
      <section class="grid grid-cols-2 gap-4">
        <!--CAMPO SELECCIÓN DE PERSONA (personal)-->
        <CustomInput type="dropdown" label="Seleccionar Persona" :options="personal" v-model:input="selectedEmployee"
          optionLabel="Nombres_Apellidos" placeholder="Seleccione Personal" showClear />

        <!--CAMPO SELECCIÓN DE SUPERVISOR (supervisor)-->
        <CustomInput type="dropdown" label="Seleccionar Supervisor" :options="personal" optionLabel="Nombres_Apellidos"
          v-model:input="selectedSupervisor" placeholder="Seleccione Supervisor" showClear />

        <!--CAMPO SELECCIÓN DE PROYECTOS (projects)-->
        <CustomInput type="dropdown" label="Seleccionar Proyectos" :options="projects" optionLabel="name"
          v-model:input="selectedProject" placeholder="Seleccione Proyecto" showClear
          :invalid="$attrs.errors.project_id != null" :errorMessage="$attrs.errors.project_id" />

        <!--CAMPO CORREO (tools)-->
        <CustomInput label="Correo" placeholder="correocorporativo@cotecmar.com" v-model:input="form.email"
          :invalid="$attrs.errors.email != null" :errorMessage="$attrs.errors.email" />

        <!--CAMPO SELECCIÓN DE EQUIPOS (tools)-->
        <div class="col-span-2">
          <label class="text-md font-semibold">Seleccionar Equipos</label>
          <Listbox v-model="form.tools" :options="fakeTools" multiple filter optionLabel="name" optionValue="id"
            :emptyMessage="loading ? 'Cargando equipos, espere un momento por favor...' : 'No se encuentran resultados.'"
            filterPlaceholder="Seleccione el/los equipo(s) para asignar." class="w-full md:w-14rem"
            :virtualScrollerOptions="{ itemSize: 38 }" listStyle="height:15rem" :pt="{
              filterInput: '!text-sm'
            }" />
        </div>
      </section>
    </template>
    <template #footer>
      <Button label="Cancelar" icon="fa-regular fa-circle-xmark" severity="danger" outlined @click="clearModal()" />
      <Button label="Guardar" icon="fa-solid fa-floppy-disk" severity="success" outlined :loading="false"
        @click="submit()" />
    </template>
  </CustomModal>
</template>
