<script setup>
import { ref, onMounted } from 'vue';
import '/resources/sass/dataTableCustomized.scss';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { FilterMatchMode, FilterOperator } from 'primevue/api';
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline';
import Button from '@/Components/Button.vue';
import { useSweetalert } from '@/composable/sweetAlert'
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { ClockIcon } from '@heroicons/vue/24/outline'
import Calendar from 'primevue/calendar';

const { confirmDelete } = useSweetalert();
const { toast } = useSweetalert();

const shift = ref({
    id: null,
    name: null,
    startShift: null,
    endShift: null,
    startBreak: null,
    endBreak: null,
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
    return new Date(hora).toLocaleString('es-CO',
        { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' })
}

const deleteShift = async (s) => {
    loading.value = true;
    await confirmDelete(s, 'horario', 'shift')
    getHours();
}

const editShift = (s) => {
    shift.value = {
        id: s.id,
        name: s.name,
        startShift: s.startShift,
        endShift: s.endShift,
        startBreak: s.startBreak,
        endBreak: s.endBreak
    };
    shiftDialog.value = true;
}
const createShift = () => {
    shift.value = {
        id: null,
        name: null,
        startShift: null,
        endShift: null,
        startBreak: null,
        endBreak: null
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
                startBreak: format24h(shift.startBreak),
                endBreak: format24h(shift.endBreak),
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
                startShift: format24h(shift.startShift),
                endShift: format24h(shift.endShift),
                startBreak: format24h(shift.startBreak),
                endBreak: format24h(shift.endBreak),
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
                            <Button @click="clearFilter()" type="button" severity="primary" class="hover:bg-primary ">
                                <i class="pi pi-filter-slash"></i>
                            </Button>
                        </div>

                        <div class="relative flex rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <MagnifyingGlassIcon class="w-5 h-4 text-gray-400" aria-hidden="true" />
                            </div>
                            <input type="search"
                                class="block w-10/12 py-4 pl-10 text-gray-900 border-0 rounded-md ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                v-model="filters.global.value" placeholder="Buscar..." />
                        </div>
                    </div>
                    <div>
                        <Button @click="createShift()" type="button" severity="success" class="hover:bg-primary ">
                            <i class="pi pi-plus"></i>
                        </Button>
                    </div>
                </div>
            </template>

            <!--COLUMNAS-->
            <Column field="name" header="Nombre" class="w-1/6"></Column>
            <Column header="Horario" class="">
                <template #body="slotProps">
                    {{ format24h(slotProps.data.startShift)+' a '+ format24h(slotProps.data.endShift) }}
                </template>
            </Column>

            <Column field="startBreak" header="Descanso" class="">
                <template #body="slotProps">
                    {{ slotProps.data.startBreak ? format24h(slotProps.data.startBreak)+' a '+ format24h(slotProps.data.endBreak) : 'No aplica' }}
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
    <TransitionRoot as="template" :show="shiftDialog">
        <Dialog as="div" class="relative z-10" @close="shiftDialog = false">
            <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100"
                leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" />
            </TransitionChild>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex items-end justify-center min-h-full p-4 text-center sm:items-center sm:p-0">
                    <TransitionChild as="template" enter="ease-out duration-300"
                        enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200"
                        leave-from="opacity-100 translate-y-0 sm:scale-100"
                        leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                        <DialogPanel
                            class="relative px-4 pt-5 pb-4 overflow-hidden text-left transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                            <div>
                                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-green-100 rounded-full">
                                    <ClockIcon class="w-6 h-6 text-green-600" aria-hidden="true" />
                                </div>
                                <div class="mt-3 text-center sm:mt-5">
                                    <DialogTitle as="h3" class="text-base font-semibold leading-6 text-gray-900">
                                        {{ shift.id ? 'Editar horario "' + shift.name + '"' : 'Nuevo horario' }}
                                    </DialogTitle>
                                    <div class="mt-2">
                                        <div class="grid grid-cols-2 gap-5">
                                            <div class="relative col-span-2">
                                                <label for="name"
                                                    class="absolute inline-block px-1 text-xs font-medium text-gray-900 bg-white -top-2 left-2">Nombre</label>
                                                <input type="text" name="name" id="name" v-model="shift.name" required
                                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                                    placeholder="Nombre para identificar el horario" />
                                            </div>
                                            <div class="relative">
                                                <label for="start"
                                                    class="absolute inline-block px-1 text-xs font-medium text-gray-900 bg-white -top-2 left-2">Horario</label>
                                                <div
                                                    class="grid w-full grid-cols-2 gap-3 p-4 text-gray-900 border-0 rounded-md shadow-sm ring-1 ring-inset ring-gray-300 ">
                                                    <div class="relative">
                                                        <label for="start"
                                                            class="absolute inline-block px-1 text-xs font-medium text-gray-900 bg-white -top-2 left-2">Inicio</label>
                                                        <Calendar name="start" id="start" timeOnly
                                                            v-model="shift.startShift"
                                                            :model-value="format24h(shift.startShift)"
                                                            class="block w-full text-gray-900 border-0 rounded-md shadow-sm alturah8 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                                    </div>
                                                    <div class="relative">
                                                        <label for="end"
                                                            class="absolute inline-block px-1 text-xs font-medium text-gray-900 bg-white -top-2 left-2">Fin</label>
                                                        <Calendar name="start" id="start" timeOnly v-model="shift.endShift"
                                                            :model-value="format24h(shift.endShift)"
                                                            class="block w-full text-gray-900 border-0 rounded-md shadow-sm alturah8 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="relative">
                                                <label for="start"
                                                    class="absolute inline-block px-1 text-xs font-medium text-gray-900 bg-white -top-2 left-2">Descanso</label>
                                                <div name="break"
                                                    class="grid w-full grid-cols-2 gap-3 p-4 text-gray-900 border-0 rounded-md shadow-sm ring-1 ring-inset ring-gray-300 ">

                                                    <div class="relative">
                                                        <label for="start"
                                                            class="absolute inline-block px-1 text-xs font-medium text-gray-900 bg-white -top-2 left-2">Inicio</label>
                                                        <Calendar name="start" id="start" timeOnly
                                                            v-model="shift.startBreak"
                                                            :model-value="format24h(shift.startBreak)"
                                                            class="block w-full text-gray-900 border-0 rounded-md shadow-sm alturah8 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                                    </div>
                                                    <div class="relative">
                                                        <label for="end"
                                                            class="absolute inline-block px-1 text-xs font-medium text-gray-900 bg-white -top-2 left-2">Fin</label>
                                                        <Calendar name="start" id="start" timeOnly v-model="shift.endBreak"
                                                            :model-value="format24h(shift.endBreak)"
                                                            class="block w-full text-gray-900 border-0 rounded-md shadow-sm alturah8 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="relative">
                                                <p>
                                                    Tiempo laborado
                                                </p>
                                                <p>{{ calcularDiferencia(shift.startShift, shift.endShift)-calcularDiferencia(shift.startBreak, shift.endBreak) }} Horas</p>
                                            </div>
                                            <div class="relative">
                                                <p>
                                                    Tiempo en descanso
                                                </p>
                                                <p>{{ calcularDiferencia(shift.startBreak, shift.endBreak) }} Horas</p>
                                            </div>
                                            <div class="relative text-warning col-span-2"
                                                v-if="calcularDiferencia(shift.startShift, shift.endShift)-calcularDiferencia(shift.startBreak, shift.endBreak) > 8.5">
                                                <p>
                                                    Tiempo adicional
                                                </p>
                                                <p>{{ calcularDiferencia(shift.startShift, shift.endShift)-calcularDiferencia(shift.startBreak, shift.endBreak)- 8.5 }} Horas
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                                <button type="button"
                                    class="inline-flex justify-center w-full px-3 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-md shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 sm:col-start-2"
                                    @click="shiftSave(true, shift)">Guardar</button>
                                <button type="button"
                                    class="inline-flex justify-center w-full px-3 py-2 mt-3 text-sm font-semibold text-gray-900 bg-white rounded-md shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:col-start-1 sm:mt-0"
                                    @click="shiftDialog = false" ref="cancelButtonRef">Cancelar</button>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>
