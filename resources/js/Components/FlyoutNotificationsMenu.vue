<script setup>
import { BellIcon, XMarkIcon } from '@heroicons/vue/20/solid'
import { ChartPieIcon, CursorArrowRaysIcon } from '@heroicons/vue/24/outline'
import { onMounted, ref } from 'vue'
import OverlayPanel from 'primevue/overlaypanel';
import { Popover, PopoverButton, PopoverPanel } from '@headlessui/vue';

const notifications = ref([])

onMounted(() => {
  axios.get(route('get.notifications')).then((res) => {
    notifications.value = res.data.notifications
  })
})

const callsToAction = [
  { name: 'Ver Todas', icon: CursorArrowRaysIcon },
  // { name: 'Contact sales', href: '#', icon: PhoneIcon },
]
</script>

<template>
  <Popover class="relative">
    <PopoverButton
      class="focus:outline-none inline-flex items-center gap-x-1 text-sm font-semibold leading-6 text-gray-900">
      <div class="relative mt-2 hover:bg-gray-100 p-1 rounded-full">
        <!-- <i p-badge="2" class="pi pi-bell p-overlay-badge" style="font-size: 1rem" /> -->
        <BellIcon class="size-6 text-gray-400" aria-hidden="true" />
        <div
          class="absolute inline-flex size-4 z-[1] bg-blue-500 rounded-full -mt-7 animate-[ping_3s_ease-in-out_infinite]">
        </div>
        <div class="absolute inline-flex size-4 bg-blue-500 rounded-full -mt-7 ">
          <span class="text-white text-xs z-[2] ml-[0.3rem]">{{ notifications.length }}</span>
        </div>
      </div>
    </PopoverButton>

    <transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 translate-y-0"
      enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 translate-y-0">
      <PopoverPanel class="absolute lg:-right-28 z-10  flex -right-28 md:w-[30vw] max-w-max  px-4">
        <section
          class="w-screen flex-auto overflow-hidden rounded-xl bg-white text-sm leading-6 shadow-lg ring-1 ring-gray-900/5">

          <!--Encabezado de Notificaciones-->
          <div class="flex flex-nowrap place-content-between w-full align-middle p-2 bg-primary">
            <div>
              <h2 class="text-lg font-extrabold text-white">Notificaciones</h2>
            </div>
          </div>
          <div class="px-2 space-y-2 border-b">
            <!--Lista de Notificaciones-->
            <div v-for="item in notifications" :key="item.name">
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
                  <i class="text-danger fa-solid fa-trash text-sm" aria-hidden="true" />
                </div>
              </div>
            </div>
          </div>
          <!--Ver Todas las Notificaciones-->
          <a>
            <div class="grid grid-cols-1 divide-x divide-gray-900/5 bg-primary">
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
