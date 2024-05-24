<script setup>
import '/resources/sass/dataTableCustomized.scss'
import { FilterMatchMode, FilterOperator } from 'primevue/api'
import { ref, onMounted } from 'vue'
import { useSweetalert } from '@/composable/sweetAlert'
import Button from 'primevue/button'
import CustomInput from '@/Components/CustomInput.vue'
import Calendar from 'primevue/calendar'
import Column from 'primevue/column'
import CustomModal from '@/Components/CustomModal.vue'
import DataTable from 'primevue/datatable'
import CustomDataTable from '@/Components/CustomDataTable.vue'
import InputNumber from 'primevue/inputnumber'
import InputText from 'primevue/inputtext'

const { confirmDelete } = useSweetalert();
const { toast } = useSweetalert();

const shift = ref({
    id: '',
    name: '',
    startShift: '',
    endShift: '',
    timeBreak: 0,
    endBreak: '',
})

const shiftDialog = ref(true)

const loading = ref(false);
const shifts = ref([])
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
})

//#region CustomDatatable
const columns = [
    { field: 'name', header: 'Nombre', filter: true },
    { field: 'Horario', header: 'Horario', filter: true },
    { field: 'timeBreak', header: 'Descanso', filter: true },
    { field: 'Horas', header: 'Horas', filter: true },
    { field: 'Adicionales', header: 'Adicionales', filter: true },
    { field: 'status', header: 'Estado', filter: true },
]

const buttons = [
    { event: 'edit', severity: 'warning', class: '', icon: 'fa-solid fa-pencil', text: true, outlined: false, rounded: false },
    { event: 'delete', severity: 'danger', icon: 'fa-regular fa-trash-can', class: '!h-8', text: true, outlined: false, rounded: false },
]
//#endregion

const getHours = async () => {
    loading.value = true;
    await axios.get(route('shift.index'))
        .then((res) => {
            shifts.value = res.data[0]
            loading.value = false;
        })
}

onMounted(() => {
    initFilters();
    getHours();
})

const initFilters = () => {
    filters.value = {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS },
        name: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
    }
};

const clearFilter = () => {
    initFilters();
};

