<script setup>
import { ref, onMounted } from 'vue';
import '/resources/sass/dataTableCustomized.scss';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { FilterMatchMode, FilterOperator } from 'primevue/api';
import { useSweetalert } from '@/composable/sweetAlert'
import Calendar from 'primevue/calendar';
import CustomModal from '@/Components/CustomModal.vue';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import Button from 'primevue/button';

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
const shiftDialog = ref(false)

const loading = ref(false);
const shifts = ref([])
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
})

const getHours = () => {
    loading.value = true;
    axios.get(route('shift.index')).then(
        (res) => {
            shifts.value = res.data[0]
            loading.value = false;
        }
    );
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
    console.log(tiempo)
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
    <div class="w-full px-auto">
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
                    <div>
                        <Button @click="createShift()" title="Crear horario" outlined icon="pi pi-plus" type="button" severity="success" class="!h-8">

                        </Button>
                    </div>
                </div>
            </template>

            <!--COLUMNAS-->
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
                    <Button @click="shiftSave(slotProps.data.status == false, slotProps.data)"
                        :severity="(slotProps.data.status == true) ? 'success' : 'danger'">
                        {{ (slotProps.data.status == true) ? 'Activo' : 'Inactivo' }}
                    </Button>
                </template>
            </Column>
            <Column header="Acciones" class="w-1/12">
                <template #body="slotProps">
                    <div class="flex justify-center space-x-1">
                        <div title="Editar">
                            <Button severity="primary" @click="editShift(slotProps.data)" class="hover:bg-primary">
                                <i class="fa-solid fa-pen" />
                            </Button>
                        </div>
                        <div title="Eliminar">
                            <Button severity="danger" @click="deleteShift(slotProps.data.id)" class="hover:bg-danger">
                                <i class="fa-solid fa-trash" />
                            </Button>
                        </div>
                    </div>
                </template>
            </Column>
            <!-- <Column field="descripcion" header="Descripción"></Column> -->

        </DataTable>
    </div>

    <CustomModal :visible=shiftDialog width="30rem">
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
                        <span class="p-float-label">
                            <InputText class="alturah8" id="name" v-model="shift.name" />
                            <label for="name">Nombre</label>
                        </span>
                        <span class="p-float-label">
                            <Calendar name="start" id="start" timeOnly hourFormat="24" v-model="shift.startShift"
                                class="alturah8" :pt="{
                                    input: { class: 'rounded-md border-0 ring-1 ring-inset ring-gray-300 text-center' }
                                }" />
                            <label for="startShift" class="">Hora inicio</label>
                        </span>
                        <span class="p-float-label">
                            <Calendar name="endShift" id="start" timeOnly hourFormat="24" v-model="shift.endShift"
                                class="alturah8" :pt="{
                                    input: { class: 'rounded-md border-0 ring-1 ring-inset ring-gray-300 text-center' }
                                }" />
                            <label for="endShift" class="">Hora fin</label>
                        </span>
                        <span class="p-float-label">
                            <InputNumber class="alturah8" id="timeBreak" v-model="shift.timeBreak" :minFractionDigits="2" />
                            <label for="timeBreak" class="">Descanso en horas </label>
                        </span>
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
            <Button type="button" severity="primary" icon="fa-solid fa-floppy-disk" @click="shiftSave(true, shift)" outline label="Guardar"/>
            <Button type="button" severity="danger" icon="fa-solid fa-xmark" @click="shiftDialog = false" outline label="Cancelar"/>
        </template>
    </CustomModal>
</template>
