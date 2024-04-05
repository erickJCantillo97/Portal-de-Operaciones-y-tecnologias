<script setup>
import { useToast } from "primevue/usetoast";
import { useConfirm } from "primevue/useconfirm";
import CustomModal from '@/Components/CustomModal.vue';
import Breadcrumb from "primevue/breadcrumb";
import Knob from "primevue/knob";
import { ref } from "vue";
const toast = useToast();
const confirm = useConfirm();

const openConflict = defineModel('visible')
const conflicts = defineModel('conflicts')
const task = defineModel('task')

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

async function confirm1(event, scheduleTime, option, data) {
    formColision.value.idTask = task.value.id
    formColision.value.startShift = format24h(task.value.shift.startShift)
    formColision.value.endShift = format24h(task.value.shift.endShift)
    if (option == 'omit') {
        scheduleTime.status = option
        formColision.value.actionType = 1
        formColision.value.endSchedule = data.find((a) => { a.status == null }) == undefined
        scheduleTime.status = undefined
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
                    scheduleTime.status = option
                    let status = resolveCollision(formColision.value)
                    if (!status) {
                        scheduleTime.status = undefined
                        toast.add({ severity: 'error', group: "customToast", text: 'Error no controlado', life: 3000 });
                    } else {
                        conflicts.value.splice(conflicts.value.indexOf(data), 1);
                        openConflict.value=conflicts.value.length>0
                        toast.add({ severity: 'info', group: "customToast", text: 'Omitida', life: 3000 });
                    }
                }, reject: () => {
                    scheduleTime.status = undefined
                    formColision.value.endSchedule = false
                }
            })
        } else {
            let status = resolveCollision(formColision.value)
            if (!status) {
                scheduleTime.status = undefined
                toast.add({ severity: 'error', group: "customToast", text: 'Error no controlado', life: 3000 });
            } else {
                toast.add({ severity: 'info', group: "customToast", text: 'Omitida', life: 3000 });
            }
        }
    } else if (option == 'remplace') {
        scheduleTime.status = option
        formColision.value.actionType = 2
        formColision.value.endSchedule = data.find((a) => { a.status == null }) == undefined
        scheduleTime.status = undefined
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
                    scheduleTime.status = option
                    let status = resolveCollision(formColision.value)
                    if (!status) {
                        scheduleTime.status = undefined
                        toast.add({ severity: 'error', group: "customToast", text: 'Error no controlado', life: 3000 });
                    } else {
                        conflicts.value.splice(conflicts.value.indexOf(data), 1);
                        openConflict.value=conflicts.value.length>0
                        toast.add({ severity: 'info', group: "customToast", text: 'Omitida', life: 3000 });
                    }
                }, reject: () => {
                    scheduleTime.status = undefined
                    formColision.value.endSchedule = false
                }
            })
        } else {
            let status = resolveCollision(formColision.value)
            if (!status) {
                scheduleTime.status = undefined
                toast.add({ severity: 'error', group: "customToast", text: 'Error no controlado', life: 3000 });
            } else {
                toast.add({ severity: 'info', group: "customToast", text: 'Omitida', life: 3000 });
            }
        }
    } else {
        confirm.require({
            target: event.currentTarget,
            message: '¿Está totalmente seguro? No se puede deshacer',
            icon: 'pi pi-exclamation-triangle text-danger',
            rejectClass: 'p-button-secondary p-button-outlined p-button-sm',
            acceptClass: 'p-button-sm p-button-success',
            rejectLabel: 'No',
            acceptLabel: 'Sí',
            accept: async () => {
                if (option == 'omitAll') {
                    let error
                    data.forEach((scheduleD) => {
                        const endIndex = scheduleD.findLastIndex((element) => element.status == null)
                        scheduleD.forEach((scheduleT, index) => {
                            if (scheduleT.status == null) {
                                formColision.value.endSchedule = endIndex == index ? true : false
                                formColision.value.actionType = 2
                                formColision.value.scheduleTime = scheduleT.idScheduleTime
                                let status = resolveCollision(formColision.value)
                                if (!status) {
                                    error = scheduleT
                                    toast.add({ severity: 'error', group: "error", text: 'Hubo un error inesperado', life: 3000 });
                                } else {
                                    scheduleT.status = 'remplace'
                                }
                            }
                        })
                    })
                    if (error) {
                        toast.add({ severity: 'warn', group: "customToast", text: 'Se remplazaron todas con errores', life: 3000 });
                    } else {
                        conflicts.value=[]
                        openConflict.value=conflicts.value.length>0
                        toast.add({ severity: 'success', group: "customToast", text: 'Se remplazaron todas', life: 3000 });
                    }
                } else if (option == 'remplaceAll') {
                    let error
                    data.forEach((scheduleD) => {
                        const endIndex = scheduleD.findLastIndex((element) => element.status == null)
                        scheduleD.forEach((scheduleT, index) => {
                            if (scheduleT.status == null) {
                                formColision.value.endSchedule = endIndex == index ? true : false
                                formColision.value.actionType = 1
                                formColision.value.scheduleTime = scheduleT.idScheduleTime
                                let status = resolveCollision(formColision.value)
                                if (!status) {
                                    error = scheduleT
                                    toast.add({ severity: 'error', group: "error", text: 'Hubo un error inesperado', life: 3000 });
                                } else {
                                    scheduleT.status = 'remplace'
                                }
                            }
                        })
                    })
                    if (error) {
                        toast.add({ severity: 'warn', group: "customToast", text: 'Se remplazaron todas con errores', life: 3000 });
                    } else {
                        conflicts.value=[]
                        openConflict.value=conflicts.value.length>0
                        toast.add({ severity: 'success', group: "customToast", text: 'Se remplazaron todas', life: 3000 });
                    }
                } else if (option == 'omitAllDay') {
                    let error
                    const endIndex = data.findLastIndex((element) => element.status == null)
                    await data.forEach((scheduleT, index) => {
                        if (scheduleT.status == null) {
                            formColision.value.endSchedule = endIndex == index ? true : false
                            formColision.value.actionType = 2
                            formColision.value.scheduleTime = scheduleT.idScheduleTime
                            let status = resolveCollision(formColision.value)
                            if (!status) {
                                error = scheduleT
                                toast.add({ severity: 'error', group: "error", text: 'Hubo un error inesperado', life: 3000 });
                            } else {
                                scheduleT.status = 'omit'
                            }
                        }
                    })
                    if (error) {
                        toast.add({ severity: 'warn', group: "customToast", text: 'Se omitieron todas las del dia con errores', life: 3000 });
                    } else {
                        conflicts.value.splice(conflicts.value.indexOf(data), 1);
                        openConflict.value=conflicts.value.length>0
                        toast.add({ severity: 'success', group: "customToast", text: 'Se omitieron todas las del dia', life: 3000 });
                    }
                } else if (option == 'remplaceAllDay') {
                    let error
                    const endIndex = data.findLastIndex((element) => element.status == null)
                    await data.forEach((scheduleT, index) => {
                        if (scheduleT.status == null) {
                            formColision.value.endSchedule = endIndex == index ? true : false
                            formColision.value.actionType = 1
                            formColision.value.scheduleTime = scheduleT.idScheduleTime
                            let status = resolveCollision(formColision.value)
                            if (!status) {
                                error = scheduleT
                                toast.add({ severity: 'error', group: "error", text: 'Hubo un error inesperado', life: 3000 });
                            } else {
                                scheduleT.status = 'remplace'
                            }
                        }
                    })
                    if (error) {
                        toast.add({ severity: 'warn', group: "customToast", text: 'Se remplazaron todas las del dia con errores', life: 3000 });
                    } else {
                        conflicts.value.splice(conflicts.value.indexOf(data), 1);
                        openConflict.value=conflicts.value.length>0
                        toast.add({ severity: 'success', group: "customToast", text: 'Se remplazaron todas las del dia', life: 3000 });
                    }
                }
            },
            reject: () => {

                // toast.add({ severity: 'error', group: "customToast", text: 'You have rejected', life: 3000 });
            }
        });
    }
};

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
        <CustomModal icon="fa-solid fa-triangle-exclamation" :base-z-index="10" v-model:visible="openConflict"
            severity="danger" :closable="false" :close-on-escape="false" width="90vw"
            :titulo="'Existen colisiones al programar tarea: ' + task.name">
            <template #body>
                <div class="py-2 flex flex-col gap-4">
                    <div v-for="conflictForDay in conflicts"
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
                            <span v-if="conflictForDay.length > 1"
                                class="flex p-2 justify-end items-center gap-2  w-[400px]">
                                <Button class="!w-full" label="Reemplazar todo" severity="contrast"
                                    @click="confirm1($event, null, 'remplaceAllDay', conflictForDay)" />
                                <Button class="!w-full" label="Omitir todo" severity="success"
                                    @click="confirm1($event, null, 'omitAllDay', conflictForDay)" />
                            </span>
                        </span>
                        <div class="flex flex-col gap-2">
                            <div v-for="conflict in conflictForDay" class="grid grid-cols-5 border rounded-md"
                                :class="conflict.status ? conflict.status == 'omit' ? '!bg-green-100' : '!bg-red-100' : ''">
                                <div class="flex  p-0.5 col-span-4">
                                    <div class="flex justify-between items-center w-full">
                                        <Breadcrumb :pt="{ root: '!bg-transparent' }"
                                            :model="[{ label: conflict.NombreProyecto, tooltip: 'Nombre del proyecto', class: 'font-bold' }, { label: conflict.nombrePadreTask, tooltip: 'Nombre del proceso' }, { label: conflict.nombreTask, tooltip: 'Nombre de la actividad', class: 'font-bold italic' }]">
                                            <template #item="item">
                                                <p v-tooltip.bottom="{ value: item.item.tooltip, pt: { text: 'text-center' } }"
                                                    :class="item.item.class" class="cursor-default truncate w-full">
                                                    {{ item.label }}
                                                </p>
                                            </template>
                                        </Breadcrumb>
                                        <div class="flex items-center">
                                            <div class="px-4 flex w-min space-x-2">
                                                <p class="flex space-x-2">
                                                    <span class="font-bold">Inicio: </span>
                                                    <span>
                                                        {{
        conflict.horaInicio.slice(0,
            conflict.horaInicio.lastIndexOf(':'))
    }}
                                                    </span>
                                                </p>
                                                <p class="flex space-x-2">
                                                    <span class="font-bold"> Fin: </span>
                                                    <span>
                                                        {{ conflict.horaFin.slice(0, conflict.horaFin.lastIndexOf(':'))
                                                        }}
                                                    </span>
                                                </p>
                                            </div>
                                            <div class="pr-10">
                                                <Knob
                                                    v-tooltip.top="'Avance: ' + (conflict.taskDetails.percentDone) + '%'"
                                                    :model-value="parseFloat(conflict.taskDetails.percentDone)" :size=50
                                                    valueTemplate="{value}%" readonly />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="conflict.status == null" class="flex p-2 justify-center items-center gap-2">
                                    <Button class="!w-full" label="Reemplazar" severity="warning"
                                        v-tooltip.top="'Remplaza esta actividad por la nueva'"
                                        @click="confirm1($event, conflict, 'remplace', conflictForDay)" />
                                    <Button label="Omitir" class="!w-full" severity="success"
                                        v-tooltip.top="'Omite esta actividad y programa el restante del horario'"
                                        @click="confirm1($event, conflict, 'omit', conflictForDay)" />
                                </div>
                                <div v-else class="flex p-2 justify-between items-center">
                                    <p>{{ conflict.status == 'omit' ? 'Sin modificaciones' : 'Remplazada' }}</p>
                                    <Button icon="fa-solid fa-rotate-left" text severity="danger" v-tooltip="'Deshacer'"
                                        @click="conflict.status = null" rounded />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            <template #footer>
                <Button label="Reemplazar todas las coliciones" severity="danger"
                    @click="confirm1($event, null, 'remplaceAll', conflicts)" />
                <Button label="Omitir todas las coliciones" severity="success"
                    @click="confirm1($event, null, 'omitAll',conflicts)" />
            </template>
        </CustomModal>
</template>