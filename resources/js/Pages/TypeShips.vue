<script setup>
const { hasRole, hasPermission } = usePermissions()
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { usePermissions } from '@/composable/permission';
import AppLayout from "@/Layouts/AppLayout.vue";
import axios from 'axios';
import CustomDataTable from '@/Components/CustomDataTable.vue';
import OverlayPanel from 'primevue/overlaypanel';
import Sidebar from 'primevue/sidebar';
import Tag from 'primevue/tag';
import CustomInput from '@/Components/CustomInput.vue';

const props = defineProps({
    typeShips: Object
})

const visibleSidebar = ref(false)
const dataSidebar = ref()

async function getProjects(id) {
    await axios.get(route('typeship.get.project', id))
        .then(response => {
            return response.data.project
        })
        .catch((error) => {
            console.log(error)
            return []
        })
}

const columns = [
    {
        field: 'name', header: 'Nombre', sortable: true, filter: true
    },
    { field: 'count_ships', header: 'Cascos', class: 'w-24', rowclass: "underline", sortable: true, filter: true, type: 'button', event: 'showHull', severity: 'info', text: true, input: false },
    {
        field: 'type', header: 'Tipo',
    },
    {
        field: 'designer', header: 'Empresa diseñadora',
    },
    {
        field: 'hull_material', header: 'Material del casco', filter: true, sortable: true, input: {
            type: 'dropdown',
            options: ['ACERO', 'ALUMINIO', 'MATERIALES COMPUESTOS']
        }
    },
    {
        field: 'length', header: 'Eslora', input: {
            type: 'number',
            suffix: ' m'
        }
    },
    {
        field: 'breadth', header: 'Manga', input: {
            type: 'number',
            suffix: ' m'
        }
    },
    {
        field: 'draught', header: 'Calado de diseño', input: {
            type: 'number',
            suffix: ' m'
        }
    },
    {
        field: 'depth', header: 'Puntal', input: {
            type: 'number',
            suffix: ' m'
        }
    },
    {
        field: 'full_load', header: 'Full load', input: {
            type: 'number',
        }
    },
    {
        field: 'light_ship', header: 'Light ship', input: {
            type: 'number',
        }
    },
    {
        field: 'power_total', header: 'Potencia', input: {
            type: 'number',
            suffix: ' Kw'
        }
    },
    {
        field: 'propulsion_type', header: 'Tipo de propulsion', input: {
            type: 'dropdown',
            options: ['Convencional', 'Azimutal', 'Water jets', 'Pump Jet', 'Eléctrica', 'Híbrida', 'Voith Schneider']
        }
    },
    {
        field: 'velocity', header: 'Velocidad maxima', input: {
            type: 'number',
            suffix: ' Km/h'
        }
    },
    {
        field: 'autonomias', header: 'Autonomia', input: {
            type: 'number',
            suffix: ' dias'
        }
    },
    {
        field: 'autonomy', header: 'Alcance', input: {
            type: 'number',
        }
    },
    {
        field: 'crew', header: 'Tripulacion maxima', input: {
            type: 'number',
            suffix: ' Personas'
        }
    },
    {
        field: 'GT', header: 'GT', input: {
            type: 'number',
        }
    },
    {
        field: 'CGT', header: 'CGT', input: {
            type: 'number',
        }
    },
    {
        field: 'bollard_pull', header: 'Bollard pull', input: {
            type: 'number',
        }
    },
    {
        field: 'clasification', header: 'Clasificacion', input: {
            type: 'number',
        }
    },
    {
        field: 'render', header: 'Render', input: {
            type: 'file',
            class: 'col-span-2',
            acceptFile: 'image/*',
            mode: 'advanced',
            multiple: false
        }
    },

];


const op = ref();
const hullsSelect = ref()
const showHull = (event, data) => {
    if (data.ships.length > 0) {
        hullsSelect.value = data.ships
        op.value.toggle(event);
    }
}

const url = [
    {
        ruta: 'typeShips.index',
        label: 'Clases',
        active: true
    }
]

const routes = {
    get: 'typeShips.index',
    update: hasPermission('typeShip edit') ? 'typeShips.update' : undefined,
    store: hasPermission('typeShip edit') ? 'typeShips.store' : undefined,
    delete: hasPermission('typeShip create') ? 'typeShips.destroy' : undefined,
}
</script>
<template>
    <AppLayout :href="url">
        <div class="h-full w-full overflow-y-auto">
            <CustomDataTable :rowsDefault="100" :columnas="columns" @buttonRowClick="showHull"
                showItem title="Clases de buque" :routes>
                <template #sidebarAdd="{ item }">
                    <span v-for="project, index in getProjects(item.data.id)">
                        <p v-if="index==0" class="font-bold text-center">Proyectos</p>
                        <span :key="index" class="grid gap-y-2 my-2">
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
                    </span>
                    <!-- {{ item }} -->
                </template>
                <!-- <template #modal="{item}">
                    <CustomInput v-model:input="item.data.name"></CustomInput>
                </template> -->
            </CustomDataTable>
        </div>
    </AppLayout>

    <!--OVERLAYPANEL-->
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
