<script setup>
const { confirmDelete } = useSweetalert()
const { hasRole, hasPermission } = usePermissions()
const toast = useToast();
import { ref, onMounted } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import { usePermissions } from '@/composable/permission';
import { useSweetalert } from '@/composable/sweetAlert'
import { useToast } from "primevue/usetoast";
import AppLayout from '@/Layouts/AppLayout.vue'
import CustomDataTable from '@/Components/CustomDataTable.vue'
import CustomInput from '@/Components/CustomInput.vue'
import CustomModal from '@/Components/CustomModal.vue'
import Empty from '@/Components/Empty.vue'
import Listbox from 'primevue/listbox'
import RadioGroups from '@/Components/RadioGroups.vue'
import Textarea from 'primevue/textarea'
import Toast from 'primevue/toast';

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
  { field: 'name', header: 'Equipo', filter: true, sortable: true },
  { field: 'serial', header: 'Serial', filter: true, sortable: true },
  { field: 'code', header: 'Codigo Interno', filter: true, sortable: true },
  { field: 'status', header: 'Estado ', filter: true, sortable: true },
  { field: 'assigment_date', header: 'Fecha de Prestamo', type: "date", filter: true, sortable: true },
]

const actions = [
  { event: 'download', severity: 'warning', icon: 'fa-regular fa-circle-xmark', text: true, outlined: false, rounded: false, show: hasPermission('assignmentTool delete') },
  // { event: 'delete', severity: 'danger', icon: 'fa-regular fa-trash-can', text: true, outlined: false, rounded: false, show: hasPermission('assignmentTool delete') },
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

const removeElement = (element) => {
  form.tools = form.tools.filter(value => value.id !== element.id)
}

const submit = () => {
  try {
    if (form.tools == null) {
      toast.add({
        severity: 'error',
        group: 'customToast',
        text: 'Seleccione los Equipos a Asignar',
        life: 2000
      })
    } else {
      router.post(route('assignmentTool.store'),
        {
          employee_id: selectedEmployee.value.Num_SAP,
          employee_name: selectedEmployee.value.Nombres_Apellidos,
          supervisor_id: selectedSupervisor.value.Num_SAP,
          project_id: selectedProject.value?.id ?? 0,
          email: selectedEmployee.value.Correo,
          tools: form.tools.map(value => value.id)
        },
        {
          onSuccess: () => {
            toast.add({
              severity: 'success',
              group: 'customToast',
              text: 'Asignación Exitosa, Enviaremos un Mensaje a: ' + selectedEmployee.value.Correo,
              life: 10000
            })
            // toast(`¡Asignación creada exitosamente!`, 'success')
            clearModal()
          },
          onError: (error) => {
            // toast(`Ha ocurrido un error al guardar las asignaciones; ERROR: ${error.message}`, 'error')
          }
        })
    }
  } catch (e) {
    toast.add({ severity: 'error', group: 'customToast', text: 'Hubo un error, reintente', life: 2000 });
    console.log(e)
  }
}

const submitDownload = () => {
  router.put(route('assignmentTool.update', toolDownload.value.id), {
    status: toolStatus.value.title,
    observation: descriptionValue.value
  }, {
    onSuccess: () => {
      toast.add({ summary: 'Asignación Descargada', life: 2000 })
      clearModal2()
    }
  })
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

const url = [
  {
    ruta: 'assignmentTool.index',
    label: 'Asignación Herramientas (Almacén)',
    active: true
  }
]
</script>
<template>
  <AppLayout :href="url">
    <div class="w-full h-[89vh] overflow-y-auto">
      <CustomDataTable :data="assignmentsTool" title="Asignaciones" :rows-default="15" :columnas="columnas"
        :actions="actions" @download="downloadAssignment" @delete="deleteAssignment">
        <template #buttonHeader>
          <Button @click="createAssignmentsTool()" label="Nuevo" icon="fa-solid fa-plus" outlined
            v-if="hasPermission('assignmentTool create')" />
        </template>
      </CustomDataTable>
    </div>
  </AppLayout>

  <!--Modal Asignación de Equipos-->
  <CustomModal v-model:visible="openDialog" :closable="true">
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
          v-model:input="selectedEmployee" optionLabel="Nombres_Apellidos" placeholder="Seleccione Personal"
          showClear />

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
          <div class="flex gap-2 mb-2 w-full overflow-x-auto">
            <div v-for="tool in form.tools"
              class="bg-emerald-100 w-32 border border-l-8 border-l-emerald-400 rounded-lg shadow-lg shadow-emerald-200">

              <div class="p-2 text-sm font-semibold">
                {{ tool.name }}
                <div class="flex w-full justify-between items-center">
                  <div class="block text-xs text-black font-light italic ">{{ tool.serial }}</div>
                  <div class="block text-xs text-danger font-light italic fa-regular fa-trash-can cursor-pointer"
                    @click="removeElement(tool)" v-tooltip="'Quitar'"></div>
                </div>
              </div>
            </div>
          </div>
          <Listbox v-model="form.tools" :options="tools" multiple filter :filterFields="['name', 'serial']"
            optionLabel="name" filterPlaceholder="Seleccione el/los equipo(s) para asignar." class="w-full md:w-14rem"
            :virtualScrollerOptions="{ itemSize: 38 }" listStyle="height:15rem" :pt="{
    filterInput: '!text-sm'
  }">
            <template #option="slotProps">
              <div class="items-center flex justify-between">
                <div> {{ slotProps.option.name }}</div>
                <div>{{ slotProps.option.serial }}</div>
              </div>
            </template>
            <template #empty>
              <Empty message="Aun no se agregan buques" />
            </template>
          </Listbox>
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
      <p>Descargar Equipo {{ toolDownload.name }} de {{ toolDownload.employee_name }}</p>
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
          <Textarea v-model="descriptionValue" rows="5" col="10" placeholder="Agregue una descripción al grupo"
            autoResize :pt="{
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
