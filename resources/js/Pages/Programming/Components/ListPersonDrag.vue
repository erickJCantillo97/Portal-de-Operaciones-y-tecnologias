<script setup>
import { ref } from 'vue';
import axios from 'axios';
import ProgressBar from 'primevue/progressbar';
import Loading from '@/Components/Loading.vue';
import CustomInput from '@/Components/CustomInput.vue';


const loadingPerson = ref(true);
const personal = ref([])
const filter = ref('');

defineProps({
    arrayPersonFilter:Object
})

const getPersonalData = () => {
    loadingPerson.value = true
    axios.get(route('get.personal.user')).then((res) => {
        personal.value = Object.values(res.data.personal)
        loadingPerson.value = false
    })
}
getPersonalData()

const itemDrag = defineModel('itemDrag', {
    required: false
})

</script>
<template>
    <div
        class="sm:flex border sm:flex-col h-36 w-full sm:w-52 sm:rounded-lg  divide-y space-y-1 sm:h-full p-1 gap-1">
        <div class="h-8">
            <CustomInput v-model:input="filter" type="search" icon="fa-solid fa-magnifying-glass" />
        </div>
        <div v-if="loadingPerson" class="w-full h-full flex flex-col justify-center">
            <Loading class="mt-10 hidden sm:flex" message="Cargando personas" />
            <ProgressBar mode="indeterminate" class="flex sm:hidden" style="height: 4px" />
        </div>
        <div v-if="personal.length > 0" oncontextmenu="return false" onkeydown="return false" :key="personal.length"
            class="sm:overflow-y-auto overflow-x-visible flex sm:block overflow-y-hidden p-1 space-x-1 space-y-0 sm:space-y-1 sm:space-x-0 sm:w-full">
            <span v-if="arrayPersonFilter.loading" class="">
                <ProgressBar mode="indeterminate" style="height: 4px"/>
            </span>
            <div v-for="item in personal" :key="item.Nombres_Apellidos" :draggable="true" @dragstart="itemDrag = item"
                v-tooltip.top="{ value: 'Arrastra hasta la tarea donde asignaras la persona', showDelay: 1000, hideDelay: 300, pt: { text: 'text-center' } }"
                :class="[(item.Nombres_Apellidos.toUpperCase().includes(filter.toUpperCase()) || item.Cargo.toUpperCase().includes(filter.toUpperCase())) ? '' : '!hidden', !(arrayPersonFilter.loading) ? (arrayPersonFilter.data?.programados.find(objeto => objeto.name === item.Nombres_Apellidos) !== undefined ? 'bg-green-200' : (arrayPersonFilter.data?.noProgramados.find(objeto => objeto.name === item.Nombres_Apellidos) !== undefined ?'bg-red-200':'')) : '']"
                class="min-w-[25vw] sm:min-w-32 z-10 rounded-xl border border-primary h-full sm:h-16 shadow-md cursor-pointer hover:bg-primary-light hover:ring-1 hover:ring-primary">
                <div class="flex flex-col h-full justify-center gap-x-1 p-1">
                    <div class="flex flex-col justify-center">
                        <p class="text-sm font-semibold truncate text-gray-900">
                            {{ item.Nombres_Apellidos }}
                        </p>
                        <p class="text-xs truncate  text-gray-500">
                            {{ item.Cargo }}
                        </p>
                        <p class="text-xs truncate  text-gray-500">
                            {{ item.Oficina }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>