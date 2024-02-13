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
  class: {
    type: String,
    default: ''
  }
})
</script>

<template>
  <section :class="class" class="absolute bottom-0 py-1 flex justify-center items-center w-full text-center">
    <footer :class="`text-${props.fontSize} text-${props.fontColor}`" class="italic">
      © TOP - COTECMAR {{ moment().format('YYYY') }} - Todos los derechos reservados.
      <div class="inline-block w-36 bg-blue-800">
        <span class="text-white ">#Seguimos </span>
        <span class="text-yellow-300">Avante</span>
      </div>
    </footer>
  </section>
</template>
