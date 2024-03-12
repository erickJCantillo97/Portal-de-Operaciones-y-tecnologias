<script setup>
import { ref, onMounted } from 'vue'
import Carousel from 'primevue/carousel'

// const props = defineProps({
//   info: {
//     type: Array,
//     required: true
//   }
// })

// const quoteInfo = ref([])

// const getQuoteInfo = async() => {
//   await axios.get(route('get.quotes.info'))
//     .then((res) => {
//       quoteInfo.value = res.data.message
//     })
// }

// onMounted(()=> {
//   getQuoteInfo()
// })

const item = [
  {
    message: '9 Dias en Promedio',
    submessage: 'De respuesta del departamento',
    icon: 'fa-solid fa-gauge'
  },
  {
    message: '2 Ofertas',
    submessage: 'Solicitadas esta semana',
    icon: 'fa-solid fa-calendar-week'
  },
  {
    message: '3 Ofertas',
    submessage: 'Solicitadas este año',
    icon: 'fa-solid fa-layer-group'
  },
  {
    message: '2 Ofertas',
    submessage: 'Contratadas este año',
    icon: 'fa-solid fa-calendar-days'
  },
]

const responsiveOptions = ref([
  {
    breakpoint: '1400px',
    numVisible: 2,
    numScroll: 1
  },
  {
    breakpoint: '1199px',
    numVisible: 3,
    numScroll: 1
  },
  {
    breakpoint: '767px',
    numVisible: 2,
    numScroll: 1
  },
  {
    breakpoint: '575px',
    numVisible: 1,
    numScroll: 1
  }
])
</script>
<template>
  <Carousel :value="item" :numVisible="2" :numScroll="2" circular :autoplayInterval="60000" :pt="{
    // indicatorButton: '!rounded-lg',
    // indicatorButton: ({ state, context }) => ({
    //   class: console.log([state], [context]) ? '!bg-blue-800' : '!bg-blue-100',
    // })
  }">
    <template #item="slotProps">
      <section class="items-center gap-x-2 mt-2 mb-2">
        <div class="col-span-1">
          <div class="flex p-2 m-2 text-center justify-center border rounded-lg
            hover:border-l-0 hover:border-t-0 hover:border-r-0 hover:border-b-2 hover:border-blue-800">
            <div class="size-12 mr-4">
              <i class="text-4xl text-blue-800" :class="slotProps.data.icon"></i>
            </div>
            <div>
              <h3 class="text-sm font-semibold">{{ slotProps.data.message }}</h3>
              <h4 class="text-xs italic">{{ slotProps.data.submessage }}</h4>
            </div>
          </div>
        </div>
      </section>
    </template>
  </Carousel>
</template>
