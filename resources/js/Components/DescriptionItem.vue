<script setup>
const props = defineProps({
  label: Array,
  field: Array,
  data: Object,
  optionStatus: Object
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
        <dt class="text-gray-900 capitalize ">
          {{ item[0] }}:
        </dt>
        <dd v-if="item[0] == 'state' || item[0] == 'status' || item[0] == 'estado' || item[0] == 'Estado'"
          class="p-1 rounded-md " :class="optionStatus[getFieldValue(item[0])]?.color">
          <i :class="optionStatus[getFieldValue(item[0])]?.icon"></i>
          {{ getFieldValue(item[0]) || 'SIN DEFINIR' }}
        </dd>
        <dd v-else :class="'text-gray-500 uppercase'">
          {{ getFieldValue(item[0]) || 'SIN DEFINIR' }}
        </dd>
      </div>
    </dl>
  </span>
</template>
