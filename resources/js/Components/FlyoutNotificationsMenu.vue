<script setup>
import { Link } from '@inertiajs/vue3'
import { Popover, PopoverButton, PopoverPanel } from '@headlessui/vue'
import { BellIcon, XMarkIcon } from '@heroicons/vue/20/solid'
import { ChartPieIcon, CursorArrowRaysIcon } from '@heroicons/vue/24/outline'
import Badge from 'primevue/badge'

const notifications = [
  { name: 'Notificación 1', description: 'Lorem Ipsum', icon: ChartPieIcon },
  { name: 'Notificación 2', description: 'Lorem Ipsum', icon: ChartPieIcon },
  { name: 'Notificación 3', description: 'Lorem Ipsum', icon: ChartPieIcon },
  { name: 'Notificación 4', description: 'Lorem Ipsum', icon: ChartPieIcon },
  { name: 'Notificación 5', description: 'Lorem Ipsum', icon: ChartPieIcon },
  // { name: 'Notificación 6', description: 'Lorem Ipsum', href:'#' icon: ChartPieIcon },
]

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
          class="absolute inline-flex size-4 z-10 bg-blue-500 rounded-full -mt-7 animate-[ping_3s_ease-in-out_infinite]">
        </div>
        <div class="absolute inline-flex size-4 bg-blue-500 rounded-full -mt-7 ">
          <span class="text-white text-xs z-20 ml-[0.3rem]">5</span>
        </div>
      </div>
    </PopoverButton>

    <transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 translate-y-1"
      enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 translate-y-1">
      <PopoverPanel class="absolute left-1/2 z-10 mt-3 flex w-96 max-w-max -translate-x-1/2 px-4">
        <section
          class="w-screen max-w-md flex-auto overflow-hidden rounded-xl bg-white text-sm leading-6 shadow-lg ring-1 ring-gray-900/5">
          <div class="p-2">
            <!--Encabezado de Notificaciones-->
            <div class="flex flex-nowrap place-content-between w-full align-middle p-2">
              <div>
                <h2 class="text-lg font-extrabold">Notificaciones</h2>
              </div>
              <div class="bg-gray-100 rounded-sm pl-[0.45rem] pr-[0.45rem]">
                <span class="text-xs text-blue-500 font-extrabold">5 no leídos</span>
              </div>
            </div>
            <div class="divide-x divide-gray-200 border-b border-gray-200"></div>

            <!--Lista de Notificaciones-->
            <div v-for="item in notifications" :key="item.name"
              class="group relative flex gap-x-6 rounded-lg p-2 hover:bg-gray-50">
              <div
                class="mt-1 flex h-11 w-11 flex-none items-center justify-center rounded-lg bg-gray-50 group-hover:bg-white">
                <component :is="item.icon" class="size-6 text-gray-600 group-hover:text-indigo-600" aria-hidden="true" />
              </div>
              <div>
                <a :href="item.href" class="font-semibold text-gray-900">
                  {{ item.name }}
                  <span class="absolute inset-0" />
                </a>
                <p class="mt-1 text-gray-600">{{ item.description }}</p>
              </div>
              <div class="absolute size-6 end-0 mr-4 mt-2 cursor-pointer p-1">
                <XMarkIcon class="size-5 text-gray-400 hover:text-gray-500" aria-hidden="true" />
              </div>
            </div>
          </div>

          <!--Ver Todas las Notificaciones-->
          <Link :href="route('notifications.index')">
          <div class="grid grid-cols-1 divide-x divide-gray-900/5 bg-blue-900">
            <div v-for="item in callsToAction" :key="item.name"
              class="flex items-center justify-center gap-x-2.5 p-3 font-semibold cursor-pointer text-white hover:bg-blue-800">
              <component :is="item.icon" class="size-5 flex-none text-gray-400" aria-hidden="true" />
              {{ item.name }}
            </div>
          </div>
          </Link>
        </section>
      </PopoverPanel>
    </transition>
  </Popover>
</template>
