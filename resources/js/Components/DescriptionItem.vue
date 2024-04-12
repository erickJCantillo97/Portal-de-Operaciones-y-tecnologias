<!-- <script setup>
const props = defineProps({
  label: Array,
  field: Array,
  data: Object
});

const getFieldContent = (data, field) => {
  const keys = field.split('.'); // Separar los campos anidados por puntos
  let value = data;

  for (const key of keys) {
    if (value[key] !== undefined) {
      value = value[key]; // Acceder al siguiente nivel del objeto
    } else {
      return 'SIN DEFINIR'; // Devolver 'SIN DEFINIR' si no se encuentra el campo
    }
  }

  return value;
};

const options = Object.entries(props.data).filter(([key]) => props.field.includes(key)); // Filtrar solo los campos necesarios
</script>

<template>
  <span v-for="(item, index) of options" :key="index">
    <dl class="divide-y divide-gray-200 border-b border-t border-gray-200">
      <div class="flex justify-between py-3 text-sm font-medium">
        <dt class="text-gray-900">
          {{ label[props.field.indexOf(item[0])] }}:
        </dt>
        <dd
          :class="item[0] == 'state' || item[0] == 'status' || item[0] == 'estado' ? 'p-2 bg-emerald-400 rounded-lg text-white text-xs animate-pulse' : 'text-gray-500 uppercase'">
          {{ getFieldContent(props.data, item[0]) }}
        </dd>
      </div>
    </dl>
  </span>
</template> -->

<script setup>
const props = defineProps({
  label: Array,
  field: Array,
  data: Object
})

const options = Object.entries(props.data)

const getFieldValue = (field) => {
  const fields = field.split('.');
  let value = props.data;
  for (const f of fields) {
    if (value[f] === undefined) return undefined;
    value = value[f];
  }
  return value;
}
</script>
<template>
  <span v-for="(item, index) of options" :key="index">
    <dl v-if="getFieldValue(item[0]) !== undefined && item[0] !== 'id'"
      class="divide-y divide-gray-200 border-b border-t border-gray-200">
      <div class="flex justify-between py-3 text-sm font-medium">
        <dt class="text-gray-900 capitalize">
          {{ item[0] }}:
        </dt>
        <dd
          :class="item[0] == 'state' || item[0] == 'status' || item[0] == 'estado' ? 'p-2 bg-emerald-400 rounded-lg text-white text-xs animate-pulse' : 'text-gray-500 uppercase'">
          {{ getFieldValue(item[0]) || 'SIN DEFINIR' }}
        </dd>
      </div>
    </dl>
  </span>
</template>
