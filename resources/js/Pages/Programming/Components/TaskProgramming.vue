<script setup>
import { ref } from 'vue';
import ProgressBar from 'primevue/progressbar';
import Loading from '@/Components/Loading.vue';
const date = ref(new Date())
const tasks = ref({})

const props = defineProps({
    day: {
        type: Date,
        default: null
    },
    project: {
        type: Number
    }
})

function format24h(hora) {
    try {
        if (hora.length > 5) {
            return new Date(hora).toLocaleString('es-CO',
                { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' })
        } else {
            return new Date("1970-01-01T" + hora).toLocaleString('es-CO',
                { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' })
        }
    } catch (error) {
        console.log(error)
        return 'error'
    }

}

async function getTaskDay() {
    tasks.value = { loading: true, data: [] }
    await axios.post(route('actividadesDeultimonivelPorProyectos', props.project), { date: props.day }).then((res) => {
        tasks.value.data = res.data
        tasks.value.loading = false
    })
}
getTaskDay()
const option = ref()
const itemDrag = defineModel('itemDrag', {
    required: false,
})
defineEmits(['drop', 'togglePerson'])

</script>
<template>
    <div v-if="tasks.loading" class="flex col-span-5 justify-center h-full items-center">
        <Loading />
    </div>
    <div v-else v-for="task in tasks.data" @drop="$emit('drop', task, day, option); option = null" @dragover.prevent
        @dragenter.prevent class="sm:h-full w-full sm:w-1/3 sm:max-h-44 p-0.5 float-left"
        :key="task.name + date.toDateString()">
        <div class="flex border pb-1 rounded-md border-primary hover:bg-primary-light flex-col justify-between h-full">
            <div class="h-min w-full">
                <p v-tooltip="task.name"
                    class="border-b font-bold trun border-primary h-10 flex items-center justify-center text-xs px-0.5 w-full text-center">
                    {{ task.name }}
                </p>
            </div>
            <div class="h-full">
                <div :key="task.name + new Date().toISOString()" class="flex flex-col justify-between h-full py-1">
                    <p v-tooltip="task.task" class="text-xs px-1 text-center h-min w-full truncate">
                        {{ task.task }}
                    </p>
                    <div class="flex text-xs h-min justify-between px-1 items-center">
                        <div class="flex h-min cursor-default space-x-1  justify-center">
                            <p v-tooltip.left="'Fecha Fin'" class="text-xs text-center"
                                :class="new Date(task.endDate) < date ? 'bg-red-500 rounded-md px-1' : ''">
                                {{ task.endDate }}
                            </p>
                        </div>
                        <div
                            class="flex cursor-default bg-success-light px-1 space-x-1 justify-center items-center rounded-md">
                            <p v-tooltip.left="'Hora inicio'" class=" text-center">
                                {{ format24h(task.shift?.startShift ?? '') }}
                            </p>
                            <p v-tooltip.left="'Hora Fin'" class="text-center">
                                {{ format24h(task.shift?.endShift ?? '') }}
                            </p>
                        </div>
                    </div>
                    <div class="h-16 flex items-center w-full">
                        <div
                            class="px-2 flex h-full w-full justify-between sm:justify-center items-center overflow-hidden">
                            <div
                                class="overflow-x-hidden hover:overflow-x-auto gap-x-1 flex items-center h-full px-2 py-1 max-w-full flex-nowrap">
                                <i v-if="task.loading"
                                    class="fa-solid fa-circle-notch font-bold text-3xl animate-spin"></i>
                                <span v-if="task.employees?.length > 0" v-for="person in task.employees">
                                    <img v-tooltip.top="{ value: person.Nombres_Apellidos, pt: { text: 'text-center' } }"
                                        @click="$emit('togglePerson', $event, person, task, day)" :dragable="true"
                                        @dragstart="itemDrag = person; option = 'move'"
                                        :src="person.photo ?? '/images/person-default.png'"
                                        class="rounded-full min-h-10 h-10 hover:ring-1 hover:ring-primary w-10 min-w-10 object-cover ring-primary-light shadow-md" />
                                </span>
                                <div v-if="(task.employees?.length == 0) && !task.loading"
                                    class="flex items-center p-1">
                                    <p
                                        class="text-center text-danger rounded-md border-dashed bg-danger-light animate-pulse">
                                        Sin personal asignado
                                    </p>
                                </div>
                            </div>
                            <div class="min-w-9 sm:hidden h-full items-center flex justify-end">
                                <Button text rounded raised class="!w-8" severity="success"
                                    icon="fa-solid fa-plus"></Button>
                            </div>
                        </div>
                    </div>
                    <ProgressBar :value="parseFloat(task.percentDone)" class="h-3 mx-1" v-tooltip="'Avance'"
                        :pt="{ label: 'text-xs font-thin' }">
                    </ProgressBar>
                </div>
            </div>
        </div>
    </div>
</template>