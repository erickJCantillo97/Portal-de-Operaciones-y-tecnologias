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
    materials_ejecutados: 0,
    labor_ejecutados: 0,
    services_ejecutados: 0,
})

function sumaEjecutados(pep) {
    if (pep.peps?.length > 0) {
        let suma = 0
        pep.peps.forEach((subPep, index) => {
            var data = sumaEjecutados(subPep)
            suma = suma + (data?.suma ?? 0)
            pep.peps[index].materials_ejecutados = (data?.suma ?? 0) + parseInt(pep.peps[index].materials_ejecutados)
            pep.peps[index].grafos = data?.grafos ?? []
        })
        pep.materials_ejecutados = suma + parseInt(pep.materials_ejecutados)
    } else if (pep.grafos?.length > 0) {
        let suma = 0
        pep.grafos.forEach((grafo, index) => {
            // pep.grafos[index].materials_ejecutados = parseFloat(pep.grafos[index].materials_ejecutados)
            const aux = sumaEjecutados(grafo);
            suma = aux + suma
            pep.grafos[index].materials_ejecutados = aux + parseInt(pep.grafos[index].materials_ejecutados);
        });
        // console.log(pep.grafos[0].materials_ejecutados)
        return { grafos: pep.grafos, suma: suma } //retorna los grafos con el materials_ejecutados
        
    } else if (pep.operaciones?.length > 0) {
        let suma = 0
        pep.operaciones.forEach(operacion => {
            suma = suma + parseInt(operacion.materials_ejecutados);
        });
        return suma //retorna la suma del materials_ejecutados de las operaciones
    }
}

const projectSelect = async () => {
    peps.value = null
    totales.value = {
        materials: 0,
        services: 0,
        labor: 0,
        materials_ejecutados: 0,
        labor_ejecutados: 0,
        services_ejecutados: 0,
    }
    loading.value = true
    try {
        await axios.get(route('get.details.budget', project.value.id)).then((res) => {
            peps.value = Object.values(res.data.peps)
            peps.value.forEach((pep) => {
                sumaEjecutados(pep)
            });
            loading.value = false
        })
    } catch (e) {
        peps.value = null
        toast.add({ text: 'El proyecto no tiene presupuesto asignado', severity: 'error', life: 3000, group: 'customToast' })
        console.log(e)
        loading.value = false
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
                    <Button :outlined="!(option == 'material')" raised :key="totales.materials_ejecutados"
                        @click="option = 'material'" class="min-h-16">
                        <span class="w-full -mt-1">
                            <p class="w-full text-center font-bold">Materiales</p>
                            <p class="w-full text-center  !text-sm">{{ formatCurrency(totales.materials_ejecutados) }}
                            </p>
                            <div class="w-full h-4 border rounded-sm bg-gray-400"
                                :title="parseFloat((totales.materials_ejecutados / parseInt(totales.materials)) / 100) + '%'"
                                :style="!(option == 'material') ? 'border: var(--primary-color)' : ''">
                                <div style="background-color: var(--primary-color);"
                                    :style="'width:' + ((totales.materials_ejecutados / parseInt(totales.materials)) / 100) + '%;'"
                                    class="h-full text-center text-xs rounded-sm text-white max-w-full">
                                </div>
                                <p class="-mt-4 text-xs text-white">
                                    {{ parseFloat((totales.materials_ejecutados / parseInt(totales.materials)) /
                            100).toFixed(2) }}%
                                </p>
                            </div>
                        </span>
                    </Button>
                    <Button :outlined="!(option == 'obra')" raised :key="totales.labor_ejecutados"
                        @click="option = 'obra'" class="min-h-16">
                        <span class="w-full -mt-1">
                            <p class="w-full text-center font-bold">Mano de obra</p>
                            <p class="w-full text-center !text-sm">{{ formatCurrency(totales.labor_ejecutados) }}</p>
                            <div class="w-full h-4 border rounded-sm bg-gray-400"
                                :title="parseFloat((totales.labor_ejecutados / parseInt(totales.labor)) / 100) + '%'"
                                :style="!(option == 'obra') ? 'border: var(--primary-color)' : ''">
                                <div style="background-color: var(--primary-color);"
                                    :style="'width:' + (parseInt((totales.labor_ejecutados / parseInt(totales.labor)) / 100)) + '%'"
                                    class="h-full text-center text-xs rounded-sm text-white">
                                </div>
                                <p class="-mt-4 text-xs text-white">
                                    {{ parseFloat((totales.labor_ejecutados / parseInt(totales.labor)) / 100).toFixed(2)
                                    }}%
                                </p>
                            </div>
                        </span>
                    </Button>
                    <Button :outlined="!(option == 'servicio')" raised :key="totales.services_ejecutados"
                        @click="option = 'servicio'" class="min-h-16">
                        <span class="w-full -mt-1">
                            <p class="w-full text-center font-bold">Servicios</p>
                            <p class="w-full text-center !text-sm">{{ formatCurrency(totales.services_ejecutados) }}</p>
                            <div class="w-full h-4 border rounded-sm bg-gray-400"
                                :title="parseFloat((totales.services_ejecutados / parseInt(totales.services)) / 100) + '%'"
                                :style="!(option == 'servicio') ? 'border: var(--primary-color)' : ''">
                                <div style="background-color: var(--primary-color);"
                                    :style="'width:' + (parseInt((totales.services_ejecutados / parseInt(totales.services)) / 100)) + '%'"
                                    class="h-full text-center text-xs rounded-sm text-white  ">
                                </div>
                                <p class="-mt-4 text-xs text-white">
                                    {{ parseFloat((totales.services_ejecutados / parseInt(totales.services)) /
                            100).toFixed(2) }}%
                                </p>
                            </div>
                        </span>
                    </Button>
                    <Button :outlined="!(option == 'total')" raised :key="(totales.services_ejecutados +
                            totales.materials_ejecutados + totales.labor_ejecutados)
                            " @click="option = 'total'" class="min-h-16">
                        <span class="w-full -mt-1">
                            <p class="w-full text-center font-bold">Total</p>
                            <p class="w-full text-center !text-sm">{{ formatCurrency(totales.services_ejecutados +
                            totales.materials_ejecutados + totales.labor_ejecutados) }}
                            </p>
                            <div class="w-full h-4 border  rounded-sm bg-gray-400" :title="parseFloat(((totales.services_ejecutados + totales.materials_ejecutados +
                            totales.labor_ejecutados) / (parseInt(totales.services) + parseInt(totales.materials) +
                                parseInt(totales.labor))) / 100) + '%'" style="border;: var(--primary-color)">
                                <div style="background-color: var(--primary-color);"
                                    :style="'width:' + (parseInt((totales.services_ejecutados + totales.materials_ejecutados + totales.labor_ejecutados) / (totales.services + totales.materials + totales.labor)) * 0.01) + '%'"
                                    class="h-full text-center text-xs text-white rounded-sm ">
                                </div>
                                <p class="-mt-4 text-xs text-white">{{
                            parseFloat(((totales.services_ejecutados + totales.materials_ejecutados +
                                totales.labor_ejecutados) / (parseInt(totales.services) +
                                    parseInt(totales.materials) +
                                    parseInt(totales.labor))) / 100).toFixed(2)
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
            <div v-if="!peps && !loading" class="h-[60%] flex items-center">
                <Empty message="Seleccione un proyecto"></Empty>
            </div>
            <div v-if="loading" class="h-[60%] flex items-center">
                <Loading message="Cargando presupuestos, dependiendo del proyecto puede demorar un poco"></Loading>
            </div>
        </div>
    </AppLayout>
</template>