<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import '../../../sass/dataTableCustomized.scss';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { FilterMatchMode, FilterOperator } from 'primevue/api';
import { MagnifyingGlassIcon, TrashIcon, } from '@heroicons/vue/24/outline';
import { useSweetalert } from '@/composable/sweetAlert';
import Button from '../../Components/Button.vue';
import UserTable from '@/Components/UserTable.vue';
import Calendar from 'primevue/calendar';
import MultiSelect from 'primevue/multiselect';
import CustomModal from '@/Components/CustomModal.vue';
const { toast } = useSweetalert()

const props = defineProps({
    personal: Array,
    miPersonal: Array,
})

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
})

var form = useForm({
    users: [],
    fecha_devolucion: null
});

const submit = () => {
    router.post(route('personal.store'), form, {
        onSuccess: () => {
            modalVisible.value = false
            toast('personal añadido exitosamente', 'success')
        }
    });
}


const formatCurrency = (value) => {
    return parseFloat(value).toLocaleString('es-CO', {
        style: 'currency',
        currency: 'COP',
    });
};


const modalVisible = ref(false)
const showNew = () => {
    modalVisible.value = true
}

function formatDate(date) {
    // Extraer año, mes y día
    var anho = date.slice(0, 4);
    var mes = date.slice(4, 6);
    var dia = date.slice(6, 8);

    // Formato de salida: dd/mm/aaaa
    return dia === '00' ? 'Indefinido' : `${dia}/${mes}/${anho}`;
}
const quitar = (persona) => {
    form.users = form.users.filter(object => object.Num_SAP !== persona.Num_SAP);
}

</script>

<template>
    <AppLayout>
        <div class="w-full overflow-y-auto custom-scroll">
            <div class="flex items-center mx-2 mb-2">
                <div class="flex-auto">
                    <h1 class="text-xl font-semibold leading-6 capitalize text-primary">
                        Mi Personal
                    </h1>
                </div>

                <div title="Agregar Cliente" class="">
                    <Button severity="primary" type="button" @click="showNew()">
                        <i class="fa-solid fa-plus" />
                    </Button>
                </div>
            </div>
            <!--DATATABLE-->
            <DataTable id="tabla" stripedRows class="p-datatable-sm" :value="miPersonal" v-model:filters="filters"
                dataKey="id" filterDisplay="menu" :globalFilterFields="['Nombres_Apellidos', 'name', 'type', 'email']"
                currentPageReportTemplate=" {first} al {last} de {totalRecords}"
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                :paginator="true" :rows="10" :rowsPerPageOptions="[10, 25, 50, 100]">

                <template #header>
                    <div class="flex justify-between w-full h-8 mb-2">
                        <div class="flex space-x-4">
                            <div class="w-8" title="Filtrar Clientes">
                                <Button @click="clearFilter()" type="button" severity="primary" class="hover:bg-primary ">
                                    <i class="pi pi-filter-slash"></i>
                                </Button>
                            </div>

                            <div class="relative flex rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <MagnifyingGlassIcon class="w-5 h-4 text-gray-400" aria-hidden="true" />
                                </div>
                                <input type="search" title="Buscar Clientes"
                                    class="block w-10/12 py-4 pl-10 text-gray-900 border-0 rounded-md ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                    v-model="filters.global.value" placeholder="Buscar..." />
                            </div>
                        </div>
                    </div>
                </template>

                <!--COLUMNAS-->
                <Column field="Nombres_Apellidos" header="Nombre">
                    <template #body="slotProps">
                        <UserTable :user="slotProps.data" :photo="true"></UserTable>
                    </template>
                </Column>
                <Column field="Fecha_Final" header="Fin Contrato">
                    <template #body="slotProps">
                        {{ formatDate(slotProps.data.Fecha_Final) }}
                    </template>
                </Column>
                <Column field="Costo_Hora" header="Costo Hora">
                    <template #body="slotProps">
                        {{ formatCurrency(slotProps.data.Costo_Hora) }}
                    </template>
                </Column>
                <Column field="Costo_Mes" header="Costo Mes">
                    <template #body="slotProps">
                        {{ formatCurrency(slotProps.data.Costo_Mes) }}
                    </template>
                </Column>

                <!--ACCIONES-->
                <Column header="Acciones" class="space-x-3">
                    <template #body="slotProps">
                        <!--BOTÓN UNIDADES-->
                        <div
                            class="flex pl-4 pr-3 space-x-2 text-sm font-medium text-gray-900 whitespace-normal sm:pl-6 lg:pl-8 ">
                            <!--BOTÓN ELIMINAR-->
                            <div title="Eliminar de mi Personal" v-if="slotProps.data.canDelete">
                                <Button severity="danger" @click="confirmDelete(slotProps.data.id, 'Cliente?', 'customers')"
                                    class="hover:bg-danger">
                                    <TrashIcon class="w-4 h-4 " aria-hidden="true" />
                                </Button>
                            </div>
                        </div>
                    </template>
                </Column>
            </DataTable>
        </div>

        <CustomModal :visible="modalVisible">
            <template #icon>
                <i class="text-white fa-solid fa-ship"></i>
            </template>
            <template #titulo>
                <span class="text-xl font-bold text-white white-space-nowrap">Añadir Personal</span>
            </template>
            <template #body>
                <div class="flex py-6 space-x-4">
                    <div class="w-1/2 space-y-8">
                        <div class="p-fluid border-0 p-2 ">
                            <label for="">Seleccionar Personal</label>
                            <MultiSelect v-model="form.users"
                                :filterFields="['Nombres_Apellidos', 'Cargo', 'Identificacion', 'Oficina']" :filter="true"
                                :options="props.personal" optionLabel="Nombres_Apellidos" placeholder="Seleccionar Personas"
                                display="chip" class="multiselect-custom w-full rounded-md md:w-14rem" :pt="{
                                    root: {
                                        class: 'h-10 !ring-gray-300 !ring-inset ring-1 !border-0 !shadow-sm '
                                    },
                                    input: {
                                        class: '!text-sm pt-3 pl-2'
                                    },
                                    item: {
                                        class: '!text-sm'
                                    }
                                }">
                                <template #option="slotProps">
                                    <UserTable :user="slotProps.option"></UserTable>

                                </template>
                            </MultiSelect>
                        </div>
                        <div class="p-fluid border-0 ">
                            <label for="">Fecha de devolución</label>
                            <Calendar inputId="icon" v-model="form.fecha_devolucion" :min-date="new Date()"
                                dateFormat="dd/mm/yy" :showIcon="true" />
                        </div>
                    </div>
                    <div class="w-1/2 h-72 custom-scroll overflow-y-auto shadow-lg rounded-lg p-2">
                        <h3 class="text-center font-bold text-lg">Personal Seleccionado</h3>
                        <div class="block space-y-4">
                            <div v-for="persona of form.users" class="flex justify-between">
                                <UserTable :user="persona" :photo="true">
                                </UserTable>
                                <div>
                                    <Button severity="danger" @click="quitar(persona)" class="hover:bg-danger">
                                        <i class="fa-regular fa-circle-xmark"></i>
                                    </Button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </template>
            <template #footer>
                <Button severity="success" @click="submit()">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <p>Guardar</p>
                </Button>
                <Button severity="danger" @click="modalVisible = false" class="hover:bg-danger">
                    <i class="fa-regular fa-circle-xmark"></i>
                    <p>Cancelar</p>
                </Button>
            </template>
        </CustomModal>
    </AppLayout>
</template>