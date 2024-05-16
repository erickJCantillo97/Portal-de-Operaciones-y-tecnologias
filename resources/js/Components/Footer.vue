<script setup>

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
  <div :class class="h-12 flex justify-center w-full text-center">
    <span :class="`text-${props.fontSize} text-${props.fontColor}`" class="italic">
      © TOP - COTECMAR {{ new Date().getFullYear() }} - Todos los derechos reservados.
      <div class="inline-block w-36 bg-blue-800">
        <span class="text-white ">#Seguimos </span>
        <span class="text-yellow-300">Avante</span>
      </div>
    </span>
  </div>
</template>
