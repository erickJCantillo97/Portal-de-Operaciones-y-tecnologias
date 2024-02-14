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
  <div class="flex flex-wrap justify-center items-center w-36 h-14 bg-slate-100 p-2 m-2 shadow-md">
    <h3 class="text-xs font-semibold">{{ props.title }}</h3>
    <p class="text-xs">{{ formatCurrency(props.value, 'COP') }}</p>
    <ProgressBar :value="valueProgressBar" showValue :pt="{
      root: '!w-full !rounded-lg',
      label: '!text-[2px]',
      value: { style: { background: 'linear-gradient(to right, #8e2de2, #4a00e0)' } }
    }" />
  </div>
</template>
