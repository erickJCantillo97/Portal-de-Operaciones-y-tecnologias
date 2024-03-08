<script setup>
import Empty from '@/Components/Empty.vue';
import Loading from '@/Components/Loading.vue';
import AppLayout from '@/Layouts/AppLayout.vue'
import AccordionBudget from '@/Pages/Project/Budget/Components/AccordionBudget.vue'
import Dropdown from 'primevue/dropdown';
import { ref } from 'vue';
import { useToast } from "primevue/usetoast";
const toast = useToast();

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
    total: 0,
    materials_ejecutados: 0,
    labor_ejecutados: 0,
    services_ejecutados: 0,
    total_ejecutado: 0,
    materials100: 0,
    services100: 0,
    labor100: 0,
    total_100: 0
})

//Esto solo lo entendi cuando lo hice... si lo tocas es tu responsabilidad :D rezare por ti, aunque soy ateo...
function sumaEjecutados(pep) {
    if (pep.peps?.length > 0) {
        pep.peps.forEach((subPep) => {
            sumaEjecutados(subPep)
            pep.materials_ejecutados = parseInt(pep.materials_ejecutados) + parseInt(subPep.materials_ejecutados)
            pep.labor_ejecutados = parseInt(pep.labor_ejecutados) + parseInt(subPep.labor_ejecutados)
            pep.services_ejecutados = parseInt(pep.services_ejecutados) + parseInt(subPep.services_ejecutados)
        })
    } else if (pep.grafos?.length > 0) {
        let materials_ejecutados = 0, labor_ejecutados = 0, services_ejecutados = 0
        pep.grafos.forEach((grafo) => {
            const ejecutado = sumaEjecutados(grafo);
            materials_ejecutados += ejecutado?.materials_ejecutados ?? 0
            labor_ejecutados += ejecutado?.labor_ejecutados ?? 0
            services_ejecutados += ejecutado?.services_ejecutados ?? 0
            grafo.materials_ejecutados = ejecutado?.materials_ejecutados ?? 0;
            grafo.labor_ejecutados = ejecutado?.labor_ejecutados ?? 0;
            grafo.services_ejecutados = ejecutado?.services_ejecutados ?? 0;
        });
        pep.materials_ejecutados = parseInt(materials_ejecutados)
        pep.labor_ejecutados = parseInt(labor_ejecutados)
        pep.services_ejecutados = parseInt(services_ejecutados)

    } else if (pep.operaciones?.length > 0) {
        let materials_ejecutados = 0
        let labor_ejecutados = 0
        let services_ejecutados = 0
        pep.operaciones.forEach(operacion => {
            materials_ejecutados = materials_ejecutados + parseInt(operacion.materials_ejecutados);
            labor_ejecutados = labor_ejecutados + parseInt(operacion.labor_ejecutados);
            services_ejecutados = services_ejecutados + parseInt(operacion.services_ejecutados);
        });
        return { materials_ejecutados, labor_ejecutados, services_ejecutados }
    }
}

