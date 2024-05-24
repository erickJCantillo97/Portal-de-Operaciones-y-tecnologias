<script setup>
import { KeyIcon, SquaresPlusIcon, ClockIcon } from '@heroicons/vue/24/outline'
import { ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import BaseActivities from './../Components/SWBS/BaseActivities.vue'
import ConstructiveGroup from './../Components/SWBS/ConstructiveGroup.vue'
import CostClasses from '../Components/CostClasses.vue'
import Managments from '../Components/Managments.vue'
import Process from './../Components/SWBS/Process.vue'
// import Shifts from './../Components/Shifts.vue'
import SubSystems from './../Components/SWBS/SubSystems.vue'
import Systems from './../Components/SWBS/Systems.vue'
import Warehouses from '../Components/Warehouses.vue'
import TabView from 'primevue/tabview'
import TabPanel from 'primevue/tabpanel'
import CustomUpload from '@/Components/CustomUpload.vue'

/**TODO
 * CRUD Sistema: Grupo Constructivo, Código y Descripción
 * CRUD Subsistema: Sistema, Código y Descripción
 * CRUD Proceso: Subsistema, Código y Descripción
 * CRUD Actividades Básica: Proceso, Código y Descripción
*/

const menu = ref('VB')

const navigation = [
    { name: 'Variables Basicas', value: 'VB', icon: SquaresPlusIcon, current: true },
    { name: 'SWBS', value: 'SW', icon: KeyIcon, current: false },
    // { name: 'Horarios', value: 'SC', icon: ClockIcon, current: false }, //SC->schedules
    // { name: 'Sugerencias', value: 'SU', icon: UserGroupIcon, current: false },
    // { name: 'Clases de Costo', value: 'CC', icon: SquaresPlusIcon, current: false },
]

const tabsPanelsSWBS = [
    { header: 'Grupos Constructivos', component: ConstructiveGroup },
    { header: 'Sistemas', component: Systems },
    { header: 'Subsistemas', component: SubSystems },
    { header: 'Procesos', component: Process },
    { header: 'Actividades Básicas', component: BaseActivities }
]

const tabsPanelsBasicsVariables = [
    { header: 'Gerencias', component: Managments },
    { header: 'Clases de Costos', component: CostClasses },
    { header: 'Almacenes', component: Warehouses },
]

// const tabsPanelsSchedule = [
//     { header: 'Horario', component: Shifts },
// ]

</script>
<template>
    <AppLayout>
        <div class=" h-full w-full p-2 lg:grid lg:grid-cols-12 lg:gap-x-2">
            <aside class="px-2 py-6 sm:px-6 lg:col-span-2 lg:px-0 lg:py-0">
                <nav class="space-y-1">
                    <a v-for="item in navigation" :key="item.name"
                        :class="[menu == item.value ? 'bg-gray-50 text-indigo-700 hover:bg-white hover:text-indigo-700' : 'text-gray-900 hover:bg-gray-50 hover:text-gray-900', 'group flex items-center rounded-md px-3 py-2 text-sm font-medium cursor-pointer']"
                        :aria-current="menu == item.value ? 'page' : undefined" @click="menu = item.value">
                        <component :is="item.icon"
                            :class="[menu == item.value ? 'text-indigo-500 group-hover:text-indigo-500' : 'text-gray-400 group-hover:text-gray-500', '-ml-1 mr-3 h-6 w-6 flex-shrink-0']"
                            aria-hidden="true" />
                        <span class="truncate">{{ item.name }}</span>
                    </a>
                </nav>
            </aside>

            <div class="space-y-6 sm:px-6 lg:col-span-10 lg:px-0">
                <div class="w-full">
                    <!--Basic Variables-->
                    <div class="mx-auto w-full rounded-2xl shadow-sm bg-white p-2" v-if="menu == 'VB'">
                        <div>
                            <h3 class="text-base font-semibold leading-6 text-gray-900">
                                Variables de Sistema
                            </h3>
                            <p class="mt-1 text-sm text-justify text-gray-500">
                                Aquí encontrarás las variables en la que
                                se sustenta la aplicación,
                                <span class="text-red-600 font-extrabold text-md uppercase underline">
                                    ¡proceda con precaución!
                                </span>
                                o ponte en contacto con los administradores del sistema.
                            </p>
                        </div>

                        <TabView>
                            <TabPanel v-for="(tabs, index) in tabsPanelsBasicsVariables" :key="index"
                                :header="tabs.header">
                                <component :is="tabs.component" />
                            </TabPanel>
                        </TabView>
                    </div>

                    <!--SWBS-->
                    <div class="mx-auto w-full rounded-2xl shadow-sm bg-white p-2" v-if="menu == 'SW'">
                        <div>
                            <h3 class="text-base font-semibold leading-6 text-gray-900">
                                SWBS
                            </h3>
                            <p class="mt-1 text-sm text-justify text-gray-500">
                                Aquí encontrarás la base de datos que soporta
                                el SWBS en el sistema, añade nuevas actividades, sistemas, subsistemas y grupos
                                constructivos.</p>
                        </div>

                        <TabView>
                            <TabPanel v-for="(tabs, index) in tabsPanelsSWBS" :key="index" :header="tabs.header">
                                <component :is="tabs.component" />
                            </TabPanel>
                        </TabView>
                    </div>

                    <!--Schedule-->
                    <!-- <div class="mx-auto w-full rounded-2xl shadow-sm bg-white p-2" v-if="menu == 'SC'">
                        <div>
                            <h3 class="text-base font-semibold leading-6 text-gray-900">
                                Horarios
                            </h3>
                            <p class="mt-1 text-sm text-justify text-gray-500">
                                Aquí encontrarás los horarios basicos
                                parametrizados.
                            </p>
                        </div>

                        <TabView>
                            <TabPanel v-for="(tabs, index) in tabsPanelsSchedule" :key="index" :header="tabs.header">
                                <component :is="tabs.component" />
                            </TabPanel>
                        </TabView>
                    </div> -->
                </div>
            </div>
        </div>
    </AppLayout>
</template>
