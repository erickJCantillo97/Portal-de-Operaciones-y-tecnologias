<script setup>
import { useToast } from "primevue/usetoast";
import { useConfirm } from "primevue/useconfirm";
import CustomModal from '@/Components/CustomModal.vue';
import Knob from "primevue/knob";
import { ref } from "vue";
const toast = useToast();
const confirm = useConfirm();

const openConflict = defineModel('visible')
const conflicts = defineModel('conflicts')
const load=ref(false)
// const task = defineModel('task')

function format24h(hora) {
    if (hora.length > 5) {
        return new Date(hora).toLocaleString('es-CO',
            { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' })
    } else {
        return new Date("1970-01-01T" + hora).toLocaleString('es-CO',
            { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' })
    }
}

const formColision = ref({
    actionType: null, // accion a realizar: 1: Reemplazar , 2: Omitir
    idTask: null,// idTask de la Nueva tarea
    scheduleTime: null, // id del schedule time
    endSchedule: null, // se manda true si es la ultima colisión
    startShift: null, // hora de inicio de la nueva tarea
    endShift: null, // hora final de la nueva tarea
});

async function confirm1(event, scheduleTime, option, index) {
    formColision.value.idTask = conflicts.value.task.id
    formColision.value.scheduleTime = scheduleTime.idScheduleTime
    formColision.value.startShift = format24h(conflicts.value.task.shift.startShift)
    formColision.value.endShift = format24h(conflicts.value.task.shift.endShift)
    formColision.value.endSchedule = (conflicts.value.data[index].filter((st) => { return (st.status === 'omit' || st.status === 'remplace') }).length == (conflicts.value.data[index].length - 1))
    option == 'omit' ? (formColision.value.actionType = 1) : (formColision.value.actionType = 2)
    if (formColision.value.endSchedule) {
        confirm.require({
            target: event.currentTarget,
            message: '¿Está totalmente seguro? No se puede deshacer',
            icon: 'pi pi-exclamation-triangle text-danger',
            rejectClass: 'p-button-secondary p-button-outlined p-button-sm',
            acceptClass: 'p-button-sm p-button-success',
            rejectLabel: 'No',
            acceptLabel: 'Sí',
            accept: async () => {
                load.value=true
                let status = await resolveCollision(formColision.value)
                if (status) {
                    conflicts.value = []
                    openConflict.value = false
                    toast.add({ severity: 'success', group: "customToast", text: '¡Guardado!', life: 3000 });
                } else {
                    scheduleTime.status = undefined
                    toast.add({ severity: 'error', group: "customToast", text: 'Error no controlado', life: 3000 });
                }
                load.value=false
            }
        })
    } else {
        let status = await resolveCollision(formColision.value)
        if (status) {
            scheduleTime.status = option
            toast.add({ severity: 'info', group: "customToast", text: 'Omitida', life: 3000 });
        } else {
            scheduleTime.status = undefined
            toast.add({ severity: 'error', group: "customToast", text: 'Error no controlado', life: 3000 });
        }
    }
}


async function resolveCollision(form) {
    await axios.post(route('programming.collisionsPerDay', form)).then((r) => {
        return true
    }).catch((e) => {
        console.log(e)
        return false
    })
    return true
}

</script>
<template>
    <CustomModal :closable="false" icon="fa-solid fa-triangle-exclamation" :base-z-index="10" v-model:visible="openConflict"
        severity="danger" width="70vw" :footer="false"
        :titulo="'Existe sobreasignación al programarlo en la actividad: ' + conflicts.task?.name ?? ''">
        <template #body>
            <div class="py-2 flex flex-col gap-4 cursor-default">
                <div v-for="conflictForDay, index in conflicts.data"
                    class="border ring-success ring-1 rounded-md p-1 hover:shadow-lg hover:shadow-primary-light">
                    <span class="flex space-x-2 font-bold">
                        <span class="text-lg flex items-center gap-2 p-2">
                            <p>
                                Fecha:
                            </p>
                            <p>
                                {{ conflictForDay[0]?.fecha }}
                            </p>
                        </span>
                    </span>
                    <div class="flex flex-col gap-2">
                        <div v-for="conflict in conflictForDay" class="grid grid-cols-7 border rounded-md"
                            :class="conflict.status ? conflict.status == 'omit' ? '!bg-green-100' : '!bg-red-100' : undefined">
                            <div class="flex p-0.5 col-span-5">
                                <div class="flex justify-between items-center w-full">
                                    <span class="flex flex-col sm:flex-row items-center space-x-2 cursor-default">
                                        <p v-tooltip.top="'Nombre del proyecto'" class="font-bold">
                                            {{ conflict.NombreProyecto }}</p>
                                        <i class="fa-solid fa-arrow-right rotate-90 sm:rotate-0"></i>
                                        <p v-tooltip.top="'Nombre del proceso'">{{ conflict.nombrePadreTask }}</p>
                                        <i class="fa-solid fa-arrow-right rotate-90 sm:rotate-0"></i>
                                        <p v-tooltip.top="'Nombre de la actividad'" class="italic text-center sm:text-left font-bold">
                                            {{ conflict.nombreTask }}</p>
                                    </span>
                                    <div class="flex flex-col sm:flex-row items-center">
                                        <div class="px-4 flex w-min sm:flex-row flex-col sm:space-x-2">
                                            <p v-tooltip.top="{value:'Hora inicio programada',pt:{text:'text-center'}}" class="flex space-x-2 justify-between">
                                                <span class="font-bold">Inicio: </span>
                                                <span>
                                                    {{ conflict.horaInicio.slice(0,conflict.horaInicio.lastIndexOf(':')) }}
                                                </span>
                                            </p>
                                            <p v-tooltip.top="{value:'Hora fin programada',pt:{text:'text-center'}}"  class="flex space-x-2 justify-between">
                                                <span class="font-bold"> Fin: </span>
                                                <span>
                                                    {{ conflict.horaFin.slice(0, conflict.horaFin.lastIndexOf(':')) }}
                                                </span>
                                            </p>
                                        </div>
                                        <div class="sm:pr-10">
                                            <Knob v-tooltip.top="'Avance: ' + (conflict.taskDetails.percentDone) + '%'"
                                                :model-value="parseFloat(conflict.taskDetails.percentDone)" :size=50
                                                valueTemplate="{value}%" readonly />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="conflict.status == null" class="flex p-2 col-span-2 sm:flex-row flex-col justify-center items-center gap-2">
                                <Button class="!w-full" label="Reemplazar" severity="warning" :loading="load"
                                    v-tooltip.top="'Remplaza esta actividad por la nueva'"
                                    @click="confirm1($event, conflict, 'remplace', index)" />
                                <Button label="Omitir" class="!w-full" severity="success" :loading="load"
                                    v-tooltip.top="'Omite esta actividad y programa el restante del horario'"
                                    @click="confirm1($event, conflict, 'omit', index)" />
                            </div>
                            <div v-else class="flex p-2 justify-between items-center">
                                <p>{{ conflict.status == 'omit' ? 'Sin modificaciones' : 'Remplazada' }}</p>
                                <Button v-if="formColision.endSchedule" icon="fa-solid fa-rotate-left" text 
                                    severity="danger" v-tooltip="'Deshacer'" @click="conflict.status = null" rounded />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </CustomModal>
</template>