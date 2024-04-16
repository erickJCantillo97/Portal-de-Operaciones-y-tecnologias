<script setup>
import { ref } from 'vue'
const props = defineProps({
    date: {
        type: Date,
        default: new Date()
    },
    letters: {
        type: Boolean,
        default: false
    }
})
const status = ref({
    loading : false,
    data:{
        programados : [],
        noProgramados : []}
    }
)

const getPersonalStatus = () => {
    status.value.loading = true
    axios.post(route('get.personal.status.programming'), { date: props.date }).then((res) => {
        status.value.data.programados = res.data.programados
        status.value.data.noProgramados = res.data.noProgramados
        status.value.loading = false
    })
}
getPersonalStatus()

const statusSelect = defineModel('statusSelect', {
    required: false,
})

</script>

<template>
    <div class="w-full max-w-[30vw] justify-center flex space-x-2 p-1 z-10 cursor-pointer" @click="statusSelect = status">
        <i v-if="status.loading" class="fa-solid fa-spinner animate-spin" />
        <p class="rounded w-full text-center px-2 text-white"
            :class="status.data.programados.length !== 0 ? 'bg-primary' : 'bg-success'" v-if="status.data.programados.length !== 0"
            v-tooltip="{ value: status.data.programados?.length > 0 ? `<div><p class='w-full text-center font-bold'>Programados</p>${status.data.programados.map((employee) => `<p class='w-44 text-sm truncate'>${employee.name}</p>`).join('')}</div>` : null, escape: false, pt: { text: 'text-center w-52' } }">
            <span v-if="letters">Programados:</span>
            <span>{{ status.data.programados.length }}
            </span>
        </p>
        <p class="rounded w-full text-center px-2 text-white bg-danger" v-if="status.data.noProgramados.length !== 0"
            v-tooltip="{ value: status.data.noProgramados?.length > 0 ? `<div><p class='w-full text-center font-bold'>No programados</p>${status.data.noProgramados.map((employee) => `<p class='w-44 text-sm truncate'>${employee.name}</p>`).join('')}</div>` : null, escape: false, pt: { text: 'text-center w-52' } }">
            <span v-if="letters">No Programados:</span>
            <span>{{ status.data.noProgramados.length }}
            </span>
        </p>
    </div>
</template>