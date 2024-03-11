<script setup>
import { ref, onMounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import { XMarkIcon } from '@heroicons/vue/24/outline'
import { Dialog, DialogPanel, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { useSweetalert } from '@/composable/sweetAlert'
import Moment from 'moment'
import Swal from 'sweetalert2'
import Accordion from 'primevue/accordion'
import AccordionTab from 'primevue/accordiontab'
import Button from 'primevue/button'
import { router } from '@inertiajs/vue3'
import { usePermissions } from '@/composable/permission';
import Tag from 'primevue/tag'
const { hasRole, hasPermission } = usePermissions()

const { toast } = useSweetalert()

const emit = defineEmits(['closeSlideOver'])

onMounted(() => {
  // getVersions()
})

const checked = ref(false)
const dateSelected = ref()
const openStatusDialog = ref(false)
const openCommentsDialog = ref(false)
const showDateResponse = ref(true)
const statusSelected = ref()
const statusOptions = ref(
  [
    { id: 0, name: 'LIQUIDADO' },
    { id: 1, name: 'EN EJECUCIÓN' }
  ])

const icons = [
  'fa-solid fa-check', //0 -> Liquidado
  'fa-solid fa-user-clock', //1 -> En Ejecución
]

const colors = [
  'emerald', //0 -> Liquidado
  'orange', //1 -> En Ejecuión
]

const props = defineProps(
  {
    show: {
      default: false
    },
    data: {
      type: Object,
      required: false
    }
  }
)

const openStatusModal = () => {
  openStatusDialog.value = true
}

const closeStatusDialog = () => {
  openStatusDialog.value = false
}

const openCommentsModal = () => {
  openCommentsDialog.value = true
}

const closeCommentsModal = () => {
  openCommentsDialog.value = false
}

const key = ref(0)
const saveStatus = async () => {
  try {
    await axios.post(route('quotestatus.store', {
      status: statusSelected.value,
      quote_version_id: props.quote.version_id,
      fecha: dateSelected.value
    })).then(res => {
      key.value++;
    })
  } catch (error) {
    console.log(error)
  }
}

//#region API's
const getVersions = () => {
  try {
    axios.get(route('get.quotes.versions')
      .then(res => {
        console.log('Hello ' + res.data)
      }))
  } catch (error) {
    console.log(error)
  }
}

const deleteQuoteVersion = () => {
  try {
    Swal.fire({
      title: `¿Está seguro de eliminar la estimación \n ${props.quote.name} ${props.quote.consecutive}?`,
      icon: 'warning',
      showDenyButton: true,
      confirmButtonText: 'Eliminar',
      denyButtonText: 'Cancelar'
    }).then((response) => {
      if (response.isConfirmed) {
        router.delete(route('quotesversion.destroy', props.quote.version_id), {
          onSuccess: () => {
            toast(`Se ha eliminado la estimación \n ${props.quote.name} ${props.quote.consecutive} satisfactoriamente`, 'success')
            emit('closeSlideOver')
          },
          onError: (error) => {
            toast(`Ha ocurrido un error al eliminar la versión \n ${props.quote.name} ${props.quote.consecutive}`, 'error')
            emit('closeSlideOver')
          }
        }).then((res) => {
        })
      }
    })
  } catch (error) {
    console.log(error)
  }
}
//#endregion

// Formatear el número en moneda (USD)
const formatCurrency = (value) => {
  return !value ? 0 : parseFloat(value).toLocaleString('es-CO', {
    style: 'currency',
    currency: 'COP',
  })
}
</script>

<template>
  <TransitionRoot as="template" :show="show">
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
                <div class="h-full overflow-y-auto bg-white p-2">
                  <div class="absolute w-96 -m-2 z-50 py-4 bg-blue-900 text-white uppercase p-2">
                    <h2 class="text-lg text-center font-bold text-white">
                      {{ data.contract_id }}
                    </h2>
                  </div>
                  <header class="w-full mt-20">
                    <div class="rounded-lg border border-gray-300 p-2 mb-4">
                      <h3 class="flex items-center justify-center uppercase underline font-bold text-gray-500">
                        Descripción
                      </h3>
                      <div class="flex items-center justify-center py-4 text-sm font-medium">
                        <dd class="text-gray-900 first-letter:uppercase">{{ data.subject }}</dd>
                      </div>
                    </div>
                    <div class="border border-solid rounded-lg p-2 mb-2">
                      <dl class="divide-y divide-gray-200 border-b border-t border-gray-200">
                        <div class="flex justify-between py-3 text-sm font-medium">
                          <dt class="text-gray-500">Estimación:</dt>
                          <dd class="text-gray-900 uppercase">{{ data.quote_id != null ? data.quote_id : 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between py-3 text-sm font-medium">
                          <dt class="text-gray-500">Cliente:</dt>
                          <dd class="text-gray-900">{{ data.customer_id != null ? data.quote.customer.name : 'N/A' }}
                          </dd>
                        </div>
                        <div class="flex justify-between py-3 text-sm font-medium">
                          <dt class="text-gray-500">Supervisor:</dt>
                          <dd class="text-gray-900">{{ data.supervisor != null ? data.supervisor : 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between py-3 text-sm font-medium">
                          <dt class="text-gray-500">Tipo de Venta:</dt>
                          <dd class="text-gray-900">{{ data.type_of_sale != null ? data.type_of_sale : 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between py-3 text-sm font-medium">
                          <dt class="text-gray-500">Fecha de Inicio:</dt>
                          <dd class="text-gray-900">
                            {{ Moment(data.start_date).format('DD/MM/YY') }}</dd>
                        </div>
                        <div class="flex justify-between py-3 text-sm font-medium">
                          <dt class="text-gray-500">Fecha Fin:</dt>
                          <dd class="text-gray-900">
                            {{ Moment(data.end_date).format('DD/MM/YY') }}</dd>
                        </div>
                        <div class="flex justify-between py-3 text-sm font-medium">
                          <dt class="text-gray-500">Costo Total:</dt>
                          <dd class="text-gray-900">
                            {{ formatCurrency(data.total_cost) }}</dd>
                        </div>
                        <div class="flex justify-between py-3 text-sm font-medium">
                          <dt class="text-gray-500">Estado:</dt>
                          <dd class="text-gray-900">
                            <span
                              :class="[data.state == 'LIQUIDADO' ? 'bg-' + colors[0] + '-500' : 'bg-' + colors[1] + '-500', 'inline-flex items-center rounded-md px-2 py-1 text-xs font-medium text-white ring-1 ring-inset ring-green-600/20']">
                              <i :class="[data.state == 'LIQUIDADO' ? icons[0] : icons[1]]" class="mr-2"
                                aria-hidden="true" />
                              {{ data.state }}
                            </span>
                          </dd>
                        </div>
                      </dl>
                    </div>
                  </header>
                  <p v-if="data.projects.length > 0" class="font-bold text-center">Proyectos</p>
                  <span v-for="(project, index) in data.projects" :key="index" class="grid gap-y-2 my-2">
                    <div class="border-2 p-1 rounded-lg cursor-default">
                      <span class="flex justify-between items-center">
                        <Link :href="route('projects.goToProjectOverview', project.id)"
                          v-tooltip.left="'Nombre del proyecto'" class="font-bold"> {{ project.name }}</Link>
                        <span v-tooltip.top="'Codigo SAP'" class="text-sm">{{ project.SAP_code }}</span>
                      </span>
                      <span class="text-xs" v-tooltip.top="'Tipo de proyecto'">{{ project.type }}</span>
                      <span class="flex justify-between items-center">
                        <Tag severity="info" v-tooltip.left="'Estado'" :value="project.status" />
                        <span class="text-sm" v-tooltip.left="'Gerencia'">{{ project.gerencia }}</span>
                      </span>
                    </div>
                  </span>
                  <!-- <Accordion>
                    <AccordionTab v-for="product in  data.products " :pt="{
                      root: '!border-0 !ring-0',
                      headerAction: '!bg-slate-200 !px-4 !py-1 mb-1',
                      headerTitle: '!text- sm'
                    }">
                      <template #header>
                        <div class="block gap-2 w-full">
                          <p class="white-space-nowrap font-semibold">{{ product.name }}</p>
                          <p class=" white-space-nowrap text-xs">{{
                            formatCurrency(product.price_before_iva_original)
                          }}</p>

                        </div>
                      </template>
<div class="space-y-6">
  <div>
    <h3 class="font-medium text-gray-900">Información del producto</h3>
    <dl class="mt-2 divide-y divide-gray-200 border-b border-t border-gray-200">
      <div class="flex justify-between py-3 text-sm font-medium">
        <dt class="text-gray-500">Clase:</dt>
        <dd class="text-gray-900">{{ product.name }}</dd>
      </div>
      <div class="flex justify-between py-3 text-sm font-medium">
        <dt class="text-gray-500">Alcance:</dt>
        <dd class="text-gray-900">{{ product.scope == null ? 'N/A' :
          product.scope }}</dd>
      </div>
      <div class="flex justify-between py-3 text-sm font-medium">
        <dt class="text-gray-500">Madurez:</dt>
        <dd class="text-gray-900">{{ product.maturity == null ? 'N/A' :
          product.maturity }}</dd>
      </div>
      <div class="flex justify-between py-3 text-sm font-medium">
        <dt class="text-gray-500">Cantidad:</dt>
        <dd class="text-gray-900">{{ product.units == null ? 'N/A' :
          product.units }}</dd>
      </div>
    </dl>
  </div>
  <div>
    <h3 class="font-medium text-gray-900">Información de la Oferta</h3>
    <dl class="mt-2 divide-y divide-gray-200 border-b border-t border-gray-200">
      <div class="flex justify-between py-3 text-sm font-medium">
        <dt class="text-gray-500">Margen Estimado:</dt>
        <dd class="text-gray-900">{{ product.margin }}%</dd>
      </div>
      <div class="flex justify-between py-3 text-sm font-medium">
        <dt class="text-gray-500">Precio Original:</dt>
        <dd class="text-gray-900">{{
          formatCurrency(product.price_before_iva_original) }}</dd>
      </div>
      <div class="flex justify-between py-3 text-sm font-medium">
        <dt class="text-gray-500">Tasa de Venta:</dt>
        <dd class="text-gray-900">0</dd>
      </div>
      <div class="flex justify-between py-3 text-sm font-medium">
        <dt class="text-gray-500">IVA:</dt>
        <dd class="text-gray-900">{{ product.iva }}</dd>
      </div>
    </dl>
  </div>
</div>
</AccordionTab>
</Accordion> -->
                </div>
              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>
