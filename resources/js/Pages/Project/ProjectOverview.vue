<script setup>
import { ref } from 'vue'
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
  <main class="flex flex-col max-w-full justify-center  bg-gray-800 min-h-screen overflow-hidden">
    <section
      class="grid grid-cols-1 mx-4 overflow-y-auto space-y-6 bg-white px-6 pt-2 shadow-lg shadow-gray-600 ring-1 ring-gray-900/5  md:space-x-6  md:rounded-lg  md:px-10 h-[90vh]">
      <!--Tool Details-->
      <article class="">
        <div class="flex text-sm font-medium items-center">
          <!-- <dt class="text-gray-500">Equipo:</dt> -->
          <i class="fa-solid fa-screwdriver-wrench text-blue-700"></i>
          <h1 class="text-lg font-semibold text-blue-800 px-4 uppercase">
            {{ project.name }} - {{ project.SAP_code }}
          </h1>
        </div>
        <div>
          <h2 class="text-sm font-semibold text-gray-500 italic">
            {{ project.type }}
          </h2>
        </div>
        <TabView :scrollable="true" :pt="{
          nav: '!flex'
        }">
          <TabPanel header="Información del Proyecto" :pt="{
            root: 'w-full',
          }">
            <div class=" custom border border-solid rounded-lg p-2 mb-2">
              <dl class="divide-y divide-gray-200 border-b border-t border-gray-200">
                <div class="flex justify-between py-3 text-sm font-medium">
                  <dt class="text-gray-900">Código SAP:</dt>
                  <dd class="text-gray-500 uppercase">{{ project.SAP_code }}</dd>
                </div>
              </dl>
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
    <!-- <Footer fontSize="sm" fontColor="white" marginTop="4" /> -->
  </main>
</template>
