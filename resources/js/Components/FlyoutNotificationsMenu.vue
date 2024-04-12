<script setup>
const { toast, confirmDelete } = useSweetalert();
import { BellIcon } from '@heroicons/vue/20/solid'
import { CursorArrowRaysIcon } from '@heroicons/vue/24/outline'
import { Popover, PopoverButton, PopoverPanel } from '@headlessui/vue'
import { ref, onMounted } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import { useSweetalert } from '@/composable/sweetAlert';
import Button from 'primevue/button'
import OverlayPanel from 'primevue/overlaypanel'

const props = defineProps({
  type: {
    type: String,
    default: 'text'
  },
  title: {
    type: String,
    default: ''
  },
  label: {
    type: String,
    default: ' '
  },
  severity: {
    type: String,
    default: 'success'
  },
  badge: {
    type: Number,
    default: 0
  },
  outlined: {
    type: Boolean,
    default: false
  },
  badgeSeverity: {
    type: String,
    default: ''
  },
  icon: {
    type: String,
    default: 'fa-solid fa-dolly'
  }
})

const notifications = ref([])
const op = ref()

onMounted(() => {
  try {
    axios.get(route('getNotifications.index'))
      .then((res) => {
        notifications.value = res.data.notifications
      })
  } catch (error) {
    console.log(error)
  }
})

const deleteNotification = (id) => {
  // confirmDelete(id, 'Notificación', 'getNotifications')

  router.delete(route('getNotifications.destroy', id), {
    preserveScroll: true,
    onSuccess: () => {
      console.log('a')
    },
    onError: (error) => {
      console.log(error)
    }
  });
}

const callsToAction = [
  { name: 'Ver Todas', icon: CursorArrowRaysIcon },
  // { name: 'Contact sales', href: '#', icon: PhoneIcon },
]

const toggle = (event) => {
  op.value.toggle(event);
}
</script>

<template>


  <div class="" v-if="type == 'buttonBadge'">
    <Button v-tooltip.bottom="title" :label :severity :badge :badgeSeverity :outlined @click="toggle" outlined :pt="{
    root: 'px-1',
  }">
      <template #icon>
        <i :class="icon"></i>
      </template>
    </Button>
  </div>

  <button v-else v-tooltip.bottom="title"
    class="focus:outline-none  inline-flex items-center gap-x-1 text-sm font-semibold leading-6 text-gray-900">
    <div class="relative hover:bg-gray-100 p-1 rounded-full" @click="toggle">
      <!-- <i p-badge="2" class="pi pi-bell p-overlay-badge" style="font-size: 1rem" /> -->
      <BellIcon class="size-8 text-gray-400" aria-hidden="true" />
      <div
        :class="notifications != 0 ? 'absolute inline-flex size-4 z-[1] bg-primary rounded-full -mt-8 animate-[ping_3s_ease-in-out_infinite]' : 'hidden'">
      </div>
      <div :class="notifications != 0 ? 'absolute inline-flex size-4 bg-primary rounded-full -mt-8' : 'hidden'">
        <span class="text-white text-xs z-[2] ml-[0.3rem]">
          {{ notifications.length }}
        </span>
      </div>
    </div>
  </button>

  <OverlayPanel ref="op" class="space-y-2" :pt="{
    content: '!p-0',
    root: '!p-0 !bg-primary'
  }">
    <div class="flex w-full flex-nowrap place-content-between rounded-t-lg bg-primary px-4 pb-2 align-middle">
      <div>
        <h2 class="text-lg font-extrabold text-white">
          {{ props.title }}
        </h2>
      </div>
    </div>
    <slot />
    <a>
      <div class="grid grid-cols-1 rounded-b-lg divide-x divide-gray-900/5 bg-primary">
        <div
          class="flex items-center justify-center gap-x-2.5 p-1 font-semibold cursor-pointer text-white hover:bg-blue-800">
          <component :is="CursorArrowRaysIcon" class="size-5 flex-none text-gray-400" aria-hidden="true" />
          Ver Todas
        </div>
      </div>
    </a>
  </OverlayPanel>
</template>
