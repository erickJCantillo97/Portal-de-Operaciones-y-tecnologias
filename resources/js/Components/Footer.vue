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
  <section :class="`mt-${props.marginTop}`" class="flex justify-center items-center w-full text-center">
    <footer :class="`text-${props.fontSize} text-${props.fontColor}`" class="italic">
      © COTECMAR {{ moment().format('DD-MM-YYYY') }} - Todos los derechos reservados.
      <div class="inline-block w-40 bg-blue-800">
        <span class="text-white ">#Seguimos </span>
        <span class="text-yellow-300">Avante</span>
      </div>
    </footer>
  </section>
</template>
