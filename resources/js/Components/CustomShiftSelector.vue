<script setup>
import Listbox from 'primevue/listbox';
import Empty from './Empty.vue';
import axios from 'axios';
import { ref } from 'vue';
import Loading from './Loading.vue';

defineProps({
    optionValue: {
        type: String,
        default: undefined
    }
})

const loading = ref(true)
const shifts = ref([])
axios.get(route('shift.index')).then((res) => {
    // console.log(res)
    shifts.value = res.data[0]
    loading.value = false
})

function formatdatetime24h(date) {
    return new Date(date).toLocaleString('es-CO',
        { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' })
}
const shift = defineModel('shift', {
    required: true
})
</script>
<template>
    <Loading v-if="loading" />
    <div v-else>
        <label class="text-sm mb-0.5 font-bold text-gray-900">Seleccione el Turno</label>
        <Listbox :options="shifts" filterPlaceholder="Escriba aqui para filtrar turno" :optionValue="optionValue"
            v-model="shift" :filterFields="['name', 'startShift', 'endShift', 'timeBreak', 'hours']" optionLabel="name"
            filter :pt="{
                list: '!h-40 !p-1 !space-y-1',
                item: '!h-10 !p-0 !rounded-md hover:!bg-primary-light',
                filterInput: '!h-8',
                header: '!p-1'
            }">
            <template #option="slotProps">
                <div class=" h-full grid grid-cols-4 border px-1 rounded-md ">
                    <span class="flex justify-between items-center overflow-hidden">
                        <p class="text-overflow h-full overflow-y-auto flex text-sm font-bold items-center">
                            {{ slotProps.option.name }}</p>
                        <i class="fa-regular fa-clock"></i>
                    </span>
                    <div class="text-xs items-center text-center col-span-3 grid grid-cols-4">
                        <span>
                            <p class="font-bold">Hora Inicio</p>
                            <p>{{ formatdatetime24h(slotProps.option.startShift) }}
                            </p>
                        </span>
                        <span>
                            <p class="font-bold">Hora Fin</p>
                            <p>{{ formatdatetime24h(slotProps.option.endShift) }}</p>
                        </span>
                        <span>
                            <p class="font-bold">Descanso</p>
                            <p>{{ slotProps.option.timeBreak }}h</p>
                        </span>
                        <span>
                            <p class="font-bold">H. Laborales</p>
                            <p> {{ parseFloat(slotProps.option.hours).toFixed(1) }}h</p>
                        </span>
                    </div>
                </div>
            </template>
            <template #empty>
                <Empty message="No hay turnos para mostrar" />
            </template>
            <template #emptyfilter>
                <Empty message="No se encuentran turnos"></Empty>
            </template>
        </Listbox>
    </div>
</template>