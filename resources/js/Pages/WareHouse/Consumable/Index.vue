<script setup>
//#region Imports
import { ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import CustomDataTable from '@/Components/CustomDataTable.vue'
import CustomModal from '@/Components/CustomModal.vue'
import CustomInput from '@/Components/CustomInput.vue'
//#endregion

const codigo_salida = ref()
const processAndSystem = ref()
const loading = ref(false)
const openChargeModal = ref(false)
const openDischargeModal = ref(false)

const props = defineProps({
  projects: Array,
  requirements: Array,
  requirement_id: Object
})

const formData = ref({
  id: 0,
  requirement: {
    materials: [
      {
        codigo_material: '',
        cantidad: ''
      }
    ]
  }
})

const requirements = [
]

const materials = ref([])

const SWBS = [
  {
    id: 0,
    name: '100 - Casco y Estructura',
  },
  {
    id: 1,
    name: '200 - Propulsión',
  },
  {
    id: 2,
    name: '300 - Electricidad y Electrónica',
  },
  {
    id: 3,
    name: '400 - Electricidad y Electrónica',
  },
  {
    id: 4,
    name: '500 - Sistemas Auxiliares',
  },
  {
    id: 5,
    name: '600 - Habitabilidad y Matcom',
  },
  {
    id: 6,
    name: '800 - Maniobras y Soporte a la Producción',
  }
]

//#region Datatable
const columns = [
  { field: 'codigo_material', header: 'Código del Material', filter: "true" },
  { field: 'descripcion', header: 'Descripción', filter: 'true' },
  { field: 'Cantidad', header: 'Cantidad', filter: true },
  { field: 'precio_unitario', header: 'Disponible', filter: true },
  { field: 'subtotal', header: 'Consumido', filter: true },
  { field: 'subtotal', header: 'Total Disponible', filter: true },
  { field: 'subtotal', header: 'Total Consumido', filter: true },
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
//#endregion

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

//#region Charge Modal Functions
const addMaterial = () => {
  openChargeModal.value = true
}

const cancelChargeModal = () => {
  clearChargeModalData()
}

const clearChargeModalData = () => {
  openChargeModal.value = false
  materials.value = []
}
//#endregion

//#region Discharge Modal Functions
const dischargeMaterial = () => {
  openDischargeModal.value = true
}

const cancelDischargeModal = () => {
  clearDischargeModalData()
}

const clearDischargeModalData = () => {
  openDischargeModal.value = false
  materials.value = []
}
//#endregion

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
          <Button label="Cargar" severity="success" icon="fa-solid fa-plus" @click="addMaterial()" />
          <Button label="Descargar" severity="warning" icon="fa-solid fa-circle-down" @click="dischargeMaterial()" />
        </template>
      </CustomDataTable>
    </div>

    <!--MODAL DE CARGAR-->
    <CustomModal v-model:visible="openChargeModal" width="80vw">
      <template #icon>
        <i class="fa-solid fa-toolbox text-2xl font-semibold"></i>
      </template>

      <template #titulo>
        <p class="text-xl font-semibold">Cargar Material</p>
      </template>

      <template #body>
        <div class="flex justify-center w-full space-x-4 mt-4">
          <CustomInput class="w-1/2" placeholder="Buscar por código de material" v-model:input="codigo_salida" />
          <Button label="Buscar" severity="primary" icon="fa-solid fa-magnifying-glass" :loading @click="search()" />
        </div>
        <div class="size-full">
          <CustomDataTable :data="materials" title="Materiales" :columnas="columnsModal" :rowsDefault="10" :loading>
          </CustomDataTable>
        </div>
      </template>

      <template #footer>
        <Button severity="danger" label="Cancelar" @click="cancelChargeModal()" />
        <Button severity="success" label="Cargar" :loading="false" />
      </template>
    </CustomModal>

    <!--MODAL DE DESCARGAR-->
    <CustomModal v-model:visible="openDischargeModal" width="80vw">
      <template #icon>
        <i class="fa-solid fa-toolbox text-2xl font-semibold"></i>
      </template>

      <template #titulo>
        <p class="text-xl font-semibold">Descargar Material</p>
      </template>

      <template #body>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <!--Empleado-->
          <CustomInput type="dropdown" optionLabel="name" optionValue="id" :options="projects" label="Empleado"
            placeholder="Selecione un proyecto" id="empleado" v-model:input="formData.requirement.project_id"
            :invalid="$attrs.errors.project_id != null" :errorMessage="$attrs.errors.project_id" />
          <!--Supervisor-->
          <CustomInput type="dropdown" optionLabel="name" optionValue="id" :options="projects" label="Supervisor"
            placeholder="Selecione un proyecto" id="bloque" v-model:input="formData.requirement.project_id"
            :invalid="$attrs.errors.project_id != null" :errorMessage="$attrs.errors.project_id" />
          <!--Empleado-->
          <CustomInput type="dropdown" optionLabel="name" optionValue="id" :options="projects" label="Proyecto"
            placeholder="Selecione un proyecto" id="bloque" v-model:input="formData.requirement.project_id"
            :invalid="$attrs.errors.project_id != null" :errorMessage="$attrs.errors.project_id" />
          <!--Zona-->
          <CustomInput label="Zona" placeholder="Escriba la zona" id="zona"
            v-model:input="formData.requirement.document" :invalid="$attrs.errors.document != null"
            :errorMessage="$attrs.errors.document" />
          <!--Bloque-->
          <CustomInput type="number" label="Bloque" placeholder="Escriba Bloque" id="bloque"
            v-model:input="formData.requirement.bloque" :invalid="$attrs.errors.bloque != null"
            :errorMessage="$attrs.errors.bloque" />
          <!--SWBS-->
          <CustomInput type="dropdown" optionLabel="name" optionValue="id" :options="SWBS" id="swbs" label="SWBS"
            placeholder="Selecione SWBS" v-model:input="formData.requirement.SWBS"
            :invalid="$attrs.errors.proceso != null" :errorMessage="$attrs.errors.proceso" />
          <div v-if="console.log('A')" class="col-span-3 w-full">
            <div class="grid grid-cols-3 gap-4">
              <!--Proceso-->
              <CustomInput type="dropdown" optionLabel="name" optionValue="id" :options="process" id="proceso"
                label="Proceso" placeholder="Selecione un proceso" v-model:input="formData.requirement.proceso"
                :invalid="$attrs.errors.proceso != null" :errorMessage="$attrs.errors.proceso" />
              <!--Sistema-->
              <CustomInput type="dropdown" optionLabel="name" optionValue="id" :options="process" id="proceso"
                label="Sistema" placeholder="Selecione un sistema" v-model:input="formData.requirement.sistema"
                :invalid="$attrs.errors.sistema != null" :errorMessage="$attrs.errors.sistema" />
            </div>
          </div>
          <!--Agregar Materiales por Código-->
          <div class="col-span-3 space-y-2 border border-slate-300 rounded-lg p-2 ">
            <div class="flex space-x-4 justify-center items-center w-full bg-yellow-200 rounded-lg p-1">
              <h3 class="text-lg text-gray-800 font-bold">Materiales</h3>
              <Button @click="addMaterial()" severity="success" icon="fa-solid fa-plus" :pt="{
                root: '!size-6'
              }" />
            </div>
            <div class="h-80 overflow-y-auto space-y-2">
              <div v-for="(material, index) in formData.requirement.materials" :key="index"
                class="w-full border-b border-slate-300">
                <div class="flex w-full space-x-2 items-end">
                  <div class="w-full pr-2 border-r border-slate-300">
                    <label v-if="index == 0" class="text-center">
                      Codigo de material
                    </label>
                    <CustomInput placeholder="Escriba Código del Material" :id="'codigo_material_' + index"
                      v-model="material.codigo_material"
                      :invalid="material.errors && material.errors.codigo_material != null"
                      :errorMessage="material.errors && material.errors.codigo_material" />
                  </div>
                  <div class="flex space-x-4">
                    <!-- <label v-if="index == 0" class="text-center">Cantidad</label> -->
                    <CustomInput type="number" label="Cantidad" placeholder="0" :id="'cantidad_' + index"
                      v-model="material.cantidad" :invalid="material.errors && material.errors.cantidad != null"
                      :errorMessage="material.errors && material.errors.cantidad" />
                    <CustomInput type="number" label="Valor Unitario" placeholder="0" mode="currency"
                      :id="'cantidad_' + index" v-model="material.valor_unitario"
                      :invalid="material.errors && material.errors.valor_unitario != null"
                      :errorMessage="material.errors && material.errors.valor_unitario" />
                  </div>
                  <div>
                    <Button @click="removeMaterial(index)" severity="danger" icon="fa-solid fa-minus" class="h-6" />
                  </div>
                </div>
                <span class="text-xs italic text-gray-500">
                  Nombre del material a comprar
                </span>
              </div>
            </div>
          </div>
        </div>
      </template>

      <template #footer>
        <Button severity="danger" label="Cancelar" icon="fa fa-circle-xmark" @click="cancelDischargeModal()" />
        <Button severity="success" label="Descargar" icon="pi pi-save" :loading="false" />
      </template>
    </CustomModal>
  </AppLayout>
</template>
