<script setup>
//#region Imports
import { ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import CustomDataTable from '@/Components/CustomDataTable.vue'
import CustomModal from '@/Components/CustomModal.vue';
import CustomInput from '@/Components/CustomInput.vue';
//#endregion

const codigo_salida = ref()
const loading = ref(false)
const openModal = ref(false)

const requirements = [
]

const materials = ref([])

const columns = [
  { field: 'codigo_material', header: 'Código del Material', filter: "true" },
  { field: 'descripcion', header: 'Descripción', filter: 'true' },
  { field: 'Cantidad', header: 'Cantidad', filter: true },
  { field: 'precio_unitario', header: 'Precio Unitario', filter: true },
  { field: 'subtotal', header: 'Subtotal', filter: true },
]

//0180055002
//4900242380
const columnsModal = [
  { field: 'MATNR', header: 'Código del Material', filter: 'true' },
  { field: 'MAKTX', header: 'Descripción', filter: 'true' },
  { field: 'MENGE', header: 'Cantidad', filter: true, type: 'number' },
  { field: 'MEINS', header: 'Unidad de Medida', filter: true },
  { field: 'DMBTR', header: 'Precio Unitario', filter: true, type: 'currency' },
  { field: 'SUBTOTAL', header: 'Subtotal', filter: true, type: 'currency' },
  { field: 'WAERS', header: 'Moneda', filter: true },
]

const addItem = () => {
  openModal.value = true
}

const search = () => {
  loading.value = true
  try {
    axios.get(route('consumable.create', {
      codigo_salida: codigo_salida.value,
    })).then((res) => {
      materials.value = res.data.consumibles
      loading.value = false
    })
  } catch (error) {
    console.error('Error: ' + error)
  }
}

const cancelModal = () => {
  clearData()
}

const clearData = () => {
  openModal.value = false
  materials.value = []
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
      <CustomDataTable :data="requirements" title="Consumibles" :columnas="columns" :rowsDefault="10">
        <template #buttonHeader>
          <Button label="Cargar" severity="success" icon="fa-solid fa-plus" @click="addItem" />
        </template>
      </CustomDataTable>
    </div>

    <CustomModal v-model:visible="openModal" width="80vw">
      <template #icon>
        <i class="fa-solid fa-toolbox text-2xl font-semibold"></i>
      </template>

      <template #titulo>
        <p class="text-xl font-semibold">Cargar Material</p>
      </template>

      <template #body>
        <div class="flex justify-center w-full space-x-4 mt-4">
          <CustomInput class="w-1/4" placeholder="Buscar" v-model:input="codigo_salida" />
          <Button label="Buscar" severity="success" icon="fa-solid fa-plus" :loading @click="search()" />
        </div>
        <div class="size-full">
          <CustomDataTable :data="materials" title="Materiales" :columnas="columnsModal" :rowsDefault="10" :loading>
          </CustomDataTable>
        </div>
      </template>

      <template #footer>
        <Button severity="danger" label="Cancelar" @click="cancelModal()" />
        <Button severity="success" label="Cargar" :loading="false" />
      </template>
    </CustomModal>
  </AppLayout>
</template>
