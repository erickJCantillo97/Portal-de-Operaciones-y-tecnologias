<script setup>
import SplitButton from 'primevue/splitbutton';
import { ref } from 'vue';

const props = defineProps({
    gama: {
        type:String,
        default:'200'
    },
})

const colorRow = ref('bg-green-'+props.gama)

const colors = [
    { value: null, name: 'Sin fondo' },
    { value: 'bg-red-'+props.gama, name: 'Rojo' },
    { value: 'bg-green-'+props.gama, name: 'Verde' },
    { value: 'bg-yellow-'+props.gama, name: 'Amarillo' },
    { value: 'bg-blue-'+props.gama, name: 'Azul' },
    { value: 'bg-orange-'+props.gama, name: 'Naranja' },
    { value: 'bg-cyan-'+props.gama, name: 'Acua' },
    { value: 'bg-amber-'+props.gama, name: 'Ámbar' },
    { value: 'bg-lime-'+props.gama, name: 'Lima' },
    { value: 'bg-emerald-'+props.gama, name: 'Esmeralda' },
    { value: 'bg-teal-'+props.gama, name: 'Turquesa' },
    { value: 'bg-sky-'+props.gama, name: 'Cielo' },
    { value: 'bg-indigo-'+props.gama, name: 'Índigo' },
    { value: 'bg-violet-'+props.gama, name: 'Violeta' },
    { value: 'bg-purple-'+props.gama, name: 'Purpura' },
    { value: 'bg-fuchsia-'+props.gama, name: 'Fucsia' },
    { value: 'bg-pink-'+props.gama, name: 'Rosado' },
    { value: 'bg-rose-'+props.gama, name: 'Rosa' },
]

defineEmits(['changeColorRow'])

</script>
<template>
    <SplitButton raised v-tooltip.bottom="'Colorear'" :model="colors" text
        @click="$emit('changeColorRow', colorRow)" :pt="{
        menu: {
            root: 'flex',
            menu: 'grid grid-cols-6 items-center gap-1 flex-wrap space-y-0',
            menuitem: 'm-0 w-min'
        }
    }">
        <template #icon>
            <div :class="colorRow" class="w-8 h-6"></div>
        </template>
        <template #menubuttonicon>
            <i class="fa-solid fa-fill-drip"></i>
        </template>
        <template #item="{ item }">
            <div :class="item.value" v-tooltip="item.name" @click="colorRow=item.value;$emit('changeColorRow', colorRow)"
                class="w-8 h-6 border cursor-pointer hover:ring-1" />
        </template>
    </SplitButton>
</template>