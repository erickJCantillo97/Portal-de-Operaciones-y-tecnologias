<script setup>
import { ref, onMounted } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import '/resources/sass/dataTableCustomized.scss';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { FilterMatchMode, FilterOperator } from 'primevue/api';
import Button from '@/Components/Button.vue';
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';
import { MagnifyingGlassIcon, PencilIcon, TrashIcon, PlusIcon } from '@heroicons/vue/24/outline';
import { useSweetalert } from '@/composable/sweetAlert'
import TextInput from '@/Components/TextInput.vue';
const { confirmDelete, toast } = useSweetalert();
const open = ref(false)
const loading = ref(false);
const groups = ref([])
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
})

const formData = useForm({});


onMounted(() => {
    initFilters();
    loading.value = true;
    axios.get(route('subsystem.index')).then(
        (res) => {
            groups.value = res.data[0]
            loading.value = false;
        }
    );
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

const editItem = (subsystem) => {
    formData.id = subsystem.id;
    formData.name = subsystem.name;
    formData.code = subsystem.code;
    open.value = true;
};

const submit = () => {
    loading.value = true;
    if (formData.id == 0) {
        router.post(route('subsystem.store'), formData, {
            preserveScroll: true,
            onSuccess: (res) => {
                open.value = false;
                toast('Sub-Sistema creado exitosamente', 'success');
            },
            onError: (errors) => {
                toast('¡Ups! Ha surgido un error', 'error');
            },
            onFinish: visit => {
                loading.value = false;
            }
        })
        return 'creado';
    }
    router.put(route('subsystem.update', formData.id), formData, {
        preserveScroll: true,
        onSuccess: (res) => {
            open.value = false;
            toast('Sub-Sistema actualizado exitosamente!', 'success');
        },
        onError: (errors) => {
            toast('¡Ups! Ha surgido un error', 'error');
        },
        onFinish: visit => {
            loading.value = false;
        }
    })
}
</script>

<template>
    <div class="px-auto  w-full">
        <DataTable id="tabla" stripedRows class="p-datatable-sm" :value="groups" scroll-height="300px"
            v-model:filters="filters" dataKey="id" filterDisplay="menu" :loading="loading"
            :globalFilterFields="['name', 'descripcion']" currentPageReportTemplate=" {first} al {last} de {totalRecords}"
            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
            :paginator="true" :rows="10" :rowsPerPageOptions="[10, 25, 50, 100]">

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
            <Column field="code" header="Codigo"></Column>
            <Column field="name" header="Nombre"></Column>
            <Column field="system.name" header="Sistema"></Column>
            <Column header="Acciones" class="space-x-3">
                <template #body="slotProps">
                    <!--BOTÓN EDITAR-->
                    <div title="Editar Sistema"
                        class="flex pl-4 pr-3 space-x-2 text-sm font-medium text-gray-900 whitespace-normal sm:pl-6 lg:pl-8 ">
                        <div>
                            <Button severity="primary" @click="editItem(slotProps.data)" class="hover:bg-primary">
                                <PencilIcon class="w-4 h-4 " aria-hidden="true" />
                            </Button>
                        </div>
                        <!--BOTÓN ELIMINAR-->
                        <div title="Eliminar Sistema">
                            <Button severity="danger" @click="confirmDelete(slotProps.data.id, 'SubSistema', 'subsystem')"
                                class="hover:bg-danger">
                                <TrashIcon class="w-4 h-4 " aria-hidden="true" />
                            </Button>
                        </div>
                    </div>
                </template>
            </Column>
        </DataTable>
    </div>
    <TransitionRoot as="template" :show="open">
        <Dialog as="div" class="relative z-30" @close="open = false">
            <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100"
                leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                <div class="fixed inset-0 z-30 w-screen h-screen transition-opacity bg-gray-500 bg-opacity-75" />
            </TransitionChild>

            <div class="fixed inset-0 z-50 h-screen overflow-y-auto">
                <div class="flex items-end justify-center min-h-full p-4 text-center sm:items-center sm:p-0">
                    <TransitionChild as="template" enter="ease-out duration-300"
                        enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200"
                        leave-from="opacity-100 translate-y-0 sm:scale-100"
                        leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                        <DialogPanel
                            class="relative px-2 pt-2 pb-4 overflow-hidden text-left transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:w-full sm:max-w-lg ">
                            <div>
                                <div class="px-2 mt-2 text-center">
                                    <DialogTitle as="h3" class="text-xl font-semibold text-primary ">{{ formData.id !=
                                        0 ? 'Editar ' : 'Crear' }} SubSistema
                                    </DialogTitle> <!--Se puede usar {{ tittle }}-->
                                    <div class="p-2 mt-2 space-y-4 border border-gray-200 rounded-lg">
                                        <div class="col-span-1 py-2 md:col-span-4 p-fluid p-input-filled">
                                            <TextInput class="mt-2 text-left" type="string" label="Codigo"
                                                :placeholder="'Escriba codigo'" v-model="formData.code"
                                                :error="$page.props.errors.end_date">
                                            </TextInput>
                                            <TextInput class="mt-2 text-left" type="string" label="Nombre del SubSistema"
                                                :placeholder="'Nombre del subsistema'" v-model="formData.name"
                                                :error="$page.props.errors.name">
                                            </TextInput>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex px-2 mt-2 space-x-4">
                                <Button class="hover:bg-danger text-danger border-danger" severity="danger"
                                    @click="open = false">Cancelar</Button>
                                <Button severity="success" :loading="false"
                                    class="text-success hover:bg-success border-success" @click="submit()">
                                    {{ formData.id != 0 ? 'Actualizar ' : 'Guardar' }}
                                </Button>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>
