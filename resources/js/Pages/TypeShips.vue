<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref } from 'vue';
import Button from 'primevue/button';
import CustomModal from '@/Components/CustomModal.vue';
import Image from 'primevue/image';
import { usePermissions } from '@/composable/permission';
import { useSweetalert } from '@/composable/sweetAlert';
import CustomDataTable from '@/Components/CustomDataTable.vue';
import CustomInput from '@/Components/CustomInput.vue';
import Sidebar from 'primevue/sidebar';
import OverlayPanel from 'primevue/overlaypanel';
import axios from 'axios';
import Card from 'primevue/card'
import Tag from 'primevue/tag';
import { Link } from '@inertiajs/vue3';

const { hasRole, hasPermission } = usePermissions()
const { toast } = useSweetalert();
const { confirmDelete } = useSweetalert();

const props = defineProps({
    typeShips: Object
})
const hull_materials = ['ACERO', 'ALUMINIO', 'MATERIALES COMPUESTOS']
const modalVisible = ref(false)
const modalType = ref('new')

const verfoto = (foto) => {
    try {
        return URL.createObjectURL(foto)
    } catch (error) {
        return null;
    }
}

const typeShip = useForm(
    {
        name: null,
        type: null,
        disinger: null,
        hull_material: null, //material del casco
        length: null, //eslra
        breadth: null, //Manga
        draught: null, //calado de diseño
        depth: null, //punta
        full_load: null,
        light_ship: null,
        power_total: null,
        propulsion_type: null,
        velocity: null,
        autonomias: null,
        autonomy: null,
        crew: null,
        GT: null,
        CGT: null,
        bollard_pull: null,
        clasification: null,
        render: null,
        image: null
    }
)
const showNew = () => {
    typeShip.reset()
    modalType.value = 'new'
    modalVisible.value = true
}

const save = () => {
    typeShip.post(route('typeShips.store'), {
        onSuccess: (response) => {
            modalVisible.value = false
            typeShip.reset()
            toast('Clase creada correctamente', 'success');
        },
        onError: (response) => {
            toast('Ocurrio un error', 'warning');
        }
    })
}

const showEdit = (event, dato) => {
    typeShip.id = dato.id
    typeShip.name = dato.name
    typeShip.type = dato.type
    typeShip.disinger = dato.disinger
    typeShip.hull_material = dato.hull_material //material del casco
    typeShip.length = dato.length ? parseFloat(dato.length) : null //eslra
    typeShip.breadth = dato.breadth ? parseFloat(dato.breadth) : null //Manga
    typeShip.draught = dato.draught ? parseFloat(dato.draught) : null //calado de diseño
    typeShip.depth = dato.depth ? parseFloat(dato.depth) : null //punta
    typeShip.full_load = dato.full_load ? parseFloat(dato.full_load) : null
    typeShip.light_ship = dato.light_ship ? parseFloat(dato.light_ship) : null
    typeShip.power_total = dato.power_total ? parseFloat(dato.power_total) : null
    typeShip.propulsion_type = dato.propulsion_type
    typeShip.velocity = dato.velocity ? parseFloat(dato.velocity) : null
    typeShip.autonomias = dato.autonomias ? parseFloat(dato.autonomias) : null
    typeShip.autonomy = dato.autonomy ? parseFloat(dato.autonomy) : null
    typeShip.crew = dato.crew ? parseFloat(dato.crew) : null
    typeShip.GT = dato.GT ? parseInt(dato.GT) : null
    typeShip.CGT = dato.CGT ? parseInt(dato.CGT) : null
    typeShip.bollard_pull = dato.bollard_pull ? parseInt(dato.bollard_pull) : null
    typeShip.clasification = dato.clasification
    typeShip.render = dato.render
    modalType.value = 'edit'
    modalVisible.value = true
}
const edit = () => {
    typeShip.post(route('typeShips.update', typeShip.id), {
        onSuccess: (response) => {
            modalVisible.value = false
            typeShip.reset()
            toast('Cambios guardados', 'success');
        },
        onError: (response) => {
            toast('Ocurrio un error', 'warning');
        }
    })
}

const deleteClic = (event, dato) => {
    confirmDelete(dato.id, 'Clase', 'typeShips')
}

