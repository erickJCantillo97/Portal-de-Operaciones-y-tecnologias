<script setup>
import CustomUpload from '@/Components/CustomUpload.vue';
</script>
<template>
  <div class="flex justify-center">
    <CustomUpload mode="advanced" label-button="Subir Archivo de clases de Costo" titleModal="Clases de costo"
      tooltip="Subir Clases" accept=".xlsx,.xls" :url="route('dashboard')" severity="success" />
  </div>
</template>
