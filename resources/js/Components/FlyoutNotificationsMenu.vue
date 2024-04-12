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
    type: String,
    default: '2'
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
  <Popover class="relative">
    <PopoverButton v-if="icon == 'bellIcon'"
      class="focus:outline-none inline-flex items-center gap-x-1 text-sm font-semibold leading-6 text-gray-900">
      <div class="relative mt-2 hover:bg-gray-100 p-1 rounded-full">
        <!-- <i p-badge="2" class="pi pi-bell p-overlay-badge" style="font-size: 1rem" /> -->
        <BellIcon class="size-6 text-gray-400" aria-hidden="true" />
        <div
          :class="notifications != 0 ? 'absolute inline-flex size-4 z-[1] bg-blue-500 rounded-full -mt-7 animate-[ping_3s_ease-in-out_infinite]' : 'hidden'">
        </div>
        <div :class="notifications != 0 ? 'absolute inline-flex size-4 bg-blue-500 rounded-full -mt-7' : 'hidden'">
          <span class="text-white text-xs z-[2] ml-[0.3rem]">
            {{ notifications.length }}
          </span>
        </div>
      </div>
    </PopoverButton>



    <Button v-if="type == 'buttonBadge'" v-tooltip.bottom="'Requerimientos Pendientes'" :label :severity :badge
      :badgeSeverity :outlined @click="toggle" :pt="{
      root: '!p-1.5',
    }">
      <template #icon>
        <i :class="icon"></i>
      </template>
    </Button>

    <OverlayPanel ref="op" class="h-96 space-y-2" :pt="{
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
      <div v-if="notifications.length > 0" class="h-80 px-2 overflow-y-auto bg-white ">
        <div v-for="item in notifications" :key="item.name" class="rounded-lg hover:bg-slate-100">
          <Link :href="route('requirements.index')">
          <div class="flex items-center justify-center space-x-6 rounded-lg border-b p-1">
            <i class="fa-solid fa-screwdriver-wrench text-2xl w-4" aria-hidden="true"
              :class="item.type == 'error' ? 'text-danger' : 'text-gray-600'">
            </i>
            <div class="col-span-2 mx-4 w-full">
              <a :href="item.href" class="font-semibold text-gray-900">
                <!-- Requerimientos Pendientes -->
                {{ item.title }}
              </a>
              <p class="text-gray-600 text-sm">{{ item.message }}</p>
              <span class="text-xs italic text-gray-500 flex">{{ item.ago }}</span>
            </div>
            <div
              class="col-span-1 flex size-8 cursor-pointer items-center justify-center rounded-2xl text-end hover:bg-gray-200">
              <!-- <i class="text-danger fa-solid fa-trash text-sm" aria-hidden="true" /> -->
              <Button v-tooltip.bottom="'Eliminar Notificación'" icon="pi pi-trash" severity="danger" text rounded
                @click="deleteNotification(item.id)" aria-label="Delete" />
            </div>
          </div>
          </Link>
        </div>
      </div>
      <div v-else class="flex h-[10rem] w-full flex-col items-center justify-center">
        <div class="flex size-20 items-center justify-center rounded-full bg-slate-100">
          <i class="fa-regular fa-bell-slash text-3xl text-blue-400"></i>
        </div>
        <p class="mt-4 text-lg font-semibold">No hay notificaciones para mostrar</p>
      </div>
      <a>
        <div v-if="notifications.length > 0"
          class="grid grid-cols-1 rounded-b-lg divide-x divide-gray-900/5 bg-primary">
          <div
            class="flex items-center justify-center gap-x-2.5 p-1 font-semibold cursor-pointer text-white hover:bg-blue-800">
            <component :is="CursorArrowRaysIcon" class="size-5 flex-none text-gray-400" aria-hidden="true" />
            Ver Todas
          </div>
        </div>
      </a>
    </OverlayPanel>


    <transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 translate-y-0"
      enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 translate-y-0">
      <PopoverPanel class="absolute lg:-right-28 z-10  flex -right-28 md:w-[30vw] max-w-max  px-4">
        <!--Encabezado de Notificaciones-->
        <section
          class="w-screen flex-auto overflow-hidden rounded-xl bg-white text-sm leading-6 shadow-lg ring-1 ring-gray-900/5">
          <div class="flex flex-nowrap place-content-between w-full align-middle p-2 bg-primary">
            <div>
              <h2 class="text-lg font-extrabold text-white">
                {{ props.title }}
              </h2>
            </div>
          </div>

          <!--Lista de Notificaciones-->
          <div class="h-96 space-y-2 overflow-y-auto border-b px-2">
            <Link :href="route('requirements.index')">
            <div v-if="notifications.length > 0" v-for="item in notifications" :key="item.name"
              class="rounded-lg hover:bg-slate-100">
              <div class="flex items-center justify-center space-x-6 rounded-lg border-b p-1">
                <i class="fa-regular fa-circle-xmark text-2xl w-4" aria-hidden="true"
                  :class="item.type == 'error' ? 'text-danger' : 'text-gray-600'">
                </i>
                <div class="col-span-2 mx-4 w-full">
                  <a :href="item.href" class="font-semibold text-gray-900">
                    {{ item.title }}
                  </a>
                  <p class="text-gray-600 text-sm">{{ item.message }}</p>
                  <span class="text-xs italic text-gray-500 flex">{{ item.ago }}</span>
                </div>
                <div
                  class="col-span-1 flex size-8 cursor-pointer items-center justify-center rounded-2xl text-end hover:bg-gray-100">
                  <!-- <i class="text-danger fa-solid fa-trash text-sm" aria-hidden="true" /> -->
                  <Button v-tooltip.bottom="'Eliminar Notificación'" icon="pi pi-trash" severity="danger" text rounded
                    @click="deleteNotification(item.id)" aria-label="Delete" />
                </div>
              </div>
            </div>
            <div v-else class="flex h-[10rem] w-full flex-col items-center justify-center">
              <div class="flex size-20 items-center justify-center rounded-full bg-slate-100">
                <i class="fa-regular fa-bell-slash text-3xl text-blue-400"></i>
              </div>
              <p class="mt-4 text-lg font-semibold">No hay notificaciones para mostrar</p>
            </div>
            </Link>
          </div>

          <!--Ver Todas las Notificaciones-->
          <a>
            <div v-if="notifications.length > 0" class="grid grid-cols-1 divide-x divide-gray-900/5 bg-primary">
              <div
                class="flex items-center justify-center gap-x-2.5 p-1 font-semibold cursor-pointer text-white hover:bg-blue-800">
                <component :is="CursorArrowRaysIcon" class="size-5 flex-none text-gray-400" aria-hidden="true" />
                Ver Todas
              </div>
            </div>
          </a>
        </section>
      </PopoverPanel>
    </transition>
  </Popover>
</template>