const visibleSidebar = ref(false)
const dataSidebar = ref()
const rowClick = (event) => {
    dataSidebar.value = event.data
    dataSidebar.value.projects = []
    axios.get(route('typeship.get.project', event.data.id)).then(response => {
        dataSidebar.value.projects = response.data.project
    })
    visibleSidebar.value = true
}

const columns = [
    { field: 'name', header: 'Nombre', sortable: true, filter: true },
    { field: 'count_ships', header: 'Cascos', class: 'w-24', rowclass: "underline", sortable: true, filter: true, type: 'button', event: 'showHull', severity: 'info', text: true },
    { field: 'type', header: 'Tipo' },
    { field: 'hull_material', header: 'Material del casco', filter: true, sortable: true },
    { field: 'length', header: 'Eslora' },
    { field: 'breadth', header: 'Manga' },
];

const buttons = [
    { event: 'showEdit', severity: 'success', class: '', icon: 'fa-solid fa-pen-to-square', text: true, outlined: false, rounded: false, show: hasPermission('typeShip edit') },
    { event: 'deleteClic', severity: 'danger', icon: 'fa-solid fa-trash', text: true, outlined: false, rounded: false, show: hasPermission('typeShip create') },
]

const op = ref();
const hullsSelect = ref()
const showHull = (event, data) => {
    if (data.ships.length > 0) {
        hullsSelect.value = data.ships
        op.value.toggle(event);
    }
}
</script>

