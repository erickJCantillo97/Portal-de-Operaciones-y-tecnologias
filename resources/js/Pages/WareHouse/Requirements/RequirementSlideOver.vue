<script setup>
const { hasRole, hasPermission } = usePermissions()
import { Dialog, DialogPanel, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { Link, router } from '@inertiajs/vue3'
import { ref, watch, onMounted } from 'vue'
import { useConfirm } from 'primevue/useconfirm'
import { usePermissions } from '@/composable/permission'
import { useToast } from "primevue/usetoast"
import { XMarkIcon } from '@heroicons/vue/24/outline'
import Accordion from 'primevue/accordion'
import AccordionTab from 'primevue/accordiontab'
import Badge from 'primevue/badge'
import ConfirmPopup from 'primevue/confirmpopup'
import CustomInput from '@/Components/CustomInput.vue'
import DescriptionItem from '@/Components/DescriptionItem.vue'
import ListBox from 'primevue/listbox'
import Loading from '@/Components/Loading.vue'

const { emit } = defineEmits(['closeSlideOver'])

const confirm = useConfirm()
const toast = useToast()

const materialsLoaded = ref()
const users = ref([]);
const user = ref([]);


const props = defineProps({
  show: {
    default: false
  },
  requirement: {
    type: Object,
    required: true
  },

})
const approving = ref(false)

const approve = () => {

}

const materials = ref([])

const getMaterial = async () => {
  materialsLoaded.value = true
  try {
    if (props.requirement.id != null) {
      await axios.get(route('materials.index', props.requirement.id))
        .then((res) => {
          materials.value = res.data.material
          materialsLoaded.value = false
        })
    }
  } catch (error) {
    console.error('Problema al obtener materiales, error: ' + error)
  }
  try {
    if (props.requirement.id != null) {
      await axios.get(route('personal.activos'))
        .then((res) => {
          users.value = res.data.personal
          materialsLoaded.value = false
        })
    }
  } catch (error) {
    console.error('Problema al obtener materiales, error: ' + error)

  }

}

//#region Requirement's CRUD
const approvedRequirement = async () => {
  console.log('Approved')
  try {
    await axios.post(route('aprove.requirement', props.requirement.id))
      .then((res) => {
        approving.value = !approving.value
      })
  } catch (error) {
    console.error('Error: ' + error)
  }
}

const printRequirement = async () => {
  console.log('Print')
  try {
    await axios.get(route('aprove.requirement', props.requirement.id))
      .then((res) => {
        approving.value = !approving.value
      })
  } catch (error) {
    console.error('Error: ' + error)
  }
}

const rejectedRequirement = async () => {
  console.log('Rejected')
  try {
    await axios.post(route('aprove.requirement', props.requirement.id))
      .then((res) => {
        approving.value = !approving.value
      })
  } catch (error) {
    console.error('Error: ' + error)
  }
}

const manageRequirement = async () => {
  // route('manage.requirements', { requirements: requirement.map((x) => x.id) })
  console.log('Gestionar')
  try {
    await axios.get(route('manage.requirements', {
      requirements: requirement.map((x) => x.id)
    })).then((res) => {
      approving.value = !approving.value
    })
  } catch (error) {
    console.error('Error: ' + error)
  }
}

const editRequirement = async () => {
  console.log('Edit')
  try {
    await axios.put(route('aprove.requirement', props.requirement.id))
      .then((res) => {
        approving.value = !approving.value
      })
  } catch (error) {
    console.error('Error: ' + error)
  }
}

const confirmDelete = (event, requirement) => {
  confirm.require({
    target: event.currentTarget,
    message: {
      message: `¿Está seguro de eliminar el requerimiento ${requirement.consecutivo} permanentemente?`,
      subMessage: 'Esta acción no se puede deshacer'
    },
    icon: 'pi pi-exclamation-triangle text-danger',
    rejectClass: 'p-button-secondary p-button-outlined p-button-sm',
    acceptClass: 'p-button-sm p-button-danger',
    rejectLabel: 'No',
    acceptLabel: 'Sí',
    accept: () => {
      // await axios.delete(route('requirement.delete'), requirement_id)
      //   .then((res) => {
      //     console.log('Hace algo')
      //   })
      console.log('Hace algo')
      toast.add({
        severity: 'success',
        group: 'customToast',
        text: `Se ha eliminado el requerimiento ${requirement.consecutivo} correctamente.`,
        life: 3000
      })
    },
    reject: () => {
      console.log('Hace algo aquí también')
      // toast.add({
      //   severity: 'error',
      //   summary: 'Rejected',
      //   detail: 'You have rejected',
      //   life: 3000
      // })
    }
  })
}
//#endregion

onMounted(() => {
  getMaterial()
})

const optionStatusRequirement = {
  'Oficial': {
    icon: 'fa-solid fa-check',
    color: 'bg-success text-white'
  },
  'Aprobado DEIPR': {
    icon: 'fa-solid fa-user-check',
    color: 'bg-primary text-white'
  },
  'Por Aprobar': {
    icon: 'fa-solid fa-user-clock',
    color: 'bg-danger text-white'
  },
}

watch(() => props.materialsLoaded, (newValue, oldValue) => {
  if (newValue) {
    loadingMaterials.value = false
  } else {
    loadingMaterials.value = true
  }
})

const optionStatus = {
  'PENDIENTE': {
    icon: 'fa-solid fa-user-clock',
    color: 'bg-danger text-white'
  },
  'Aprobado DEIPR': {
    icon: 'fa-solid fa-check',
    color: 'bg-success text-white'
  },
  'RECHAZADO': {
    icon: 'fa-solid fa-xmark',
    color: 'bg-red-600 text-white'
  },
  'DISPONIBLE GECON': {
    icon: '',
    color: 'bg-success text-white'
  },
  'DISPONIBLE SAP INHOUSE': {
    icon: '',
    color: 'bg-success text-white'
  },
}
</script>
<template>
  <TransitionRoot as="template" :show>
    <Dialog as="div" class="relative z-10" @close="$emit('closeSlideOver')">
      <TransitionChild as="template" enter="ease-in-out duration-500" enter-from="opacity-0" enter-to="opacity-100"
        leave="ease-in-out duration-500" leave-from="opacity-100" leave-to="opacity-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
      </TransitionChild>
      <div class="fixed inset-0 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
          <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
            <TransitionChild as="template" enter="transform transition ease-in-out duration-500 sm:duration-700"
              enter-from="translate-x-full" enter-to="translate-x-0"
              leave="transform transition ease-in-out duration-500 sm:duration-700" leave-from="translate-x-0"
              leave-to="translate-x-full">
              <DialogPanel class="pointer-events-auto relative w-96">
                <TransitionChild as="template" enter="ease-in-out duration-500" enter-from="opacity-0"
                  enter-to="opacity-100" leave="ease-in-out duration-500" leave-from="opacity-100" leave-to="opacity-0">
                  <div class="absolute left-0 top-0 -ml-8 flex pr-2 pt-4 sm:-ml-10 sm:pr-4">
                    <button type="button"
                      class="relative rounded-md text-gray-300 hover:text-white focus:outline-none focus:ring-2 focus:ring-white"
                      @click="$emit('closeSlideOver')">
                      <span class="absolute -inset-2.5" />
                      <span class="sr-only">Close panel</span>
                      <XMarkIcon class="size-6" aria-hidden="true" />
                    </button>
                  </div>
                </TransitionChild>
                <div class="h-full overflow-y-auto bg-white ">
                  <div class="w-full bg-blue-900 text-white uppercase p-2">
                    <h2 class="text-lg text-center font-bold text-white">
                      Requerimiento
                    </h2>
                  </div>
                  <div class="flex gap-2 items-center justify-center p-2">
                    <!--Botón Imprimir-->
                    <Link :href="'#'" v-if="requirement.estado == 'Aprobado Gerencia'">
                    <Button @click="printRequirement()" v-tooltip.top="'Imprimir'" size="small" icon="pi pi-file-pdf"
                      raised severity="danger" />
                    </Link>

                    <!--Botón Aprobar-->
                    <Button @click="approving = true" v-tooltip.top="'Aprobar'" size="small" icon="pi pi-check-circle"
                      severity="success"
                      v-if="hasPermission('aprobar requerimientos') && requirement.estado != 'Aprobado Gerencia'" />

                    <!--Botón Rechazar-->
                    <Link :href="'#'">
                    <Button @click="rejectedRequirement()" v-tooltip.top="'Rechazar'" size=" small"
                      icon="pi pi-times-circle" raised severity="warning"
                      v-if="hasPermission('aprobar requerimientos') && requirement.estado != 'Aprobado Gerencia'" />
                    </Link>

                    <!--Botón Gestionar-->
                    <!-- <Link :href="route('manage.requirements', { requirements: requirement.map((data) => data.id) })"> -->
                    <Link :href="'#'">
                    <Button v-tooltip.top="'Gestionar'" size="small" raised severity="info"
                      v-if="hasPermission('gestionar materiales') && requirement.estado == 'Aprobado DEIPR'">
                      <template #icon>
                        <i class=" fa-solid fa-list-check"></i>
                      </template>
                    </Button>
                    </Link>

                    <!--Botón Editar-->
                    <Link :href="'#'">
                    <Button @click="editRequirement()" v-tooltip.top="'Editar'" size="small" icon="pi pi-pencil" raised
                      severity="warning"
                      v-if="hasPermission('quote create') && requirement.estado != 'Aprobado Gerencia'" />
                    </Link>

                    <!--Botón Eliminar-->
                    <Link :href="'#'">
                    <Button @click="confirmDelete($event, requirement)" v-tooltip.top="'Eliminar'" size=" small"
                      icon="pi pi-trash" raised severity="danger"
                      v-if="hasPermission('quote delete') && requirement.estado != 'Oficial'" />
                    </Link>

                  </div>
                  <div v-if="approving" class="space-y-4 p-2 border rounded-lg mx-2">
                    <CustomInput type="text" label="Grafo" class="w-full" placeholder="Grafo" />
                    <CustomInput type="text" label="Solicitud de Pedido" class="w-full" placeholder="Solped" />
                    <CustomInput type="date" label="Fecha" class="w-full" placeholder="dd/mm/yyyy" />
                    <div class="flex space-x-4">
                      <Button class="w-full" label="Aprobar" icon="pi pi-save" severity="success"
                        @click="approvedRequirement()" />
                      <Button class="w-full" label="Cancelar" icon="fa fa-circle-xmark" @click="approving = false"
                        severity="danger" />
                    </div>
                  </div>
                  <article class="w-full p-2">
                    <div class=" border border-solid rounded-lg p-2 mb-2">
                      <!-- {{ requirement }} -->
                      <DescriptionItem :data="requirement" :option-status="optionStatusRequirement" />
                    </div>
                    <div class="border border-solid rounded-lg mb-2">

                    </div>
                    <div class="border border-solid rounded-lg mb-2">
                      <div class="mb-4">
                        <h3 class="font-semibold p-1 rounded-t-xl text-center bg-primary text-white">
                          Lista de Materiales
                        </h3>
                        <div v-if="materialsLoaded" class="mt-4">
                          <Loading message="Cargando Lista de Materiales" />
                        </div>
                        <div class="shadow-md my-4 px-2 h-full" v-for="material in materials">
                          <Accordion :pt="{
                            content: '!h-[80vh] !p-2 !overflow-y-auto'
                          }">
                            <AccordionTab :activeIndex="0">
                              <template #header>
                                <span class="flex align-items-center gap-2 w-full">
                                  <span class="font-bold white-space-nowrap">
                                    {{ material.material }}
                                  </span>
                                  <Badge :value="`${parseInt(material.cantidad)} ${material.unidad}`"
                                    class="ml-auto mr-2 bg-emerald-600" />
                                </span>
                              </template>
                              <DescriptionItem :data="material" :optionStatus />
                            </AccordionTab>
                          </Accordion>
                        </div>
                      </div>
                    </div>
                  </article>
                </div>
              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>
