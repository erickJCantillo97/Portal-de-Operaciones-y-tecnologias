<script setup>
import { ref, onMounted } from 'vue'
import { Dialog, DialogPanel, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { HeartIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { PencilIcon, PlusIcon } from '@heroicons/vue/20/solid'
import Moment from 'moment'
import Accordion from 'primevue/accordion'
import AccordionTab from 'primevue/accordiontab'
import Tag from 'primevue/tag'
import CustomModal from '@/Components/CustomModal.vue'
import Dropdown from 'primevue/dropdown'
import Calendar from 'primevue/calendar'
import Button from 'primevue/button'

onMounted(() => {
  // getVersions()
})

const checked = ref(false)
const dateSelected = ref()
const openStatusDialog = ref(false)
const showDateResponse = ref(true)
const statusSelected = ref()
const statusOptions = ref([
  'Proceso',
  'Entregada',
  'Pendinete por firma',
  'Firmada',
  'No Firmada',
  'Contratada'
])

//#region fakeData
const listOfVersions = [
  {
    id: '1'
  },
  {
    id: '2'
  },
  {
    id: '3'
  },
  {
    id: '4'
  },
  {
    id: '5'
  },
  {
    id: '6'
  },
  {
    id: '7'
  },
  {
    id: '8'
  },
  {
    id: '9'
  },
  {
    id: '10'
  },
  {
    id: '11'
  },
  {
    id: '12'
  },
]
//#endregion

const props = defineProps(
  {
    openSlideOver: {
      type: Boolean,
      default: false
    },
    quote: {
      type: Object,
      required: false
    }
  }
)

const openDialog = () => {
  openStatusDialog.value = true
}

const saveStatus = () => {
  console.log('Hello!')
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
//#endregion

// Formatear el número en moneda (USD)
const formatCurrency = (value) => {
  return parseFloat(value).toLocaleString('es-CO', {
    style: 'currency',
    currency: 'COP',
  })
}
</script>
<template>
  <TransitionRoot as="template" :show="props.openSlideOver">
    <Dialog as="div" class="relative z-10" @close="open = false">
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
                      <XMarkIcon class="h-6 w-6" aria-hidden="true" />
                    </button>
                  </div>
                </TransitionChild>
                <div class="h-full overflow-y-auto bg-white p-2">
                  <div class="rounded-lg bg-blue-900 text-white uppercase p-2 mb-2">
                    <h2 class="text-md text-center font-bold text-white">{{ quote.name }} {{ quote.consecutive
                    }}-{{ quote.version }}-2023
                    </h2>
                  </div>
                  <header class="w-full">
                    <div class="flex flex-nowrap text-center justify-center items-center" v-if="quote.version > 1">
                      <ul class="hover:border-b text-md text-center font-semibold text-blue-900 w-10 cursor-pointer"
                        v-for="version in quote.version">
                        <li>{{ version }}</li>
                      </ul>
                    </div>
                    <div class="flex flex-nowrap space-x-2 p-2 justify-center rounded-lg">
                      <button type="button" @click="openDialog()"
                        class="flex flex-nowrap justify-center items-center space-x-2 bg-emerald-600 p-2 cursor-pointer rounded-lg hover:bg-emerald-500">
                        <i class="fa-regular fa-rectangle-list fa-xl" style="color: #ffffff;"></i>
                        <p class="text-md text-center font-bold text-white">Estados</p>
                      </button>
                      <button type="button"
                        class="flex flex-nowrap justify-center items-center space-x-2 bg-orange-600 p-2 cursor-pointer rounded-lg hover:bg-orange-500">
                        <i class="fa-regular fa-comment-dots fa-xl" style="color: #ffffff;"></i>
                        <p class="text-md text-center font-bold text-white">Comentarios</p>
                      </button>
                    </div>
                    <div class="border border-solid rounded-lg p-2 mb-2">
                      <dl class="divide-y divide-gray-200 border-b border-t border-gray-200">
                        <div class="flex justify-between py-3 text-sm font-medium">
                          <dt class="text-gray-500">Estimador:</dt>
                          <dd class="text-gray-900 uppercase">{{ quote.estimador }}</dd>
                        </div>
                        <div class="flex justify-between py-3 text-sm font-medium">
                          <dt class="text-gray-500">Cliente:</dt>
                          <dd class="text-gray-900">{{ quote.customer }}</dd>
                        </div>
                        <div class="flex justify-between py-3 text-sm font-medium">
                          <dt class="text-gray-500">Fecha de Solicitud:</dt>
                          <dd class="text-gray-900">{{ Moment(quote.created_at).format('DD/MM/YY') }}</dd>
                        </div>
                        <div class="flex justify-between py-3 text-sm font-medium">
                          <dt class="text-gray-500">Fecha Respuesta Esperada:</dt>
                          <dd class="text-gray-900">{{ Moment(quote.expeted_answer_date).format('DD/MM/YY') }}</dd>
                        </div>
                        <div class="flex justify-between py-3 text-sm font-medium">
                          <dt class="text-gray-500">Estado:</dt>
                          <dd class="text-gray-900">
                            <Tag icon="pi pi-times" severity="danger" value="No Firmado"></Tag>
                          </dd>
                        </div>
                      </dl>
                    </div>
                  </header>
                  <Accordion :activeIndex="0">
                    <AccordionTab :header="product.name" :pt="{
                      root: '!border-0 !ring-0',
                      headerAction: '!bg-slate-200',
                      headerTitle: '!font-semibold',
                    }" v-for="product in quote.products">
                      <div class="space-y-6 pb-16">
                        <div>
                          <h3 class="font-medium text-gray-900">Información del producto</h3>
                          <dl class="mt-2 divide-y divide-gray-200 border-b border-t border-gray-200">
                            <div class="flex justify-between py-3 text-sm font-medium">
                              <dt class="text-gray-500">Clase:</dt>
                              <dd class="text-gray-900">{{ product.name }}</dd>
                            </div>
                            <div class="flex justify-between py-3 text-sm font-medium">
                              <dt class="text-gray-500">Tipo de proyecto:</dt>
                              <dd class="text-gray-900">Artefacto Naval</dd>
                            </div>
                            <div class="flex justify-between py-3 text-sm font-medium">
                              <dt class="text-gray-500">Alcance:</dt>
                              <dd class="text-gray-900">Construcción</dd>
                            </div>
                            <div class="flex justify-between py-3 text-sm font-medium">
                              <dt class="text-gray-500">Madurez:</dt>
                              <dd class="text-gray-900">Contractual</dd>
                            </div>
                            <div class="flex justify-between py-3 text-sm font-medium">
                              <dt class="text-gray-500">Cantidad:</dt>
                              <dd class="text-gray-900">2</dd>
                            </div>
                          </dl>
                        </div>
                        <div>
                          <h3 class="font-medium text-gray-900">Información de la Oferta</h3>
                          <dl class="mt-2 divide-y divide-gray-200 border-b border-t border-gray-200">
                            <div class="flex justify-between py-3 text-sm font-medium">
                              <dt class="text-gray-500">Versión:</dt>
                              <dd class="text-gray-900">1</dd>
                            </div>
                            <div class="flex justify-between py-3 text-sm font-medium">
                              <dt class="text-gray-500">Tipo de Oferta:</dt>
                              <dd class="text-gray-900">ROM</dd>
                            </div>
                            <div class="flex justify-between py-3 text-sm font-medium">
                              <dt class="text-gray-500">Margen Estimado:</dt>
                              <dd class="text-gray-900">25.3%</dd>
                            </div>
                            <div class="flex justify-between py-3 text-sm font-medium">
                              <dt class="text-gray-500">Precio Original:</dt>
                              <dd class="text-gray-900">{{ formatCurrency('1200000000') }}</dd>
                            </div>
                            <div class="flex justify-between py-3 text-sm font-medium">
                              <dt class="text-gray-500">Tasa de Venta:</dt>
                              <dd class="text-gray-900">0</dd>
                            </div>
                            <div class="flex justify-between py-3 text-sm font-medium">
                              <dt class="text-gray-500">IVA:</dt>
                              <dd class="text-gray-900">19%</dd>
                            </div>
                            <div v-if="showDateResponse" class="flex justify-between py-3 text-sm font-medium">
                              <dt class="text-gray-500">Fecha de Respuesta:</dt>
                              <dd class="text-gray-900">{{ Moment().add(6, 'days').format('DD/MM/YY') }}</dd>
                            </div>
                          </dl>
                        </div>
                        <!-- <div>
                          <h3 class="font-medium text-gray-900">Description</h3>
                          <div class="mt-2 flex items-center justify-between">
                            <p class="text-sm italic text-gray-500">Add a description to this image.</p>
                            <button type="button"
                              class="relative -mr-2 flex h-8 w-8 items-center justify-center rounded-full bg-white text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                              <span class="absolute -inset-1.5" />
                              <PencilIcon class="h-5 w-5" aria-hidden="true" />
                              <span class="sr-only">Add description</span>
                            </button>
                          </div>
                        </div>
                        <div>
                          <h3 class="font-medium text-gray-900">Shared with</h3>
                          <ul role="list" class="mt-2 divide-y divide-gray-200 border-b border-t border-gray-200">
                            <li class="flex items-center justify-between py-3">
                              <div class="flex items-center">
                                <img
                                  src="https://images.unsplash.com/photo-1502685104226-ee32379fefbe?ixlib=rb-=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=3&w=1024&h=1024&q=80"
                                  alt="" class="h-8 w-8 rounded-full" />
                                <p class="ml-4 text-sm font-medium text-gray-900">Aimee Douglas</p>
                              </div>
                              <button type="button"
                                class="ml-6 rounded-md bg-white text-sm font-medium text-indigo-600 hover:text-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Remove<span
                                  class="sr-only"> Aimee Douglas</span></button>
                            </li>
                            <li class="flex items-center justify-between py-3">
                              <div class="flex items-center">
                                <img
                                  src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-1.2.1&ixqx=oilqXxSqey&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                                  alt="" class="h-8 w-8 rounded-full" />
                                <p class="ml-4 text-sm font-medium text-gray-900">Andrea McMillan</p>
                              </div>
                              <button type="button"
                                class="ml-6 rounded-md bg-white text-sm font-medium text-indigo-600 hover:text-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Remove<span
                                  class="sr-only"> Andrea McMillan</span></button>
                            </li>
                            <li class="flex items-center justify-between py-2">
                              <button type="button"
                                class="group -ml-1 flex items-center rounded-md bg-white p-1 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <span
                                  class="flex h-8 w-8 items-center justify-center rounded-full border-2 border-dashed border-gray-300 text-gray-400">
                                  <PlusIcon class="h-5 w-5" aria-hidden="true" />
                                </span>
                                <span
                                  class="ml-4 text-sm font-medium text-indigo-600 group-hover:text-indigo-500">Share</span>
                              </button>
                            </li>
                          </ul>
                        </div> -->
                        <div>
                          <h3 class="font-medium text-gray-900">Documentos</h3>
                          <div class="divide-y divide-gray-200 border-b border-t border-gray-200"></div>
                        </div>
                        <div class="grid grid-cols-2 space-x-2 text-center">
                          <div class="col-span-1 space-y-2 items-center">
                            <button type="button"
                              class="flex-1 w-full rounded-md bg-emerald-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600">Clonar</button>
                            <button type="button"
                              class="flex-1 w-full rounded-md bg-orange-600 px-3 py-2 text-sm font-semibold text-white shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-orange-500">Editar</button>
                          </div>
                          <div class="col-span-1 space-y-2 items-center">
                            <button type="button"
                              class="flex-1 w-full rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Actualizar</button>
                            <button type="button"
                              class="flex-1 w-full rounded-md bg-red-600 text-white px-3 py-2 text-sm font-semibold  shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-red-500">Eliminar</button>
                          </div>
                        </div>
                      </div>
                    </AccordionTab>
                  </Accordion>
                </div>
              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>

  <CustomModal :visible="openStatusDialog" :maximizable="true">
    <template #icon>
      <span class="text-white material-symbols-outlined text-4xl">
        engineering
      </span>
    </template>
    <template #titulo>
      <span class="text-xl font-bold text-white white-space-nowrap">Cambiar Estado</span>
    </template>
    <template #body>
      <div class="flex flex-nowrap space-x-2">
        <div>
          <label for="dd-city">Estado de la Estimación</label>
          <Dropdown v-model="statusSelected" :options="statusOptions" showClear
            placeholder="Seleccione Estado de la estimación" class="w-full md:w-14rem" />
        </div>
        <div>
          <label for="dd-city">Seleccione Fecha</label>
          <Calendar v-model="dateSelected" showIcon />
        </div>
        <div>
          <Button @click="saveStatus()" label="Guardar" icon="pi pi-save" severity="success" raised />
        </div>
      </div>
      <div>
        <ul>
          <li>asdasdasdas</li>
        </ul>
      </div>
    </template>
  </CustomModal>
</template>
