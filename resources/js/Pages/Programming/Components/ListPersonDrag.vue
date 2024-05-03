<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import ProgressBar from 'primevue/progressbar';
import Loading from '@/Components/Loading.vue';
import Listbox from 'primevue/listbox';
import ToggleButton from 'primevue/togglebutton';


const loadingPerson = ref(true);
const personal = ref([])
const contratista = ref([])
const typePersonal = ref()
defineProps({
    arrayPersonFilter: Object
})

const getPersonalData = async () => {
    await axios.get(route('get.personal.user')).then((res) => {
        personal.value = Object.values(res.data.personal)
    })
}
const getContratisaData = async () => {
    contratista.value = [{ nombre: 'Contratista 1' }, { nombre: 'Contratista 2' }, { nombre: 'Contratista 3' }]
    // await axios.get(route('get.personal.user')).then((res) => {
    //     contratista.value = Object.values(res.data.personal)
    // })
}

onMounted(async () => {
    loadingPerson.value = true
    await getContratisaData()
    await getPersonalData()
    loadingPerson.value = false
})

const itemDrag = defineModel('itemDrag', {
    required: false,
})

const item = ref()

</script>
<template>
    <div class="sm:flex sm:flex-col h-36 w-full sm:w-52 sm:rounded-lg  divide-y  sm:h-full pt-1 gap-1">
        <ToggleButton v-model="typePersonal" onLabel="Ver Cotecmar" offLabel="Ver Contratista"
            :pt="{ root: 'h-8', box: '!border bg-primary-light border-primary' }" />
        <div v-if="loadingPerson" class="w-full h-full flex flex-col justify-center">
            <Loading class="mt-10 hidden sm:flex" message="Cargando personas" />
            <ProgressBar mode="indeterminate" class="flex sm:hidden" style="height: 4px" />
        </div>
        <div v-if="personal.length > 0" oncontextmenu="return false" :key="personal.length"
            class="overflow-x-visible flex sm:block overflow-y-hidden space-x-1 space-y-0 sm:space-y-1 sm:space-x-0 sm:w-full">
            <span v-if="arrayPersonFilter.loading" class="">
                <ProgressBar mode="indeterminate" style="height: 4px" />
            </span>
            <div class="flex justify-center">
            </div>
            <Listbox :options="typePersonal ? contratista : personal" class="max-h-full" v-model="item" filter
                :filterFields="['Nombres_Apellidos', 'Cargo', 'Oficina']" optionLabel="Nombres_Apellidos" :pt="{
            header: '!h-10',
            wrapper: 'h-[75vh]',
            item: '!p-0 !rounded-md hover:!bg-primary-light',
            filterInput: '!h-8',
            header: '!p-1'
        }">
                <template #option="slotProps">
                    <div v-ripple :key="slotProps.option.Nombres_Apellidos" :draggable="true"
                        @dragstart="itemDrag = slotProps.option; itemDrag.option = null;itemDrag.task = null"
                        v-tooltip.top="{ value: 'Arrastra hasta la tarea donde asignaras la persona', showDelay: 1000, hideDelay: 300, pt: { text: 'text-center' } }"
                        :class="[!(arrayPersonFilter.loading) ? (arrayPersonFilter.data?.programados.find(objeto => objeto.name === slotProps.option.Nombres_Apellidos) !== undefined ? 'bg-green-200' : (arrayPersonFilter.data?.noProgramados.find(objeto => objeto.name === slotProps.option.Nombres_Apellidos) !== undefined ? 'bg-red-200' : '')) : '']"
                        class="p-ripple min-w-[25vw] sm:min-w-32 z-10 rounded-xl border border-primary h-full sm:h-16 shadow-md cursor-pointer hover:bg-primary-light">
                        <div class="flex flex-col h-full justify-center gap-x-1 p-1">
                            <div class="flex flex-col justify-center">
                                <p class="text-sm font-semibold truncate text-gray-900">
                                    {{ slotProps.option?.Nombres_Apellidos ?? slotProps.option.nombre }}
                                </p>
                                <p class="text-xs truncate  text-gray-500">
                                    {{ slotProps.option.Cargo }}
                                </p>
                                <p class="text-xs truncate  text-gray-500">
                                    {{ slotProps.option.Oficina }}
                                </p>
                            </div>
                        </div>
                    </div>
                </template>
            </Listbox>
        </div>
    </div>
</template>