<script setup>
import ProgressBar from 'primevue/progressbar'

const props = defineProps({
  title: {
    type: String,
    default: 'Put a title here',
    required: true
  },
  value: {
    type: Number,
    default: 0,
    required: true
  },
  valueProgressBar: {
    type: Number,
    default: 50,
    required: true
  }
})

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
  <div class="block text-center  space-y-1 bg-slate-100 p-2 shadow-md">
    <h3 class="text-sm text-primary font-bold">{{ props.title }}</h3>
    <p class="text-md font-semibold">{{ formatCurrency(props.value, 'COP') }}</p>
  </div>
</template>
