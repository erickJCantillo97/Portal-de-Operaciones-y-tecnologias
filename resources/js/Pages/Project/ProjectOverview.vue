<script setup>
import { ref, onMounted } from 'vue'
import TabView from 'primevue/tabview'
import TabPanel from 'primevue/tabpanel'
import Galleria from 'primevue/galleria'
import Footer from '@/Components/Footer.vue'

const props = defineProps({
  project: Object
})

const images = ref()
const imageSrc = [
  {
    id: 1,
    src: 'https://primefaces.org/cdn/primevue/images/galleria/galleria1.jpg',
    thumb: 'https://primefaces.org/cdn/primevue/images/galleria/galleria1s.jpg',
    alt: 'image-1'
  },
  {
    id: 2,
    src: 'https://primefaces.org/cdn/primevue/images/galleria/galleria2.jpg',
    thumb: 'https://primefaces.org/cdn/primevue/images/galleria/galleria2s.jpg',
    alt: 'image-1'
  },
]

const responsiveOptions = ref([
  {
    breakpoint: '1300px',
    numVisible: 4
  },
  {
    breakpoint: '575px',
    numVisible: 1
  }
])

const formatCurrency = (valor, moneda) => {
  if (valor == undefined || valor == null) {
    return 'Sin definir'
  } else {
    return parseInt(valor).toLocaleString('es-CO',
      { style: 'currency', currency: moneda == null ? 'COP' : moneda, maximumFractionDigits: 0 })
  }
}
</script>
<template>
  <main class="flex flex-col max-w-full justify-center bg-gray-800 min-h-screen overflow-hidden">
    <section
      class="grid grid-cols-1 mx-4 overflow-y-auto space-y-6 bg-white px-6 pt-2 shadow-lg shadow-gray-600 ring-1 ring-gray-900/5  md:space-x-6  md:rounded-lg  md:px-10 h-[90vh]">
      <!--Project Details-->
      <article class="">
        <TabView :scrollable="true" :pt="{
          nav: '!flex !justify-between'
        }">
          <TabPanel header="Información del Proyecto" :pt="{
            root: 'w-full',
          }">
            <div class="grid grid-cols-2 gap-2">
              <div class="col-span-1 rounded-lg p-2 mb-2 w-full bg-gray-800 border border-solid">
                <div class="flex text-sm font-medium justify-center items-center">
                  <i class="fa-solid fa-screwdriver-wrench text-white"></i>
                  <h1 class="text-lg font-semibold text-white px-4 uppercase">
                    {{ project.name }} - {{ project.SAP_code }}
                  </h1>
                </div>
                <div class="flex justify-center items-center">
                  <h2 class="text-sm font-semibold text-blue-500 italic">
                    INFORMACIÓN DEL PROYECTO
                  </h2>
                </div>
                <dl class="divide-y divide-gray-200 border-b border-t border-gray-200">
                  <div class="flex py-3 text-xs font-medium">
                    <dt class="text-white">No. del Casco:</dt>
                    <dd class="text-gray-400 uppercase">&nbsp;{{ project.manager }}</dd>
                  </div>
                  <div class="flex py-3 text-xs font-medium">
                    <dt class="text-white">Alcance:</dt>
                    <dd class="text-gray-400 uppercase">&nbsp;{{ project.scope }}</dd>
                  </div>
                  <div class="flex py-3 text-xs font-medium">
                    <dt class="text-white">Tipo de Proyecto:</dt>
                    <dd class="text-gray-400 uppercase">&nbsp;{{ project.type }}</dd>
                  </div>
                </dl>
                <div>
                  <h2 class="text-sm text-white italic">
                    {{ project.description }}
                  </h2>
                </div>
                <div class="w-full h-full">
                  <img :src="'https://primefaces.org/cdn/primevue/images/galleria/galleria2.jpg'"
                    onerror="this.src='/public/images/generic-boat.png'"
                    class="object-cover object-center w-full h-[20rem] mr-1" />
                </div>
              </div>
              <TabView :scrollable="true" :pt="{
                nav: '!flex !justify-between'
              }">
                <TabPanel header="Información del Contrato" :pt="{
                  root: 'w-full',
                }">
                  <div class="w-full p-2 uppercase text-justify">
                    <p>
                      {{ project.contract.subject }}
                    </p>
                  </div>
                  <div class="custom border border-solid rounded-lg p-2 mb-2">
                    <dl class="divide-y divide-gray-200 border-b border-t border-gray-200">
                      <div class="flex justify-between py-3 text-sm font-medium">
                        <dt class="text-gray-900">No. del Contrato:</dt>
                        <dd class="text-gray-500 uppercase">{{ project.contract.contract_id }}</dd>
                      </div>
                      <div class="flex justify-between py-3 text-sm font-medium">
                        <dt class="text-gray-900">Estado del Contrato:</dt>
                        <dd class="text-gray-500 uppercase">{{ project.contract.state }}</dd>
                      </div>
                      <div class="flex justify-between py-3 text-sm font-medium">
                        <dt class="text-gray-900">Cliente:</dt>
                        <dd class="text-gray-500 uppercase">{{ project.contract.customer_id }}</dd>
                      </div>
                      <div class="flex justify-between py-3 text-sm font-medium">
                        <dt class="text-gray-900">Precio de Venta:</dt>
                        <dd class="text-gray-500 uppercase">{{ formatCurrency(project.contract.price, 'COP') }}</dd>
                      </div>
                      <div class="flex justify-between py-3 text-sm font-medium">
                        <dt class="text-gray-900">Fecha de Inicio:</dt>
                        <dd class="text-gray-500 uppercase">{{ project.contract.start_date }}</dd>
                      </div>
                      <div class="flex justify-between py-3 text-sm font-medium">
                        <dt class="text-gray-900">Fecha de Fin:</dt>
                        <dd class="text-gray-500 uppercase">{{ project.contract.end_date }}</dd>
                      </div>
                      <div class="flex justify-between py-3 text-sm font-medium">
                        <dt class="text-gray-900">Tipo de Venta:</dt>
                        <dd class="text-gray-500 uppercase">{{ project.contract.type_of_sale }}</dd>
                      </div>
                    </dl>
                  </div>
                </TabPanel>
                <TabPanel header="Características Técnicas" :pt="{
                  root: 'w-full',
                }">

                </TabPanel>
              </TabView>
              <!-- <div class="col-span-1 rounded-lg p-2 mb-2 w-full h-full bg-gray-500 border border-solid">
                <p>asasa</p>
              </div> -->
            </div>
          </TabPanel>
          <TabPanel header="Dashboard" :pt="{
            root: 'w-full'
          }">
            <p class="m-0">
              Aqui va el Dashboard
            </p>
          </TabPanel>
        </TabView>
      </article>
      <!--Galería-->

    </section>
    <Footer fontSize="sm" fontColor="white" marginTop="0" />
  </main>
</template>
