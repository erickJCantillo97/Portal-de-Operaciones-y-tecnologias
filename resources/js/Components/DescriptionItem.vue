<script setup>
import { ref } from 'vue'

const props = defineProps({
  options: Array,
  name: {
    String,
    default: 'Name'
  }
})
</script>
<template>
  <div class="border border-solid rounded-lg p-2 mb-2">
    <dl class="divide-y divide-gray-200 border-b border-t border-gray-200">
      <div v-for="item in options.length" :key="item.id" class="flex justify-between py-3 text-sm font-medium">
        <dt class="text-gray-900">{{ item.name }}:</dt>
        <dd class="text-gray-500 uppercase">{{ item.type }}</dd>
      </div>
    </dl>
  </div>
</template>
