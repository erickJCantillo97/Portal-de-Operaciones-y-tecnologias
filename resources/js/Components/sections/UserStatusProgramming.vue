<script setup>
import ProgressBar from 'primevue/progressbar';
import ProgressSpinner from 'primevue/progressspinner';
import { ref } from 'vue'
const props = defineProps({
    date: {
        type: Date,
        default: new Date()
    },
    letters: {
        type: Boolean,
        default: false
    },
    loadingType: {
        type: String,
        default: 'bar'
    },
})
const status = ref({
    loading: false,
    data: {
        programados: [],
        noProgramados: []
    }
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
    <div class="h-8"
        :class="[(date.toDateString() == new Date().toDateString()) ? 'bg-secondary rounded-b-md' : '', (statusSelect == status) ? 'border-success border rounded-md bg-success-light' : '', letters == true ? 'bg-white' : '']">
        <div v-if="status.loading" class="flex justify-center items-center h-8 px-2">
            <ProgressBar mode="indeterminate" style="height: 4px; width: 80px;" v-if="loadingType == 'bar'" />
            <ProgressSpinner style="width: 25px; height: 25px" strokeWidth="8" fill="var(--surface-ground)"
                animationDuration=".5s" aria-label="Custom ProgressSpinner" v-else />

        </div>
        <div v-else class="grid grid-cols-2 sm:max-w-[30vw] justify-center gap-x-1 z-10 p-1 cursor-pointer" @click="!(statusSelect == status) ? statusSelect = status : (statusSelect = {
            loading: false,
            data: {
                programados: [],
                noProgramados: []
            }
        })">
            <p class="rounded w-full text-center px-2 text-white"
                :class="[status.data.noProgramados.length !== 0 ? 'bg-primary' : 'bg-success col-span-2']"
                v-if="status.data.programados.length !== 0"
                v-tooltip="{ value: status.data.programados?.length > 0 ? `<div><p class='w-full text-center font-bold'>Programados</p>${status.data.programados.map((employee) => `<p class='w-44 text-sm truncate'>${employee.name}</p>`).join('')}</div>` : null, escape: false, pt: { text: 'text-center w-52' } }">
                <span v-if="letters">Programados: </span>
                <span>{{ status.data.programados.length }}
                </span>
            </p>
            <p class="rounded w-full text-center px-2 text-white bg-danger"
                :class="status.data.programados.length == 0 ? 'col-span-2' : ''"
                v-if="status.data.noProgramados.length !== 0"
                v-tooltip="{ value: status.data.noProgramados?.length > 0 ? `<div><p class='w-full text-center font-bold'>No programados</p>${status.data.noProgramados.map((employee) => `<p class='w-44 text-sm truncate'>${employee.name}</p>`).join('')}</div>` : null, escape: false, pt: { text: 'text-center w-52' } }">
                <span v-if="letters">No Programados: </span>
                <span>{{ status.data.noProgramados.length }}
                </span>
            </p>
        </div>
    </div>
</template>