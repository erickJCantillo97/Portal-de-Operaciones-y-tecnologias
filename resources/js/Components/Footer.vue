<script setup>
import moment from "moment";

const props = defineProps({
  fontSize: {
    type: String,
    default: 'sm'
  },
  marginTop: {
    type: String,
    default: '2'
  },
  fontColor: {
    type: String,
    default: 'gray-500'
  },
})
</script>

<template>
  <section :class="`mt-${props.marginTop}`" class="relative bottom-0 w-full text-center">
    <footer :class="`text-${props.fontSize} text-${props.fontColor}`" class="italic">
      © Cotecmar {{ moment().format('DD-MM-YYYY') }} - Todos los derechos reservados.
      <div class="inline-block w-40 bg-blue-800">
        <i class="text-white ">#Seguimos </i>
        <i><strong class="text-yellow-300 ">Avante</strong></i>
      </div>
    </footer>
  </section>
</template>
