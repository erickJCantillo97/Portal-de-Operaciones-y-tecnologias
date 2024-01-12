<script setup>
import { Popover, PopoverButton, PopoverPanel } from '@headlessui/vue'
import { BellIcon } from '@heroicons/vue/20/solid'
import { ChartPieIcon, CursorArrowRaysIcon } from '@heroicons/vue/24/outline'

const solutions = [
  { name: 'Notificación 1', description: 'Lorem Ipsum', href: '#', icon: ChartPieIcon },
]
const callsToAction = [
  { name: 'Ver todas', href: '#', icon: CursorArrowRaysIcon },
  // { name: 'Contact sales', href: '#', icon: PhoneIcon },
]
</script>

<template>
  <Popover class="relative">
    <PopoverButton class="inline-flex items-center gap-x-1 text-sm font-semibold leading-6 text-gray-900">
      <button type="button" class="text-gray-400 hover:text-gray-500">
        <div class="relative size-6" title="Notificaciones">
          <BellIcon aria-hidden="true" />
          <span class="flex w-3 h-3">
            <span class="absolute inline-flex size-3 ml-3 bg-red-500 rounded-full -mt-7"></span>
          </span>
        </div>
      </button>
    </PopoverButton>

    <transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 translate-y-1"
      enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 translate-y-1">
      <PopoverPanel class="absolute left-1/2 z-10 mt-3 flex w-96 max-w-max -translate-x-1/2 px-4">
        <div
          class="w-screen max-w-md flex-auto overflow-hidden rounded-xl bg-white text-sm leading-6 shadow-lg ring-1 ring-gray-900/5">
          <div class="p-2">
            <div v-for="item in solutions" :key="item.name"
              class="group relative flex gap-x-6 rounded-lg p-4 hover:bg-gray-50">
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
            </div>
          </div>
          <div class="grid grid-cols-1 divide-x divide-gray-900/5 bg-blue-900">
            <a v-for="item in callsToAction" :key="item.name" :href="item.href"
              class="flex items-center justify-center gap-x-2.5 p-3 font-semibold text-white hover:bg-blue-800">
              <component :is="item.icon" class="size-5 flex-none text-gray-400" aria-hidden="true" />
              {{ item.name }}
            </a>
          </div>
        </div>
      </PopoverPanel>
    </transition>
  </Popover>
</template>
