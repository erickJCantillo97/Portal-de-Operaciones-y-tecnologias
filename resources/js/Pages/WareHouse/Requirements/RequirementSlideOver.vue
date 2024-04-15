<script setup>
import { Dialog, DialogPanel, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { XMarkIcon } from '@heroicons/vue/24/outline'
import { ref } from 'vue'
import Accordion from 'primevue/accordion';
import AccordionTab from 'primevue/accordiontab';
import DescriptionItem from '@/Components/DescriptionItem.vue'
import Loading from '@/Components/Loading.vue'
import ListBox from 'primevue/listbox';

const emit = defineEmits(['closeSlideOver'])

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
  }
})

const optionStatus = {
  'PENDIENTE': {
    icon: 'fa-solid fa-user-clock',
    color: 'bg-orange-600 text-white'
  },
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
                <div class="h-full overflow-y-auto bg-white ">
                  <div class="w-full bg-blue-900 text-white uppercase p-2">
                    <h2 class="text-lg text-center font-bold text-white">
                      Requerimiento
                    </h2>
                  </div>
                  <article class="w-full p-2">
                    <div class=" border border-solid rounded-lg p-2 mb-2">
                      <!-- {{ requirement }} -->
                      <DescriptionItem :data="requirement" />
                    </div>
                    <div class="border border-solid rounded-lg mb-2">

                    </div>
                    <div class="border border-solid rounded-lg mb-2">
                      <div class="mb-4">
                        <h3 class="font-semibold p-1 rounded-t-xl text-center bg-primary text-white">
                          Lista de Materiales
                        </h3>
                        <!-- <div v-if="loadingMaterials">
                          <Loading message="Cargando Lista de Materiales" />
                        </div> -->
                        <div class="shadow-md my-4 px-2 h-full" v-for="material in materials">
                          <Accordion expandIcon="pi pi-plus" collapseIcon="pi pi-minus" :pt="{
    content: '!h-[80vh] !p-2 !overflow-y-auto'
  }">
                            <AccordionTab :activeIndex="0" :header="`${material.material}`">
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
