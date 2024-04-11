<script setup>
import { ref, onMounted } from 'vue'
import { Link, router } from '@inertiajs/vue3'
const props = defineProps({
    date: {
        type: String,
        default: new Date().toISOString().split('T')[0]
    },
    letters: {
        type: Boolean,
        default: false
    }

})
const statusProgram = ref([])
const statusNoProgram = ref([])
const loading = ref(false)

const getPersonalStatus = () => {
    loading.value = true
    axios.post(route('get.personal.status.programming'), { date: props.date }).then((res) => {
        statusProgram.value = res.data.programados
        statusNoProgram.value = res.data.noProgramados
        loading.value = false
    })
}


getPersonalStatus()

</script>

<template>
    <Link :href="route('programming.create')">
    <div class="w-full justify-center flex space-x-2 p-1 z-10 cursor-pointer">

        <p class="rounded  px-2 text-white" :class="statusNoProgram.length !== 0 ? 'bg-primary' : 'bg-success'"
            v-if="statusProgram.length !== 0"
            v-tooltip="{ value: statusProgram?.length > 0 ? `<div><p class='w-full text-center font-bold'>Personal Programado</p>${statusProgram.map((employee) => `<p class='w-44 text-sm truncate'>${employee.name}</p>`).join('')}</div>` : null, escape: false, pt: { text: 'text-center w-52' } }">
            <span v-if="letters">Programados:</span>
            <i v-if="loading" class="fa-solid fa-spinner animate-spin" />
            <span v-else>{{ statusProgram.length }}
            </span>
        </p>
        <p class="rounded  px-2 text-white bg-danger" v-if="statusNoProgram.length !== 0"
            v-tooltip="{ value: statusNoProgram?.length > 0 ? `<div><p class='w-full text-center font-bold'>Personal NO Programados</p>${statusNoProgram.map((employee) => `<p class='w-44 text-sm truncate'>${employee.name}</p>`).join('')}</div>` : null, escape: false, pt: { text: 'text-center w-52' } }">
            <span v-if="letters">No Programados:</span>
            <i v-if="loading" class="fa-solid fa-spinner animate-spin" />
            <span v-else>{{ statusNoProgram.length }}
            </span>
        </p>
    </div>
    </Link>
</template>