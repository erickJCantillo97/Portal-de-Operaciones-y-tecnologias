<script setup>
import { CheckIcon, HandThumbUpIcon, UserIcon, ClockIcon, DocumentIcon } from '@heroicons/vue/20/solid'
import { onMounted, ref } from 'vue';

const status = ref([])
const props = defineProps({
  quoteId: Number
})

onMounted(() => {
  axios.get(route('quotestatus.index', { id: props.quoteId })).then((res) => {
    status.value = res.data.status
  })
})

const icons = [
  ClockIcon,
  CheckIcon,
  DocumentIcon,
  UserIcon,
  HandThumbUpIcon,
  HandThumbUpIcon,
]

const colors = [
  'bg-red-500',
  'bg-teal-500',
  'bg-teal-500',
  'bg-teal-500',
  'bg-primary',
  'bg-teal-500',
]
</script>

<template>
  <div class="flow-root">
    <ul role="list" class="mb-8">
      <li v-for="(event, eventIdx) in status" :key="event.id">
        <div class="relative pb-8">
          <span v-if="eventIdx !== status.length - 1" class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200"
            aria-hidden="true" />
          <div class="relative flex space-x-3">
            <div>
              <span
                :class="[colors[event.status], 'h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white']">
                <component :is="icons[event.status]" class="h-5 w-5 text-white" aria-hidden="true" />
              </span>
            </div>
            <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
              <div>
                <p class="text-sm text-gray-500">
                  {{ event.get_status }} <a class="font-medium text-gray-900">{{ event.user.short_name }}</a>
                </p>
              </div>
              <div class="whitespace-nowrap text-right text-sm text-gray-500">
                <time :datetime="event.fecha">{{ event.fecha }}</time>
              </div>
            </div>
          </div>
        </div>
      </li>
    </ul>
  </div>
</template>