const projectSelect = async () => {
    peps.value = null
    if (project.value.id) {
        totales.value = {
            materials: 0,
            services: 0,
            labor: 0,
            total: 0,
            materials_ejecutados: 0,
            labor_ejecutados: 0,
            services_ejecutados: 0,
            total_ejecutado: 0,
            materials100: 0,
            services100: 0,
            labor100: 0,
            total_100: 0
        }
        loading.value = true
        try {
            await axios.get(route('get.details.budget', project.value.id)).then((res) => {
                peps.value = Object.values(res.data.peps)
                peps.value.forEach((pep) => {
                    sumaEjecutados(pep)
                    totales.value.materials_ejecutados += parseInt(pep.materials_ejecutados)
                    totales.value.labor_ejecutados += parseInt(pep.labor_ejecutados)
                    totales.value.services_ejecutados += parseInt(pep.services_ejecutados)
                    totales.value.materials += parseInt(pep.materials)
                    totales.value.labor += parseInt(pep.labor)
                    totales.value.services += parseInt(pep.services)
                });
                totales.value.total_ejecutado = totales.value.materials_ejecutados + totales.value.services_ejecutados + totales.value.labor_ejecutados
                totales.value.total = totales.value.materials + totales.value.services + totales.value.labor
                totales.value.materials100 = (totales.value.materials_ejecutados / totales.value.materials) * 100
                totales.value.services100 = (totales.value.services_ejecutados / totales.value.services) * 100
                totales.value.labor100 = (totales.value.labor_ejecutados / totales.value.labor) * 100
                totales.value.total_100 = ((totales.value.materials_ejecutados + totales.value.services_ejecutados + totales.value.labor_ejecutados) / (totales.value.materials + totales.value.services + totales.value.labor)) * 100
                loading.value = false
            })
        } catch (e) {
            peps.value = null
            toast.add({ text: 'El proyecto no tiene presupuesto asignado', severity: 'error', life: 3000, group: 'customToast' })
            console.log(e)
            loading.value = false
        }
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
                        <Dropdown :options="projects" class="w-96" optionLabel="name" @change="projectSelect()"
                            showClear v-model="project" :pt="{
                            root: '!h-8',
                            input: '!py-0 !flex !items-center !text-sm !font-normal',
                            item: '!py-1 !px-3 !text-sm !font-normal',
                            filterInput: '!h-8'
                        }" />
                    </span>
                </span>
                <div v-if="peps" class="grid sm:grid-cols-4 gap-4 p-1">
                    <Button :outlined="!(option == 'material')" raised :key="totales.materials"
                        @click="option = 'material'" class="min-h-16">
                        <span class="w-full -mt-1">
                            <p class="w-full text-center font-bold">Materiales</p>
                            <div class="grid-cols-2 grid py-0.5 -mt-0.5">
                                <p title="Presupuesto" class="w-full text-center border-r !text-sm">{{
                            formatCurrency(totales.materials) }}
                                </p>
                                <p title="Ejecutado" class="w-full text-center !text-sm border-l"> {{
                            formatCurrency(totales.materials_ejecutados) }}
                                </p>
                            </div>
                            <div class="w-full h-4 border rounded-sm bg-gray-400" :title="totales.materials100 + '%'">
                                <div style="background-color: var(--primary-color);"
                                    :style="'width:' + totales.materials100 + '%;'"
                                    class="h-full text-center text-xs rounded-sm text-white max-w-full">
                                </div>
                                <p class="-mt-4 text-xs text-white">
                                    {{ totales.materials100.toFixed(2) }}%
                                </p>
                            </div>
                        </span>
                    </Button>
                    <Button :outlined="!(option == 'obra')" raised :key="totales.labor_ejecutados"
                        @click="option = 'obra'" class="min-h-16">
                        <span class="w-full -mt-1">
                            <p class="w-full text-center font-bold">Mano de obra</p>
                            <div class="grid-cols-2 grid py-0.5 -mt-0.5">
                                <p title="Presupuesto" class="w-full text-center border-r !text-sm"> {{
                            formatCurrency(totales.labor) }}
                                </p>
                                <p title="Ejecutado" class="w-full text-center !text-sm border-l"> {{
                            formatCurrency(totales.labor_ejecutados) }}
                                </p>
                            </div>
                            <div class="w-full h-4 border rounded-sm bg-gray-400" :title="totales.labor100 + '%'">
                                <div style="background-color: var(--primary-color);"
                                    :style="'width:' + totales.labor100 + '%'"
                                    class="h-full text-center text-xs rounded-sm text-white">
                                </div>
                                <p class="-mt-4 text-xs text-white">
                                    {{ totales.labor100.toFixed(2) }}%
                                </p>
                            </div>
                        </span>
                    </Button>
                    <Button :outlined="!(option == 'servicio')" raised :key="totales.services_ejecutados"
                        @click="option = 'servicio'" class="min-h-16">
                        <span class="w-full -mt-1">
                            <p class="w-full text-center font-bold">Servicios</p>
                            <div class="grid-cols-2 grid py-0.5 -mt-0.5">
                                <p title="Presupuesto" class="w-full text-center border-r !text-sm"> {{
                            formatCurrency(totales.services) }}
                                </p>
                                <p title="Ejecutado" class="w-full text-center !text-sm border-l"> {{
                            formatCurrency(totales.services_ejecutados) }}
                                </p>
                            </div>
                            <div class="w-full h-4 border rounded-sm bg-gray-400" :title="totales.services100 + '%'">
                                <div style="background-color: var(--primary-color);"
                                    :style="'width:' + totales.services100 + '%'"
                                    class="h-full text-center text-xs rounded-sm text-white  ">
                                </div>
                                <p class="-mt-4 text-xs text-white">
                                    {{ totales.services100.toFixed(2) }}%
                                </p>
                            </div>
                        </span>
                    </Button>
                    <Button :outlined="!(option == 'total')" raised :key="totales.total" @click="option = 'total'"
                        class="min-h-16">
                        <span class="w-full -mt-1">
                            <p class="w-full text-center font-bold">Total</p>
                            <div class="grid-cols-2 grid py-0.5 -mt-0.5">
                                <p title="Presupuesto" class="w-full text-center border-r !text-sm"> {{
                            formatCurrency(totales.total) }}
                                </p>
                                <p title="Ejecutado" class="w-full text-center !text-sm border-l"> {{
                            formatCurrency(totales.total_ejecutado) }}
                                </p>
                            </div>
                            <div class="w-full h-4 border  rounded-sm bg-gray-400" :title="totales.total_100 + '%'">
                                <div style="background-color: var(--primary-color);"
                                    :style="'width:' + totales.total_100 + '%'"
                                    class="h-full text-center text-xs text-white rounded-sm ">
                                </div>
                                <p class="-mt-4 text-xs text-white">{{ totales.total_100.toFixed(2) }}%</p>
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
            <div v-if="!peps && !loading" class="h-[60%] flex items-center">
                <Empty message="Seleccione un proyecto"></Empty>
            </div>
            <div v-if="loading" class="h-[60%] flex items-center">
                <Loading message="Cargando presupuestos, dependiendo del proyecto puede demorar un poco"></Loading>
            </div>
        </div>
    </AppLayout>
</template>