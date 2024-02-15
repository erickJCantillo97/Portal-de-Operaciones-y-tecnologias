<script setup>
const props = defineProps({
  field: Array,
  label: Array,
  data: Object
})

const options = Object.entries(props.data)

</script>
<template>
  <span v-for="item in options">
    <dl v-if="field.includes(item[0])" class="divide-y divide-gray-200 border-b border-t border-gray-200">
      <div class="flex justify-between py-3 text-sm font-medium">
        <dt class="text-gray-900">
          {{ label[field.indexOf(item[0])] }}:
        </dt>
        <dd class="text-gray-500 uppercase"
          :class="item[1] == 'state' ? 'p-2 bg-emerald-400 rounded-lg text-white text-xs animate-pulse' : 'text-gray-500 uppercase'">
          {{ item[1] != null ? item[1] : 'SIN DEFINIR' }}
        </dd>
      </div>
    </dl>
  </span>
</template>
