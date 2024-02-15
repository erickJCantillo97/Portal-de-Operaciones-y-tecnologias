<script setup>
import { ref, onMounted } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import { useSweetalert } from '@/composable/sweetAlert'
import AppLayout from '@/Layouts/AppLayout.vue'
import CustomDataTable from '@/Components/CustomDataTable.vue'
import CustomModal from '@/Components/CustomModal.vue'
import CustomInput from '@/Components/CustomInput.vue'
import RadioGroups from '@/Components/RadioGroups.vue'
import Listbox from 'primevue/listbox'
import Textarea from 'primevue/textarea'
import Toast from 'primevue/toast';
import { useToast } from "primevue/usetoast";
const toast = useToast();
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

// V-models de Asignación de Equipos
const personal = ref()
const openDialog = ref(false)
const selectedEmployee = ref({})
const selectedSupervisor = ref()
const selectedProject = ref()
const loading = ref(true)

//V-models de Descarga de Equipos
const openDialog2 = ref(false)
const toolStatus = ref()
const descriptionValue = ref()
const toolDownload = ref(true)

const getPersonal = async () => {
  await axios.get(route('personal.activos'))
    .then(res => {
      personal.value = res.data.personal
      loading.value = false
    })
}

const columnas = [
  // { field: 'project.name', header: 'Proyecto', filter: true, sortable: true },
  { field: 'employee_name', header: 'Empleado', filter: true, sortable: true },
  { field: 'tool.name', header: 'Equipo', filter: true, sortable: true },
  { field: 'tool.serial', header: 'Serial', filter: true, sortable: true },
  { field: 'tool.code', header: 'Codigo Interno', filter: true, sortable: true },
  { field: 'status', header: 'Estado ', filter: true, sortable: true },
  { field: 'assigment_date', header: 'Fecha de Prestamo', type: "date", filter: true, sortable: true },
]

const actions = [
  { event: 'download', severity: 'warning', icon: 'fa-regular fa-circle-xmark', text: true, outlined: false, rounded: false },
  { event: 'delete', severity: 'danger', icon: 'fa-regular fa-trash-can', text: true, outlined: false, rounded: false },
  // { event: 'deleteClic', severity: 'danger', icon: 'fa-solid fa-trash', class: '!h-8', text: true, outlined: false, rounded: false },
]

const form = useForm({
  email: '',
  tools: null,
})

const createAssignmentsTool = () => {
  openDialog.value = true
}

const downloadAssignment = (event, data) => {
  openDialog2.value = true
  console.log(data)
  toolDownload.value = data
}

const deleteAssignment = (event, data) => {
  confirmDelete(data.id, 'Asignación', 'assignmentTool')
}

const submit = () => {
  try {
    router.post(route('assignmentTool.store'),
      {
        employee_id: selectedEmployee.value.Num_SAP,
        employee_name: selectedEmployee.value.Nombres_Apellidos,
        supervisor_id: selectedSupervisor.value.Num_SAP,
        project_id: selectedProject.value.id,
        email: selectedEmployee.value.Correo,
        tools: form.tools
      },
      {
        onSuccess: () => {
          // toast(`¡Asignación creada exitosamente!`, 'success')
          clearModal()
        },
        onError: (error) => {
          // toast(`Ha ocurrido un error al guardar las asignaciones; ERROR: ${error.message}`, 'error')
        }
      })
  } catch (e) {
    // toast(e.message, 'error')
  }
}

const submitDownload = () => {
  router.put(route('assignmentTool.update', toolDownload.value.id), {
    status: toolStatus.value.title,
    observation: descriptionValue.value
  }, {
    onSuccess: () => {
      toast.add({ summary: 'Asignación Descargada', life: 2000 });
      clearModal2()
    }
  });
}

const clearModal = () => {
  openDialog.value = false
  selectedEmployee.value = [],
    selectedSupervisor.value = [],
    selectedProject.value = [],
    form.reset()
}

const clearModal2 = () => {
  openDialog2.value = false
  form.reset()
}
</script>

<template>
  <AppLayout>
    <div class="w-full h-[89vh] overflow-y-auto">
      <CustomDataTable :data="assignmentsTool" title="Asignaciones" :rows-default="15" :columnas="columnas"
        :actions="actions" @download="downloadAssignment" @delete="deleteAssignment">
        <template #buttonHeader>
          <Button @click="createAssignmentsTool()" label="Nuevo" icon="fa-solid fa-plus" outlined />
        </template>
      </CustomDataTable>
    </div>
  </AppLayout>

  <!--Modal Asignación de Equipos-->
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
        <CustomInput type="dropdown" :loading label="Seleccionar Persona" :options="personal"
          v-model:input="selectedEmployee" optionLabel="Nombres_Apellidos" placeholder="Seleccione Personal" showClear />

        <!--CAMPO SELECCIÓN DE SUPERVISOR (supervisor)-->
        <CustomInput type="dropdown" :loading label="Seleccionar Supervisor" :options="personal"
          optionLabel="Nombres_Apellidos" v-model:input="selectedSupervisor" placeholder="Seleccione Supervisor"
          showClear />

        <!--CAMPO SELECCIÓN DE PROYECTOS (projects)-->
        <CustomInput type="dropdown" label="Seleccionar Proyectos" :options="projects" optionLabel="name"
          v-model:input="selectedProject" placeholder="Seleccione Proyecto" showClear
          :invalid="$attrs.errors.project_id != null" :errorMessage="$attrs.errors.project_id" />

        <!--CAMPO CORREO (tools)-->
        <CustomInput label="Correo" placeholder="correocorporativo@cotecmar.com" v-model:input="selectedEmployee.Correo"
          :invalid="$attrs.errors.email != null" :errorMessage="$attrs.errors.email" />

        <!--CAMPO SELECCIÓN DE EQUIPOS (tools)-->
        <div class="col-span-2">
          <label class="text-md font-semibold">Seleccionar Equipos</label>
          <Listbox v-model="form.tools" :options="tools" multiple filter optionLabel="name" optionValue="id"
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

  <!--Modal Descargar Herramienta-->
  <CustomModal v-model:visible="openDialog2" :closable="false">
    <template #icon>
      <span class="material-symbols-outlined font-semibold text-3xl">
        construction
      </span>
    </template>
    <template #titulo>
      <p>Descargar Equipo {{ toolDownload.tool.name }} de {{ toolDownload.employee_name }}</p>
    </template>
    <template #body>
      <section class="p-2 [&>div>p]:text-xs [&>div>p]:text-gray-500 [&>div>p]:italic">
        <div class="mb-4">
          <label>Estado de la herramienta <span class="text-red-700 italic mt-2 font-serif">*</span></label>
          <RadioGroups v-model="toolStatus" />
          <span class="text-red-700 text-xs italic mt-2 font-serif" v-if="!toolStatus">{{
            $page.props.errors.status
          }}</span>
        </div>
        <div class="col-span-4">
          <label>Descripción de Estado de la herramienta</label>
          <Textarea v-model="descriptionValue" rows="5" col="10" placeholder="Agregue una descripción al grupo" autoResize
            :pt="{
              root: '!w-full !text-sm'
            }" />
        </div>
      </section>
    </template>
    <template #footer>
      <Button label="Cancelar" icon="fa-regular fa-circle-xmark" severity="danger" outlined @click="clearModal2()" />
      <Button label="Guardar" icon="fa-solid fa-floppy-disk" severity="success" outlined :loading="false"
        @click="submitDownload()" />
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
