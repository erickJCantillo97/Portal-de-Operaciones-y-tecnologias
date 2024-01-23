<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import { useSweetalert } from '@/composable/sweetAlert';
import UserTable from '@/Components/UserTable.vue';
import Listbox from 'primevue/listbox';
import CustomModal from '@/Components/CustomModal.vue';
import Loading from '@/Components/Loading.vue';
import CustomDataTable from '@/Components/CustomDataTable.vue';
const { toast } = useSweetalert()
const { confirmDelete } = useSweetalert()

const props = defineProps({
    miPersonal: Array,
})
const personal = ref([])

var form = useForm({
    users: [],
    fecha_devolucion: null
});

const submit = () => {
    router.post(route('personal.store'), form, {
        onSuccess: () => {
            form.users = []
            modalVisible.value = false
            toast('personal añadido exitosamente', 'success')
        }
    });
}

const getPersonal = () => {
    axios.get(route('personal.activos')).then((res) => {
        personal.value = res.data.personal
    })
}

const modalVisible = ref(false)
const showNew = () => {
    modalVisible.value = true
    if (personal.value.length == 0)
        getPersonal()
}

const quitar = (persona) => {
    form.users = form.users.filter(object => object.Num_SAP !== persona.Num_SAP);
}

const del = (event, data) => {
    confirmDelete(data.Num_SAP, 'Empleado', 'personal')
}

const columnas = [
    {
        field: 'Nombres_Apellidos', header: 'Nombre', filter: true, sortable: true, type: 'object', objectRows: {
            photo: { field: 'photo' },
            primary: { field: 'Nombres_Apellidos' },
            secundary: { field: 'Cargo' }
        }
    },
    { field: 'Fecha_Final', header: 'Fin contrato', filter: true, sortable: true, type: 'date' },
    { field: 'Costo_Hora', header: 'Costo hora', filter: true, sortable: true, type: 'currency' },
    { field: 'Costo_Mes', header: 'costo mes', filter: true, sortable: true, type: 'currency' },
    { field: '', header: 'grupos', },
]
const buttons = [
    { event: 'delete', severity: 'danger', class: '', icon: 'fa-regular fa-trash-can', text: true, outlined: false, rounded: false },
]
</script>

<template>
    <AppLayout>
        <div class="h-[89vh] overflow-y-auto">
            <CustomDataTable title="Mi personal" :rowsDefault="20" :data="miPersonal" :columnas="columnas"
                :actions="buttons" @delete="del">
                <template #buttonHeader>
                    <Button severity="success" type="button" outlined label="Agregar" icon="fa-solid fa-plus"
                        @click="showNew()" />
                </template>
            </CustomDataTable>
        </div>
    </AppLayout>


    <CustomModal v-model:visible="modalVisible">
        <template #icon>
            <span class="text-white material-symbols-outlined text-4xl">
                engineering
            </span>
        </template>
        <template #titulo>
            <span class="text-xl font-bold text-white white-space-nowrap">Añadir Personal</span>
        </template>
        <template #body>
            <div class="flex py-2 space-x-4">
                <div class="w-1/2 space-y-8">
                    <div class="p-fluid border-0 p-2 ">
                        <label for="">Seleccionar Personal</label>
                        <Listbox multiple v-model="form.users" listStyle="height:230px"
                            :filterFields="['Nombres_Apellidos', 'Cargo', 'Identificacion', 'Oficina']" :filter="true"
                            :options="personal" filter optionLabel="name" class="w-full md:w-14rem"
                            :loading="personal.length == 0">
                            <template #option="slotProps">
                                <UserTable :user="slotProps.option"></UserTable>
                            </template>
                            <template #empty>
                                <Loading message="Cargando Personal" />
                            </template>
                        </Listbox>
                    </div>
                </div>
                <div class="w-1/2 ">
                    <h3 class="text-center font-bold text-lg">Personal Seleccionado</h3>
                    <div class="block space-y-4 h-[320px] custom-scroll overflow-y-auto shadow-lg rounded-lg p-2">
                        <div v-for="persona of form.users" class="flex justify-between">
                            <UserTable :user="persona" :photo="true">
                            </UserTable>
                            <span class="flex items-center">
                                <Button severity="danger" @click="quitar(persona)" text icon="fa-regular fa-circle-xmark" />
                            </span>
                        </div>

                    </div>
                </div>
            </div>
        </template>
        <template #footer>
            <Button severity="success" icon="fa-solid fa-floppy-disk" v-if="form.users.length > 0" @click="submit()"
                label="Guardar" />
            <Button severity="danger" @click="modalVisible = false" icon="fa-regular fa-circle-xmark" label="Cancelar" />
        </template>
    </CustomModal>
</template>
