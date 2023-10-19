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
import { CheckIcon } from '@heroicons/vue/24/outline'

const shift = ref()
const shiftDialog = ref(false)
const { confirmDelete } = useSweetalert();
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
    shift.value = { ...s };
    shiftDialog.value = true;
}

const changeStatus = (status, shift) => {
    loading.value = true;
    axios.put(route('shift.update', shift.id), {
        name: hour.name,
        startShift: hour.startShift,
        endShift: hour.endShift,
        startBreak: hour.startBreak,
        endBreak: hour.endBreak,
        status: status,
        description: hour.description
    }).then(
        (res) => {
            shifts.value = res.data[0]
            loading.value = false;
        }
    );
}

</script>

<template>
    <div class="px-auto w-full">
        <DataTable id="tabla" stripedRows class="p-datatable-sm" :value="shifts" v-model:filters="filters" dataKey="id"
            filterDisplay="menu" :loading="loading" :globalFilterFields="['name', 'descripcion']"
            currentPageReportTemplate=" {first} al {last} de {totalRecords}"
            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
            :paginator="true" :rows="5" :rowsPerPageOptions="[5, 10, 25, 50, 100]">

            <template #header>
                <div class="flex justify-between w-full h-8 mb-2">
                    <div class="flex space-x-4">
                        <div class="w-8">
                            <Button @click="clearFilter()" type="button" severity="primary" class="hover:bg-primary ">
                                <i class="pi pi-filter-slash" style="color: 'var(--primary-color)'"></i>
                            </Button>
                        </div>

                        <div class="relative flex rounded-md shadow-sm">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <MagnifyingGlassIcon class="h-4 w-5  text-gray-400" aria-hidden="true" />
                            </div>
                            <input type="search"
                                class="block w-10/12 rounded-md border-0 py-4 pl-10 text-gray-900 ring-1  ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                v-model="filters.global.value" placeholder="Buscar..." />
                        </div>
                    </div>
                </div>
            </template>

            <!--COLUMNAS-->
            <Column field="name" header="Nombre" class="w-1/4"></Column>
            <Column field="startShift" header="Inicio turno" class="">
                <template #body="slotProps">
                    {{ format24h(slotProps.data.startShift) }}
                </template>
            </Column>
            <Column field="endShift" header="Fin turno" class="">
                <template #body="slotProps">
                    {{ format24h(slotProps.data.endShift) }}
                </template>
            </Column>
            <Column field="startBreak" header="Inicio descanso" class="">
                <template #body="slotProps">
                    {{ slotProps.data.startBreak ? format24h(slotProps.data.startBreak) : 'No aplica' }}
                </template>
            </Column>
            <Column field="endBreak" header="Fin descanso" class="">
                <template #body="slotProps">
                    {{ slotProps.data.endBreak ? format24h(slotProps.data.endBreak) : 'No aplica' }}
                </template>
            </Column>
            <Column field="hours" header="Horas" class="">
                <template #body="slotProps">
                    {{ parseFloat(slotProps.data.hours).toFixed(2) }}
                </template>
            </Column>
            <Column field="status" header="Estado" class="w-1/12">
                <template #body="slotProps">
                    <Button @click="changeStatus(slotProps.data.status == false, slotProps.data)"
                        :severity="(slotProps.data.status == true) ? 'success' : 'danger'">
                        {{ (slotProps.data.status == true) ? 'Activo' : 'Inactivo' }}
                    </Button>
                </template>
            </Column>
            <Column header="Acciones" class="w-1/12">
                <template #body="slotProps">
                    <div class="flex space-x-1 justify-center">
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
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
            </TransitionChild>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <TransitionChild as="template" enter="ease-out duration-300"
                        enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200"
                        leave-from="opacity-100 translate-y-0 sm:scale-100"
                        leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                        <DialogPanel
                            class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                            <div>
                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-green-100">
                                    <CheckIcon class="h-6 w-6 text-green-600" aria-hidden="true" />
                                </div>
                                <div class="mt-3 text-center sm:mt-5">
                                    <DialogTitle as="h3" class="text-base font-semibold leading-6 text-gray-900">Payment
                                        successful</DialogTitle>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">Lorem ipsum, dolor sit amet consectetur adipisicing
                                            elit. Eius aliquam laudantium explicabo pariatur iste dolorem animi vitae error
                                            totam. At sapiente aliquam accusamus facere veritatis.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                                <button type="button"
                                    class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 sm:col-start-2"
                                    @click="shiftDialog = false">Deactivate</button>
                            <button type="button"
                                class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:col-start-1 sm:mt-0"
                                @click="open = false" ref="cancelButtonRef">Cancel</button>
                        </div>
                    </DialogPanel>
                </TransitionChild>
            </div>
        </div>
    </Dialog>
</TransitionRoot></template>