<template>
    <AppLayout>
        <div class="h-[89vh] overflow-y-auto">
            <CustomDataTable :rows-default="100" :data="typeShips" :columnas="columns" :actions="buttons"
                @showHull="showHull" @rowClic="rowClick" @showEdit="showEdit" title="Clases de buque"
                @deleteClic="deleteClic" @addClick="showNew()">
                <template #buttonHeader>
                    <Button title="Nuevo" severity="success" label="Agregar" outlined class="!h-8"
                        icon="fa-solid fa-plus" @click="showNew()" v-if="hasPermission('typeShip create')" />
                </template>
            </CustomDataTable>
        </div>
    </AppLayout>

    <CustomModal v-model:visible="modalVisible">

        <template #icon>
            <i class="text-white fa-solid fa-ship"></i>
        </template>

        <template #titulo>
            <span class="text-xl font-bold text-white white-space-nowrap">
                {{ modalType == 'new' ? 'Nueva clase' : 'Editar clase' }}</span>
        </template>

        <template #body>
            <div class="grid md:grid-cols-4 gap-2">
                <CustomInput label="Nombre" id='name' :showSup="true" v-model:input="typeShip.name" />
                <CustomInput label="Tipo de buque" id="type" type="text" v-model:input="typeShip.type" />
                <CustomInput label="Empresa diseñadora" id="designer" type="text" v-model:input="typeShip.disinger" />
                <CustomInput label="Material Casco" id="material" :options="hull_materials" type="dropdown"
                    v-model:input="typeShip.hull_material" placeholder="Selecciona un material" />
                <CustomInput label="Eslora" type="number" id="eslora" v-model:input="typeShip.length" suffix=" m" />
                <CustomInput label="Manga" type="number" id="breadth" v-model:input="typeShip.breadth" suffix=" m" />
                <CustomInput label="Calado de diseño" id="draught" type="number" v-model:input="typeShip.draught"
                    suffix=" m" />
                <CustomInput label="Puntal" type="number" id="depth" v-model:input="typeShip.depth" suffix=" m" />
                <CustomInput label="Full Load" type="number" id="full_load" v-model:input="typeShip.full_load" />
                <CustomInput label="Ligth Ship" type="number" id="light_ship" v-model:input="typeShip.light_ship" />
                <CustomInput label="Potencia" type="number" id="power_total" v-model:input="typeShip.power_total"
                    suffix=" Kw" />
                <CustomInput label="Tipo de propulsion" type="dropdown"
                    :options="['Convencional', 'Azimutal', 'Water jets', 'Pump Jet', 'Eléctrica', 'Híbrida', 'Voith Schneider']"
                    id="propulsion_type" v-model:input="typeShip.propulsion_type"
                    placeholder="Selecciona un material" />
                <CustomInput label="Velocidad maxima" type="text" id="velocity" v-model:input="typeShip.velocity"
                    suffix=" Km/h" />
                <CustomInput label="Autonomia" type="number" id="autonomias" v-model:input="typeShip.autonomias"
                    suffix=" dias" />
                <CustomInput label="Alcance" type="number" id="autonomy" v-model:input="typeShip.autonomy" />
                <CustomInput label="Tripulacion maxima" id="crew" type="number" v-model:input="typeShip.crew"
                    suffix=" Personas" />
                <CustomInput label="GT" type="number" id=gt v-model:input="typeShip.GT" />
                <CustomInput label="CGT" type="number" id="cgt" v-model:input="typeShip.CGT" />
                <CustomInput label="Bollard pull" type="number" id="bollard_pull"
                    v-model:input="typeShip.bollard_pull" />
                <CustomInput label="Clasificacion" type="text" id="clasification"
                    v-model:input="typeShip.clasification" />
                <CustomInput label="Imagen" type="file" id="image" v-model:input="typeShip.image"
                    acceptFile="image/*" />
                <span class="flex items-center justify-center">
                    <Image v-if="typeShip.image != null || typeShip.render != null" id="verFoto" alt="imagen" preview
                        height="50" width="50" :pt="{
            }">
                        <template #image>
                            <div class="flex items-center justify-center w-full">
                                <img :src="typeShip.image ? verfoto(typeShip.image) : typeShip.render" alt="image"
                                    class="h-12" />
                            </div>
                        </template>

                        <template #preview="slotProps">
                            <img :src="typeShip.image ? verfoto(typeShip.image) : typeShip.render"
                                class="!max-w-[80vw] !max-h-[80vh]" alt="preview" :style="slotProps.style"
                                @click="slotProps.previewCallback" />
                        </template>
                    </Image>
                </span>

            </div>
        </template>

        <template #footer>
            <Button severity="primary" outlined label="Guardar" icon="fa-solid fa-floppy-disk"
                @click="modalType == 'new' ? save() : edit()" />
            <Button severity="danger" outlined label="Cancelar" icon="fa-regular fa-circle-xmark"
                @click="modalVisible = false; typeShip.reset()" />
        </template>
    </CustomModal>

    <Sidebar v-model:visible="visibleSidebar" :showCloseIcon="false" position="right">
        <span class="flex justify-center">
            <img :src="dataSidebar.render" alt="ImageShip" onerror="this.src='/svg/cotecmar-logo.svg'"
                class="min-w-16 py-0.5 rounded-lg sm:h-12 sm:w-16 object-cover" draggable="false" />
        </span>

        <p class="font-bold text-center">{{ dataSidebar.name }}</p>
        <p class="text-center">{{ dataSidebar.type }}</p>
        <span class="grid grid-cols-2 gap-1 text-sm">
            <p v-if="dataSidebar.disinger" class="font-bold">Diseñador:</p>
            <p v-if="dataSidebar.disinger" class="">{{ dataSidebar.disinger }}</p>

            <p v-if="dataSidebar.hull_material" class="font-bold h-full flex items-center">Material del casco:</p>
            <p v-if="dataSidebar.hull_material" class="">{{ dataSidebar.hull_material }}</p>

            <p v-if="dataSidebar.length" class="font-bold">Eslora:</p>
            <p v-if="dataSidebar.length" class="">{{ dataSidebar.length }} m</p>

            <p v-if="dataSidebar.breadth" class="font-bold">Manga:</p>
            <p v-if="dataSidebar.breadth" class="">{{ dataSidebar.breadth }} m</p>

            <p v-if="dataSidebar.draught" class="font-bold">Calado:</p>
            <p v-if="dataSidebar.draught" class="">{{ dataSidebar.draught }} m</p>

            <p v-if="dataSidebar.depth" class="font-bold">Puntal:</p>
            <p v-if="dataSidebar.depth" class="">{{ dataSidebar.depth }} m</p>

            <p v-if="dataSidebar.full_load" class="font-bold">Full Load:</p>
            <p v-if="dataSidebar.full_load" class="">{{ dataSidebar.full_load }} m</p>

            <p v-if="dataSidebar.light_ship" class="font-bold">Light Ship:</p>
            <p v-if="dataSidebar.light_ship" class="">{{ dataSidebar.light_ship }} m</p>

            <p v-if="dataSidebar.power_total" class="font-bold">Potencia:</p>
            <p v-if="dataSidebar.power_total" class="">{{ dataSidebar.power_total }} kw/h</p>

            <p v-if="dataSidebar.propulsion_type" class="font-bold">Tipo de propulsion:</p>
            <p v-if="dataSidebar.propulsion_type" class="">{{ dataSidebar.propulsion_type }}</p>

            <p v-if="dataSidebar.velocity" class="font-bold">Velocidad maxima:</p>
            <p v-if="dataSidebar.velocity" class="">{{ dataSidebar.velocity }} nudos</p>

            <p v-if="dataSidebar.autonomias" class="font-bold">Autonomia:</p>
            <p v-if="dataSidebar.autonomias" class="">{{ dataSidebar.autonomias }} dias</p>

            <p v-if="dataSidebar.autonomy" class="font-bold">Alcance:</p>
            <p v-if="dataSidebar.autonomy" class="">{{ dataSidebar.autonomy }}</p>

            <p v-if="dataSidebar.crew" class="font-bold">Tripulacion:</p>
            <p v-if="dataSidebar.crew" class="">{{ parseInt(dataSidebar.crew) }} personas</p>

            <p v-if="dataSidebar.GT" class="font-bold">GT:</p>
            <p v-if="dataSidebar.GT" class="">{{ dataSidebar.GT }} Ton</p>

            <p v-if="dataSidebar.CGT" class="font-bold">CGT:</p>
            <p v-if="dataSidebar.CGT" class="">{{ dataSidebar.CGT }} Ton</p>

            <p v-if="dataSidebar.bollard_pull" class="font-bold">Bollard pull:</p>
            <p v-if="dataSidebar.bollard_pull" class="">{{ dataSidebar.bollard_pull }}</p>

            <p v-if="dataSidebar.clasification" class="font-bold">Clasificacion:</p>
            <p v-if="dataSidebar.clasification" class="">{{ dataSidebar.clasification }}</p>
        </span>
        <p v-if="dataSidebar.projects.length > 0" class="font-bold text-center">Proyectos</p>
        <span v-for="(project, index) in dataSidebar.projects" :key="index" class="grid gap-y-2 my-2">
            <div class="border-2 p-1 rounded-lg cursor-default">
                <span class="flex justify-between items-center">
                    <Link :href="route('projects.goToProjectOverview', project.id)"
                        v-tooltip.left="'Nombre del proyecto'" class="font-bold"> {{ project.name }}</Link>
                    <span v-tooltip.top="'Codigo SAP'" class="text-sm">{{ project.SAP_code }}</span>
                </span>
                <span class="text-xs" v-tooltip.top="'Tipo de proyecto'">{{ project.type }}</span>
                <span class="flex justify-between items-center">
                    <Tag severity="info" v-tooltip.left="'Estado'" :value="project.status" />
                    <span class="text-sm" v-tooltip.left="'Gerencia'">{{ project.gerencia }}</span>
                </span>
            </div>
        </span>
    </Sidebar>

    <OverlayPanel ref="op">
        <ul class="list-none p-0 m-0 flex flex-col gap-2 w-96 max-w-96 max-h-52 overflow-y-auto">
            <li v-for="hull in hullsSelect" :key="hull.name" class="flex items-center gap-2">
                <img :src="hull.file" alt="ImageShip" onerror="this.src='/svg/cotecmar-logo.svg'"
                    class="min-w-16 py-0.5 rounded-lg sm:h-12 sm:w-16 object-cover" draggable="false" />
                <span>
                    <span class="font-medium">{{ hull.name }}</span>
                    <span class="flex items-center justify-between">
                        <p>{{ hull.acronyms }}</p>
                        <div class="text-sm text-color-secondary">Id del casco: {{ hull.idHull }}</div>
                    </span>
                </span>
            </li>
        </ul>
    </OverlayPanel>
</template>