function format24h(hora) {
    let tiempo
    if (hora.length > 5) {
        tiempo = new Date(hora).toLocaleString('es-CO',
            { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' })
    }
    else {
        tiempo = new Date("1970-01-01T" + hora).toLocaleString('es-CO',
            { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' })
    }
    // console.log(tiempo)
    return tiempo
}

const deleteShift = async (s) => {
    loading.value = true;
    await confirmDelete(s, 'horario', 'shift')
    getHours();
}

const editShift = async (s) => {
    shift.value = {
        id: s.id,
        name: s.name,
        startShift: format24h(String(s.startShift)),
        endShift: format24h(String(s.endShift)),
        timeBreak: s.timeBreak ? s.timeBreak : 0
    };
    console.table(shift.value)
    shiftDialog.value = true;
}
const createShift = () => {
    shift.value = {
        id: null,
        name: null,
        startShift: null,
        endShift: null,
        timeBreak: 0,
    };
    shiftDialog.value = true;
}

const shiftSave = (status, shift) => {
    if (shift.name == '') {
        toast('Se requiere un nombre', 'error');
    } else {
        loading.value = true;
        if (shift.id != null) {
            axios.put(route('shift.update', shift.id), {
                name: shift.name,
                startShift: format24h(shift.startShift),
                endShift: format24h(shift.endShift),
                timeBreak: shift.timeBreak,
                status: status
            }).then(
                (res) => {
                    shifts.value = res.data[0]
                    loading.value = false;
                    shiftDialog.value = false;
                    toast('¡Se ha actualizado el horario!', 'success');
                }
            )
        } else {
            axios.post(route('shift.store'), {
                name: shift.name,
                startShift: format24h(String(shift.startShift)),
                endShift: format24h(String(shift.endShift)),
                timeBreak: shift.timeBreak,
                status: status
            }).then((res) => {
                shifts.value = res.data[0]
                loading.value = false;
                shiftDialog.value = false;
                toast('¡Se ha guardado el nuevo horario!', 'success');
            }
            )
        }
    }
}

const calcularDiferencia = (start, end) => {
    if (!start || !end) {
        return 0
    }
    var horaInicio = format24h(start)
    var horaFinal = format24h(end)
    // Expresión regular para comprobar el formato de las horas
    var formatoHora = /^([01]?[0-9]|2[0-3]):[0-5][0-9]/;

    // Si alguna de las horas no tiene el formato correcto, salimos
    if (!(horaInicio.match(formatoHora) && horaFinal.match(formatoHora))) {
        return "Formato de hora incorrecto. Usa HH:mm.";
    }

    // Calculamos los minutos de cada hora
    var minutosInicio = horaInicio.split(':')
        .reduce((p, c) => parseInt(p) * 60 + parseInt(c));
    var minutosFinal = horaFinal.split(':')
        .reduce((p, c) => parseInt(p) * 60 + parseInt(c));

    // Calcular la diferencia en minutos
    let diferenciaMinutos = minutosFinal - minutosInicio;

    if (diferenciaMinutos < 0) {
        // Si la hora final es anterior a la inicial (pasa al siguiente día)
        diferenciaMinutos += 24 * 60; // Sumar un día completo en minutos
    }

    // Convertimos la diferencia a horas y minutos
    // var horas = Math.floor(diferenciaMinutos / 60);
    // var minutos = diferenciaMinutos % 60;
    // return (horas + ':' + (minutos < 10 ? '0' : '') + minutos);
    return diferenciaMinutos / 60
}
</script>
<template>
    <div class="size-full overflow-y-auto">
        <CustomDataTable :data="shifts" route-data="shift.index" :rows-default="100" :columnas="columns"
            :actions="buttons" @edit="editShift" @delete="deleteShift" :loading>
            <template #buttonHeader>
                <Button @click="createShift" severity="success" icon="fa-solid fa-plus" label="Agregar" outlined />
            </template>
        </CustomDataTable>
    </div>
    <!-- <div class="w-full px-auto">
        <DataTable id="tabla" stripedRows class="p-datatable-sm" :value="shifts" v-model:filters="filters" dataKey="id"
            filterDisplay="menu" :loading="loading" :globalFilterFields="['name', 'descripcion']"
            currentPageReportTemplate=" {first} al {last} de {totalRecords}"
            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
            :paginator="true" :rows="5" :rowsPerPageOptions="[5, 10, 25, 50, 100]">
            <template #header>
                <div class="flex justify-between w-full h-8 mb-2 align-middle">
                    <div class="flex space-x-4">
                        <div class="w-8">
                            <Button @click="clearFilter()" type="button" severity="primary" icon="pi pi-filter-slash"
                                outlined class="!h-8" />
                        </div>
                        <span class="p-input-icon-left">
                            <i class="pi pi-search" />
                            <InputText size="small" class="!h-8" type="search" title="Buscar horarios"
                                v-model="filters.global.value" placeholder="Buscar..." />
                        </span>
                    </div>
                    <Button @click="createShift()" title="Crear horario" label="Nuevo" icon="pi pi-plus"
                        severity="success" class="!h-8" />
                </div>
            </template>

            <Column field="name" header="Nombre" class="w-1/6"></Column>
            <Column header="Horario" class="">
                <template #body="slotProps">
                    {{ format24h(slotProps.data.startShift) + ' a ' + format24h(slotProps.data.endShift) }}
                </template>
            </Column>

            <Column field="timeBreak" header="Descanso" class="">
                <template #body="slotProps">
                    {{ slotProps.data.timeBreak ? slotProps.data.timeBreak + ' hora' : 'No aplica' }}
                </template>
            </Column>
            <Column header="Horas" class="">
                <template #body="slotProps">
                    {{ parseFloat(slotProps.data.hours).toFixed(2) }}
                </template>
            </Column>
            <Column header="Adicionales" class="">
                <template #body="slotProps">
                    {{ slotProps.data.hours > 8.5 ? parseFloat(slotProps.data.hours - 8.5).toFixed(2) : 0 }}
                </template>
            </Column>
            <Column field="status" header="Estado" class="w-1/12">
                <template #body="slotProps">
                    <Button @click="shiftSave(slotProps.data.status == false, slotProps.data)" class="!h-8"
                        :severity="(slotProps.data.status == true) ? 'success' : 'danger'"
                        :label="(slotProps.data.status == true) ? 'Activo' : 'Inactivo'" />

                </template>
            </Column>
            <Column header="Acciones" class="w-1/12">
                <template #body="slotProps">
                    <div class="flex justify-center space-x-1">
                        <div title="Editar">
                            <Button severity="primary" text icon="fa-solid fa-pen" @click="editShift(slotProps.data)"
                                class="!h-8" />
                        </div>
                        <div title="Eliminar">
                            <Button severity="danger" text icon="fa-solid fa-trash"
                                @click="deleteShift(slotProps.data.id)" class="!h-8" />
                        </div>
                    </div>
                </template>
            </Column>
        </DataTable>
    </div> -->

    <!--MODAL-->
    <CustomModal v-model:visible=shiftDialog width="30rem">
        <template #icon>
            <i class="text-white fa-regular fa-clock"></i>
        </template>
        <template #titulo>
            <p class="text-white">{{ shift.id ? 'Editar horario "' + shift.name + '"' : 'Nuevo horario' }}</p>
        </template>
        <template #body>
            <div class="mt-5">
                <div class="grid grid-cols-2 gap-5">
                    <div class="space-y-3">
                        <CustomInput label="Nombre" placeholder="Nombre del Turno" v-model:input="shift.name" />
                        <CustomInput label="Hora inicio" type="time" v-model:input="shift.startShift" :stepMinute="30"
                            id="start" placeholder="Hora de inicio" :required="true" />
                        <CustomInput label="Hora fin" type="time" id="end" v-model:input="shift.endShift"
                            :stepMinute="30" placeholder="Hora de fin" :required="true" />
                        <CustomInput label="Descanso" type="number" v-model:input="shift.timeBreak" suffix="Hora"
                            id="break" placeholder="Descanso en horas" :required="true" />
                    </div>

                    <div class="flex flex-col justify-center items-center w-full h-full space-y-2">
                        <div class="flex justify-between w-full">
                            <p>
                                Tiempo laborado
                            </p>
                            <p>{{ calcularDiferencia(String(shift.startShift),
                                String(shift.endShift)) - shift.timeBreak
                                }}
                                Horas</p>
                        </div>
                        <div class="flex justify-between w-full text-warning"
                            v-if="calcularDiferencia(String(shift.startShift), String(shift.endShift)) - shift.timeBreak > 8.5">
                            <p>
                                Tiempo adicional
                            </p>
                            <p>{{ calcularDiferencia(String(shift.startShift),
                                String(shift.endShift)) - shift.timeBreak - 8.5 }} Horas
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <template #footer>
            <Button type="button" severity="danger" class="!h-8" icon="fa-solid fa-circle-xmark"
                @click="shiftDialog = false" outline label="Cancelar" />
            <Button type="button" severity="success" class="!h-8" icon="fa-solid fa-floppy-disk"
                @click="shiftSave(true, shift)" outline label="Guardar" />
        </template>
    </CustomModal>
</template>
