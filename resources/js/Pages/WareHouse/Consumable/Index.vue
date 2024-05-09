<script setup>
//#region Imports
import { ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import CustomDataTable from '@/Components/CustomDataTable.vue'
import CustomModal from '@/Components/CustomModal.vue';
import CustomInput from '@/Components/CustomInput.vue';
//#endregion



const requirements = [
]

const materials = [
]

const columns = [
  { field: 'codigo_material', header: 'Código del Material', filter: "true" },
  { field: 'descripcion', header: 'Descripción', filter: 'true' },
  { field: 'Cantidad', header: 'Cantidad', filter: true },
  { field: 'precio_unitario', header: 'Precio Unitario', filter: true },
  { field: 'subtotal', header: 'Subtotal', filter: true },
]

const codigo_salida = ref()
const openModal = ref(false)

const addItem = () => {
  openModal.value = true
}

const search = () => {
  
}

const url = [
  {
    ruta: 'requirements.index',
    label: 'Requerimientos',
    active: true
  }
]
</script>
<template>
  <AppLayout :href="url">
    <div class="size-full">
      <CustomDataTable :data="requirements" title="Consumibles" :columnas="columns" :rowsDefault="10"
        @showSlide="showClick">
        <template #buttonHeader>
          <Button label="Cargar" severity="success" icon="fa-solid fa-plus" @click="addItem" />
        </template>
      </CustomDataTable>
    </div>

    <CustomModal v-model:visible="openModal" width="80vw">
      <template #icon>
        <i class="fa-solid fa-file-contract"></i>
      </template>

      <template #titulo>
        <p>Cargar Material</p>
      </template>

      <template #body>
        <div>
          <CustomInput placeholder="Buscar" v-model:input="codigo_salida" />
          <Button label="Buscar" severity="success" icon="fa-solid fa-plus" @click="search()" />
        </div>
        <div class="size-full">
          <CustomDataTable :data="materials" title="Materiales" :columnas="columns" :rowsDefault="10"
            @showSlide="showClick">
          </CustomDataTable>
        </div>
      </template>

      <template #footer>
        <Button severity="danger" @click="openModal = false">Cancelar</Button>
        <Button severity="success" :loading="false" @click="submit()">
          Guardar
        </Button>
      </template>
    </CustomModal>
  </AppLayout>
</template>
