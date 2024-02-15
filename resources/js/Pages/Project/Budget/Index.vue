<script setup>
import Empty from '@/Components/Empty.vue';
import Loading from '@/Components/Loading.vue';
import AppLayout from '@/Layouts/AppLayout.vue'
import AccordionBudget from '@/Pages/Project/Budget/Components/AccordionBudget.vue'
import Dropdown from 'primevue/dropdown';
import { ref } from 'vue';

const props = defineProps({
    projects: Array,
})

const project = ref()
const peps = ref()
const loading = ref(false)

const totales = ref({
    materials: 0,
    services: 0,
    labor: 0,
    materials_ejecutados: 0,
    labor_ejecutados: 0,
    services_ejecutados: 0,
})
const projectSelect = async() => {
    peps.value = null
    totales.value = {
        materials: 0,
        services: 0,
        labor: 0,
        materials_ejecutados: 0,
        labor_ejecutados: 0,
        services_ejecutados: 0,
    }
    loading.value=true
    try {
        await axios.get(route('get.details.budget', project.value.id)).then((res) => {
            // console.log(Object.values(res.data.peps))
            peps.value = Object.values(res.data.peps)
            peps.value.forEach(element => {
                totales.value.materials_ejecutados = totales.value.materials_ejecutados + parseInt(element.materials_ejecutados)
                totales.value.labor_ejecutados = totales.value.labor_ejecutados + parseInt(element.labor_ejecutados)
                totales.value.services_ejecutados = totales.value.services_ejecutados + parseInt(element.services_ejecutados)
                totales.value.materials = totales.value.materials + parseInt(element.materials)
                totales.value.labor = totales.value.labor + parseInt(element.labor)
                totales.value.services = totales.value.services + parseInt(element.services)
            });
            console.log(totales.value)
            loading.value=false
        })
    } catch (e) {
        peps.value = null
        console.log(e)
        loading.value=false
    }
}

const formatCurrency = (valor, moneda) => {
    if (valor == undefined || valor == null) {
        return 'Sin definir'
    } else {
        return parseInt(valor).toLocaleString('es-CO',
            { style: 'currency', currency: moneda == null ? 'COP' : moneda, maximumFractionDigits: 0 })
    }
}
const option = ref('total')

</script>

