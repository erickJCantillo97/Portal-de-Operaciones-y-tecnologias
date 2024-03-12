<script setup>
import { commonUtilities } from '@/composable/commonUtilities'
import { ref } from 'vue'
import Footer from '@/Components/Footer.vue'
import Galleria from 'primevue/galleria'
import TabPanel from 'primevue/tabpanel'
import TabView from 'primevue/tabview'

const { currencyFormat } = commonUtilities()

const props = defineProps({
  tool: Object
})

//#region Unsplash API
// const KEY = 'X84hXPJtgOVrB9c-mn_bj4K2elpN5-YNm08ACNxVUm4'

// const images = ref({
//   thumb: 'https://images.unsplash.com/photo-1705247492538-bcef75c74f68?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w1NjQwMjV8MHwxfHJhbmRvbXx8fHx8fHx8fDE3MDcyNDYzNjl8&ixlib=rb-4.0.3&q=80&w=400'
// })

// const getImages = async () => {
//   await axios.get('https://api.unsplash.com/search/photos?page=1&query=' + props.tool.name + `&client_id=${KEY}`)
//     .then(res => {
//       images.value.src = res.data.results[0].urls.regular
//     })
// }

// onMounted(() => {
//   getImages()
// })
//#endregion

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
  {
    id: 3,
    src: 'https://primefaces.org/cdn/primevue/images/galleria/galleria3.jpg',
    thumb: 'https://primefaces.org/cdn/primevue/images/galleria/galleria3s.jpg',
    alt: 'image-1'
  },
  {
    id: 4,
    src: 'https://primefaces.org/cdn/primevue/images/galleria/galleria4.jpg',
    thumb: 'https://primefaces.org/cdn/primevue/images/galleria/galleria4s.jpg',
    alt: 'image-1'
  },
  {
    id: 5,
    src: 'https://primefaces.org/cdn/primevue/images/galleria/galleria5.jpg',
    thumb: 'https://primefaces.org/cdn/primevue/images/galleria/galleria5s.jpg',
    alt: 'image-1'
  },
  {
    id: 6,
    src: 'https://primefaces.org/cdn/primevue/images/galleria/galleria6.jpg',
    thumb: 'https://primefaces.org/cdn/primevue/images/galleria/galleria6s.jpg',
    alt: 'image-1'
  },
  {
    id: 7,
    src: 'https://primefaces.org/cdn/primevue/images/galleria/galleria7.jpg',
    thumb: 'https://primefaces.org/cdn/primevue/images/galleria/galleria7s.jpg',
    alt: 'image-1'
  },
  {
    id: 8,
    src: 'https://primefaces.org/cdn/primevue/images/galleria/galleria8.jpg',
    thumb: 'https://primefaces.org/cdn/primevue/images/galleria/galleria8s.jpg',
    alt: 'image-1'
  },
  {
    id: 9,
    src: 'https://primefaces.org/cdn/primevue/images/galleria/galleria9.jpg',
    thumb: 'https://primefaces.org/cdn/primevue/images/galleria/galleria9s.jpg',
    alt: 'image-1'
  },
  {
    id: 10,
    src: 'https://primefaces.org/cdn/primevue/images/galleria/galleria10.jpg',
    thumb: 'https://primefaces.org/cdn/primevue/images/galleria/galleria10s.jpg',
    alt: 'image-1'
  }
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
</script>
<!-- <style scoped>
.custom {
  mask-image: linear-gradient(to bottom,white 50%, transparent 95%);
}
</style> -->

