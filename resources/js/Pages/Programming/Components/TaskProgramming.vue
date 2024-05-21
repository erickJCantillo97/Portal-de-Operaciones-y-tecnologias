<script setup>
import { ref } from 'vue';
import ProgressBar from 'primevue/progressbar';
import Loading from '@/Components/Loading.vue';
import Empty from '@/Components/Empty.vue';
import IconField from 'primevue/iconfield';
import InputText from 'primevue/inputtext';
import InputIcon from 'primevue/inputicon';

const date = ref(new Date())
const tasks = ref({})

const props = defineProps({
    day: {
        type: Date,
        default: null
    },
    project: {
        type: Number,
        required: true
    },
    type: {
        type: String,
        default: 'day'
    },
    movil: {
        type: Boolean,
        default: false
    },
    dataRightClick: {
        type: Object,
        default: {}
    },
    optionsData: {
        type: Object
    },
    filterTaskMode: {
        type: String
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
    await axios.post(route('actividadesDeultimonivelPorProyectos', props.project), { date: props.day, mode: props.filterTaskMode }).then((res) => {
        tasks.value.data = res.data

    }).catch((error) => {
        console.log(error)
    })
    tasks.value.loading = false
}
getTaskDay()
const option = ref()
const itemDrag = defineModel('itemDrag', {
    required: false,
})
defineEmits(['drop', 'togglePerson', 'addPerson', 'menu'])

function getDaysLate(day) {
    return ((new Date() - new Date(day)) / 86400000).toFixed(0) + ' dias de retraso'
}

const input = ref('')

function search(data, filter) {

    return JSON.stringify(data).toLowerCase().includes(filter.toLowerCase())
}

</script>
<template>
    <div v-if="tasks.loading" class="flex justify-center h-full items-center">
        <Loading />
    </div>
    <div v-else-if="tasks.data.length > 0" class="w-full">
        <div class="px-1 py-0.5">
            <IconField iconPosition="left">
                <InputIcon class="pi pi-search"> </InputIcon>
                <InputText v-model="input" placeholder="Filtar" :class="[type == 'day' ? 'w-60' : 'w-full']" />
            </IconField>
        </div>
        <div v-for="task in tasks.data" class="w-full">
            <div v-if="search(task, input)" @contextmenu="$emit('menu', $event, task, day)"
                @drop="!movil ? $emit('drop', task, day) : null; option = null" @dragover.prevent @dragenter.prevents
                class=" sm:h-full w-full sm:max-h-44 p-0.5"
                :class="[type == 'day' ? 'sm:w-1/' + optionsData.colummnsProgramming?.data + ' float-left' : '']"
                :key="task.name + date.toDateString()">
                <div class="flex border pb-1 rounded-md border-primary hover:bg-primary-light flex-col justify-between h-full"
                    :class="[type === 'day' ? 'text-sm' : 'text-xs']">
                    <div class="h-min w-full">
                        <p v-tooltip="task.name"
                            class="border-b font-bold trun border-primary truncate px-0.5 w-full text-center">
                            {{ task.name }}
                        </p>
                    </div>
                    <div class="h-full">
                        <div :key="task.name" class="flex flex-col h-full py-1"
                            :class="task.task == 'ANRP' ? 'justify-center' : 'justify-between'">
                            <p v-if="task.task != 'ANRP'" v-tooltip="task.task"
                                class="px-1 text-center h-min w-full truncate">
                                {{ task.task }}
                            </p>
                            <div v-if="task.task != 'ANRP'" class="flex justify-between items-center px-1 w-full">
                                <div class="flex h-min cursor-default space-x-1 justify-center"
                                    v-if="optionsData.dateEndProgramming?.data">
                                    <p v-tooltip.left="'Fecha Fin'" class="text-center w-full"
                                        :class="new Date(task.endDate) < date ? 'bg-danger rounded-md px-1 text-white' : ''">
                                        {{ task.endDate }}
                                    </p>
                                </div>
                                <div v-if="optionsData.daysLateProgramming?.data">
                                    <p v-if="(new Date(task.endDate) < date) && type === 'day' && (optionsData.colummnsProgramming?.data < 4)"
                                        class="border truncate px-2 rounded-md bg-danger text-white">
                                        {{ getDaysLate(task.endDate) }}
                                    </p>
                                    <i v-else-if="(new Date(task.endDate) < date)" v-tooltip="getDaysLate(task.endDate)"
                                        class="fa-solid fa-circle-exclamation text-danger animate-pulse"></i>
                                </div>
                                <div v-if="optionsData.shiftProgramming?.data"
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
                                <div v-if="optionsData.showPersonProgramming?.data"
                                    class="px-2 flex h-full w-full justify-between sm:justify-center items-center overflow-hidden">
                                    <div
                                        class="overflow-x-hidden hover:overflow-x-auto gap-x-1 flex items-center h-full px-2 py-1 max-w-full flex-nowrap">
                                        <i v-if="task.loading > 0"
                                            class="fa-solid fa-circle-notch font-bold text-3xl animate-spin"></i>
                                        <span v-if="task.employees?.length > 0" v-for="person in task.employees">
                                            <img v-tooltip.top="{ value: person.Nombres_Apellidos, pt: { text: 'text-center' } }"
                                                @click="$emit('togglePerson', $event, person, task, day)"
                                                :dragable="true"
                                                @dragstart="itemDrag.person = person; itemDrag.task = task; itemDrag.option = 'move'; itemDrag.day = day"
                                                :src="person.photo ?? '/images/person-default.png'"
                                                class="rounded-full min-h-10 h-10 hover:ring-1 hover:ring-primary w-10 min-w-10 object-cover ring-primary-light shadow-md" />
                                        </span>
                                        <div v-if="(task.employees?.length == 0) && !task.loading"
                                            class="flex items-center px-6 text-danger w-full h-full border rounded-md font-bold border-dashed bg-danger-light animate-pulse">
                                            <p class="text-center ">
                                                Sin personal asignado
                                            </p>
                                        </div>
                                    </div>
                                    <div v-if="movil" class="min-w-9  h-full items-center flex justify-end">
                                        <Button text rounded raised class="!w-8" severity="success"
                                            icon="fa-solid fa-plus"
                                            @click="movil ? $emit('addPerson', $event, task, day) : null" />
                                    </div>
                                </div>
                            </div>
                            <span v-if="optionsData.progressProgramming?.data">
                                <ProgressBar v-if="task.task != 'ANRP'" :value="parseFloat(task.percentDone)"
                                    class="h-3 mx-1" v-tooltip="'Avance'" :pt="{ label: 'text-xs font-thin' }">
                                </ProgressBar>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div v-else class="flex flex-col h-full justify-center">
        <Empty message="Sin actividades" />
    </div>
</template>