<script setup>
const props = defineProps({
  title: {
    type: String,
    default: 'Presupuesto'
  },
  value: {
    type: String,
    default: 'Presupuesto'
  }
})
</script>
<template>
  <div class="flex flex-wrap justify-center items-center w-36 h-14 bg-slate-100 p-2 m-2 shadow-md">
    <h3 class="text-xs font-semibold">Presupuesto</h3>
    <p class="text-xs">$191.192.375.980</p>
    <span class="bg-gray-600 h-2 z-10"></span>
  </div>
</template>