<template>
  <main class="flex flex-col max-w-full max-h-screen justify-center overflow-hidden bg-gray-800 md:py-6">
    <section
      class="grid grid-cols-1 h-screen overflow-y-auto space-y-6 bg-white px-6 pt-2  shadow-lg shadow-gray-600 ring-1 ring-gray-900/5 sm:w-[70rem] md:h-screen md:grid-cols-4 md:space-x-6 md:mx-auto md:rounded-lg md:pb-8 md:px-10 md:pt-10">
      <!--Tool Details-->
      <article class="col-span-1 md:col-span-2">
        <div class="flex text-sm font-medium items-center">
          <!-- <dt class="text-gray-500">Equipo:</dt> -->
          <i class="fa-solid fa-screwdriver-wrench text-blue-700"></i>
          <h1 class="text-lg font-semibold text-blue-800 px-4 uppercase">
            {{ tool.name }}
          </h1>
        </div>
        <div>
          <h2 class="text-md font-semibold text-gray-500 italic">
            Serial: {{ tool.serial }}
          </h2>
        </div>
        <div>
          <h2 class="text-sm font-semibold mb-2 text-gray-500 italic">
            Gerencia: {{ tool.gerencia }}
          </h2>
        </div>
        <TabView :scrollable="true" :pt="{
              nav: '!flex !justify-between'
            }">
          <TabPanel header="Detalles del Equipo" :pt="{
              content: '!h-[18rem] !p-2 !overflow-y-auto'
            }">
            <div class="custom border border-solid rounded-lg p-2 mb-2">
              <dl class="divide-y divide-gray-200 border-b border-t border-gray-200">
                <div class="flex justify-between py-3 text-sm font-medium">
                  <dt class="text-gray-900">Código:</dt>
                  <dd class="text-gray-500 uppercase">{{ tool.code }}</dd>
                </div>
                <div class="flex justify-between py-3 text-sm font-medium">
                  <dt class="text-gray-900">Gerencia:</dt>
                  <dd class="text-gray-500 uppercase">{{ tool.gerencia }}</dd>
                </div>
                <div class="flex justify-between py-3 text-sm font-medium">
                  <dt class="text-gray-900">Costo:</dt>
                  <dd class="text-gray-500 uppercase">{{ currencyFormat(tool.value, 'COP') }}</dd>
                </div>
                <div class="flex justify-between py-3 text-sm font-medium">
                  <dt class="text-gray-900">Fecha de préstamo:</dt>
                  <dd class="text-gray-500 uppercase">{{ tool.entry_date }}</dd>
                </div>
                <div class="flex justify-between py-3 text-sm font-medium">
                  <dt class="text-gray-900">Estado:</dt>
                  <dd class="p-2 bg-emerald-400 rounded-lg text-white text-xs animate-pulse">{{ tool.estado }}</dd>
                </div>
                <div class="flex justify-between py-3 text-sm font-medium">
                  <dt class="text-gray-900">Estado Operativo:</dt>
                  <dd class="text-gray-500 uppercase">{{ tool.estado_operativo }}</dd>
                </div>
                <div class="flex justify-between py-3 text-sm font-medium">
                  <dt class="text-gray-900">Categoría:</dt>
                  <dd class="text-gray-500 uppercase">{{ tool.category.name }}</dd>
                </div>
                <div class="flex justify-between py-3 text-sm font-medium">
                  <dt class="text-gray-900">Funcion:</dt>
                  <dd class="text-gray-500 uppercase">{{ tool.category.padre.name }}</dd>
                </div>
                <div v-if="tool.description != null" class="flex justify-between py-3 text-sm font-medium">
                  <dt class="text-gray-900">Categoría:</dt>
                  <dd class="text-gray-500 uppercase">{{ tool.category.padre.padre.name }}</dd>
                </div>
                <div class="flex justify-between py-3 text-sm font-medium">
                  <dt class="text-gray-900">Categoría:</dt>
                  <dd class="text-gray-500 uppercase">{{ tool.category.name }}</dd>
                </div>
              </dl>
            </div>
          </TabPanel>
          <TabPanel header="Lista de Préstamos" :pt="{
              content: '!h-[18rem] !p-2 !overflow-y-auto'
            }">
            <p class="m-0">
              Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam
              rem
              aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt
              explicabo.
              Nemo enim
              ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos
              qui ratione voluptatem sequi nesciunt. Consectetur, adipisci velit, sed quia non numquam eius modi.
            </p>
          </TabPanel>
          <TabPanel header="Otros Datos" :pt="{
              content: '!h-[18rem] !p-2 !overflow-y-auto'
            }">
            <p class="m-0">
              At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti
              atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique
              sunt in culpa qui
              officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et
              expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo
              minus.
            </p>
          </TabPanel>
        </TabView>
        <Button class="w-full mt-1 md:mt-1" label="Reasignar" rounded small />
      </article>
      <!--Galería-->
      <article class="flex col-span-1 md:flex md:col-span-2 md:justify-center md:items-center">
        <!-- <img :src="images.src" class="size-[24rem] rounded-lg shadow-md object-cover" alt="tool-image"> -->
        <Galleria :value="imageSrc" :responsiveOptions="responsiveOptions" numVisible="5" indicatorsPosition="bottom"
          :circular="true" :autoPlay="true" :transitionInterval="2000" :showThumbnails="false" :showIndicators="true"
          :changeItemOnIndicatorHover="true" containerStyle="max-width: 640px" :pt="{
              itemContainer: '!border !border-gray-200 !rounded-lg',
              indicatorButton: '!bg-blue-800'
              // thumbnailItemsContainer: '!opacity-0',
            }">
          <template #item="slotProps">
            <img :src="slotProps.item.src" :alt="slotProps.item.alt" />
          </template>
          <template #thumbnail="slotProps">
            <img :src="slotProps.item.thumb" :alt="slotProps.item.alt" style="width: 100%;" />
          </template>
        </Galleria>
      </article>
    </section>
    <Footer fontSize="sm" fontColor="white" marginTop="4" />
  </main>
</template>
