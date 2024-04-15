<script setup>
const { hasRole, hasPermission } = usePermissions()
import { Dialog, DialogPanel, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { Link } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import { usePermissions } from '@/composable/permission'
import { XMarkIcon } from '@heroicons/vue/24/outline'
import Accordion from 'primevue/accordion';
import AccordionTab from 'primevue/accordiontab';
import Badge from 'primevue/badge'
import DescriptionItem from '@/Components/DescriptionItem.vue'
import ListBox from 'primevue/listbox'
import Loading from '@/Components/Loading.vue'

const { emit } = defineEmits(['closeSlideOver'])

const selectedMaterial = ref()
const loadingMaterials = ref(true)

const props = defineProps({
  show: {
    default: false
  },
  requirement: {
    type: Object,
    required: true
  },
  materials: {
    type: Array,
    required: false,
    default: []
  },
  materialsLoaded: Boolean
})

watch(() => props.materialsLoaded, (newValue, oldValue) => {
  if (newValue) {
    loadingMaterials.value = false
  }
})

const optionStatus = {
  'PENDIENTE': {
    icon: 'fa-solid fa-user-clock',
    color: 'bg-orange-600 text-white'
  },
  'APROBADO': {
    icon: 'fa-solid fa-check',
    color: 'bg-emerald-600 text-white'
  },
  'RECHAZADO': {
    icon: 'fa-solid fa-xmark',
    color: 'bg-red-600 text-white'
  },
  'ELIMINADO': {
    icon: 'fa-solid fa-trash',
    color: 'bg-yellow-600 text-gray-800'
  }
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
                      Requerimientos
                    </h2>
                  </div>
                  <div class="flex gap-2 mt-14 h-12 items-center justify-center p-4">
                    <!--Botón Aprobar-->
                    <Link :href="'#'">
                    <Button v-tooltip.top="'Aprobar'" size="small" icon="pi pi-check-circle" outlined severity="success"
                      v-if="hasPermission('quote create')" />
                    </Link>

                    <!--Botón Rechazar-->
                    <Link :href="'#'">
                    <Button v-tooltip.top="'Rechazar'" size="small" icon="pi pi-times-circle" outlined
                      severity="warning" v-if="hasPermission('quote create')" />
                    </Link>

                    <!--Botón Gestionar-->
                    <Link :href="''">
                    <Button v-tooltip.top="'Gestionar'" size="small" icon="pi pi-cog" outlined severity="info"
                      v-if="hasPermission('quote create')" />
                    </Link>

                    <!--Botón Editar-->
                    <Link :href="''">
                    <Button v-tooltip.top="'Editar'" size="small" icon="pi pi-pencil" outlined severity="warning"
                      v-if="hasPermission('quote create')" />
                    </Link>

                    <!--Botón Eliminar-->
                    <Link :href="''">
                    <Button v-tooltip.top="'Eliminar'" size="small" icon="pi pi-trash" outlined severity="danger"
                      v-if="hasPermission('quote delete')" />
                    </Link>
                  </div>
                  <article class="w-full">
                    <div class="border border-solid rounded-lg p-2 mb-2">
                      <DescriptionItem :data="requirement" />
                    </div>
                    <div class="border border-solid rounded-lg mb-2">
                      <div class="mb-4">
                        <h3 class="font-semibold p-1 rounded-t-xl text-center bg-primary text-white">
                          Lista de Materiales
                        </h3>
                        <div v-if="loadingMaterials" class="mt-4">
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