<template>
    <AppLayout>
        <div class="h-[85vh] overflow-y-auto px-3">
            <div class="sm:max-h-[20vh] py-1">
                <span class="sm:flex justify-between mb-2">
                    <p class="text-xl flex items-center font-semibold leading-6 capitalize text-primary">
                        Presupuesto
                    </p>
                    <span class="flex flex-col sm:flex-row items-center sm:space-x-2">
                        <p>Selecciona un proyecto</p>
                        <Dropdown :options="projects" class="w-96" optionLabel="name" @change="projectSelect()" showClear
                            v-model="project" :pt="{
                                root: '!h-8',
                                input: '!py-0 !flex !items-center !text-sm !font-normal',
                                item: '!py-1 !px-3 !text-sm !font-normal',
                                filterInput: '!h-8'
                            }" />
                    </span>
                </span>
                <div v-if="peps" class="grid sm:grid-cols-4 gap-4 p-1">
                    <Button :outlined="!(option == 'material')" :key="totales.materials_ejecutados"
                        @click="option = 'material'" class="min-h-16">
                        <span class="w-full -mt-1">
                            <p class="w-full text-center font-bold">Materiales</p>
                            <p class="w-full text-center  !text-sm">{{ formatCurrency(totales.materials_ejecutados) }}</p>
                            <div class="w-full h-4 border rounded-full bg-gray-400"
                                :style="!(option == 'material') ? 'border: var(--primary-color)' : ''">
                                <div style="backgroundColor: var(--primary-color);he"
                                    :class="'w-[' + parseInt((totales.materials_ejecutados / totales.materials) * 0.01) + '%]'"
                                    class="h-full text-center text-xs text-white rounded-full">
                                </div>
                                <p class="-mt-4 text-xs text-white">
                                    {{ parseInt((totales.materials_ejecutados / totales.materials) * 0.01) }}%
                                </p>
                            </div>
                        </span>
                    </Button>
                    <Button :outlined="!(option == 'obra')" :key="totales.labor_ejecutados" @click="option = 'obra'"
                        class="min-h-16">
                        <span class="w-full -mt-1">
                            <p class="w-full text-center font-bold">Mano de obra</p>
                            <p class="w-full text-center !text-sm">{{ formatCurrency(totales.labor_ejecutados) }}</p>
                            <div class="w-full h-4 border rounded-full bg-gray-400"
                                :style="!(option == 'material') ? 'border: var(--primary-color)' : ''">
                                <div style="backgroundColor: var(--primary-color);"
                                    :class="'w-[' + parseInt((totales.labor_ejecutados / totales.labor) * 0.01) + '%]'"
                                    class="h-full text-center text-xs text-white rounded-full ">
                                </div>
                                <p class="-mt-4 text-xs text-white">
                                    {{ parseInt((totales.labor_ejecutados / totales.labor) * 0.01) }}%
                                </p>
                            </div>
                        </span>
                    </Button>
                    <Button :outlined="!(option == 'servicio')" :key="totales.services_ejecutados"
                        @click="option = 'servicio'" class="min-h-16">
                        <span class="w-full -mt-1">
                            <p class="w-full text-center font-bold">Servicios</p>
                            <p class="w-full text-center !text-sm">{{ formatCurrency(totales.services_ejecutados) }}</p>
                            <div class="w-full h-4 border rounded-full bg-gray-400"
                                :style="!(option == 'material') ? 'border: var(--primary-color)' : ''">
                                <div style="backgroundColor: var(--primary-color);"
                                    :class="'w-[' + parseInt((totales.services_ejecutados / totales.services) * 0.01) + '%]'"
                                    class="h-full text-center text-xs text-white rounded-full ">
                                </div>
                                <p class="-mt-4 text-xs text-white">
                                    {{ parseInt((totales.services_ejecutados / totales.services) * 0.01) }}%
                                </p>
                            </div>
                        </span>
                    </Button>
                    <Button :outlined="!(option == 'total')" :key="(totales.services_ejecutados +
                        totales.materials_ejecutados + totales.labor_ejecutados + totales.services_ejecutados)"
                        @click="option = 'total'" class="min-h-16">
                        <span class="w-full -mt-1">
                            <p class="w-full text-center font-bold">Total</p>
                            <p class="w-full text-center !text-sm">{{ formatCurrency(totales.services_ejecutados +
                                totales.materials_ejecutados + totales.labor_ejecutados + totales.services_ejecutados) }}
                            </p>
                            <div class="w-full h-4 border rounded-full bg-gray-400" style="border;: var(--primary-color)">
                                <div style="backgroundColor: var(--primary-color);"
                                    :class="'w-[' + parseInt((totales.services_ejecutados + totales.materials_ejecutados + totales.labor_ejecutados) / (totales.services + totales.materials + totales.labor)) * 0.01 + '%]'"
                                    class="h-full text-center text-xs text-white rounded-full ">
                                </div>
                                <p class="-mt-4 text-xs text-white">{{
                                    parseInt((totales.services_ejecutados + totales.materials_ejecutados +
                                        totales.labor_ejecutados) / (totales.services + totales.materials + totales.labor)) *
                                    0.01
                                }}%</p>
                            </div>
                        </span>
                    </Button>
                </div>
            </div>
            <div v-if="peps" class="grid grid-cols-4 font-bold border rounded-t-md">
                <p class="px-2 w-full">Actividad</p>
                <p class="px-2 w-full text-center">Presupuesto</p>
                <p class="px-2 w-full text-center ">Ejecutado</p>
                <p class="px-2 w-full text-center">Disponible</p>
            </div>
            <div class="sm:max-h-[60vh] overflow-y-auto">
                <AccordionBudget :data="peps" :option="option" />
            </div>
            <div v-if="!peps&&!loading" class="h-[60%] flex items-center">
            <Empty message="Seleccione un proyecto"></Empty>
            </div>
            <div v-if="loading" class="h-[60%] flex items-center">
            <Loading message="Cargando presupuestos, dependiendo del proyecto puede demorar un poco"></Loading>
            </div>
        </div>
    </AppLayout>
</template>