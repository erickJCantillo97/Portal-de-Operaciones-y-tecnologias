<script setup>
import { ref } from 'vue'
import { RadioGroup, RadioGroupDescription, RadioGroupLabel, RadioGroupOption } from '@headlessui/vue'
import { CheckCircleIcon } from '@heroicons/vue/20/solid'

const toolStatus = [
  {
    id: 1,
    title: 'Operativa',
    description:
      'La herramienta se encuentra en pleno funcionamiento y puede ser utilizada.',
    color: 'border-emerald-600 ring-2 ring-emerald-600',
    textColor: 'text-emerald-600'
  },
  {
    id: 2,
    title: 'Con Limitaciones',
    description:
      'La herramienta está operativa pero presenta ciertas limitaciones que afectan su rendimiento.',
    color: 'border-yellow-600 ring-2 ring-yellow-600',
    textColor: 'text-yellow-600'
  },
  {
    id: 3,
    title: 'Fuera de Servicio',
    description:
      'La herramienta no está en condiciones de realizar sus funciones debido a un problema identificado.',
    color: 'border-red-600 ring-2 ring-red-600',
    textColor: 'text-red-600'


  },
  {
    id: 4,
    title: 'De Baja',
    description: 'La herramienta ha sido retirada del servicio de manera permanente.',
    color: 'border-black ring-2 ring-black',
    textColor: 'text-black'
  },
]

const selectedToolStatus = defineModel()
</script>

<template>
  <RadioGroup v-model="selectedToolStatus">
    <div class="mt-2 grid grid-cols-8 gap-x-2">
      <RadioGroupOption class="col-span-2" as="template" v-for="item in toolStatus" :key="item.id" :value="item"
        v-slot="{ active, checked }">
        <div
          :class="[active ? item.color : 'border-gray-300', 'relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none']">
          <span class="flex flex-1">
            <span class="flex flex-col">
              <RadioGroupLabel as="span" class="block text-sm font-medium" :class="[item.textColor]">
                {{ item.title }}
              </RadioGroupLabel>
              <RadioGroupDescription as="span" class="mt-1 flex items-center text-sm text-gray-500">
                {{ item.description }}
              </RadioGroupDescription>
              <!-- <RadioGroupDescription as="span" class="mt-6 text-sm font-medium text-gray-900">
                {{ item.users }}
              </RadioGroupDescription> -->
            </span>
          </span>
          <CheckCircleIcon :class="[!checked ? 'invisible' : '', 'size-5 ', item.textColor]" aria-hidden="true" />
          <span
            :class="[active ? 'border' : 'border-2', checked ? item.color : 'border-transparent', 'pointer-events-none absolute -inset-px rounded-lg']"
            aria-hidden="true" />
        </div>
      </RadioGroupOption>
    </div>
  </RadioGroup>
</template>
