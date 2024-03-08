<script setup>
import Accordion from 'primevue/accordion';
import AccordionTab from 'primevue/accordiontab';
import AccordionBudget from '@/Pages/Project/Budget/Components/AccordionBudget.vue'
const props = defineProps({
    data: Object,
    option: {
        type: String,
        default: 'total'
    },
    level: String
})

const formatCurrency = (valor, moneda) => {
    if (valor == undefined || valor == null) {
        return 'Sin definir'
    } else {
        return parseInt(valor).toLocaleString('es-CO',
            { style: 'currency', currency: moneda == null ? 'COP' : moneda, maximumFractionDigits: 0 })
    }
}

</script>

<template>
    <Accordion v-if="data">
        <AccordionTab v-for="pep in data" :pt="{
        headerAction: '!p-1 !bg-gray-100 hover:!bg-primary-light',
        content: '!p-1 !py-2',
        headerIcon: '!hidden'
    }">
            <template #header>
                <div class="grid sm:grid-cols-4 w-full px-1">
                    <span class="">
                        <p>
                            {{ pep.identification }}
                        </p>
                        <p class="text-xs text-gray-600 italic"> {{ pep.grafo_id }}</p>
                    </span>
                    <span v-if="option == 'material'" class="col-span-3 grid grid-cols-3">
                        <p class="h-full flex items-center justify-end">
                            {{ formatCurrency(pep.materials) }}
                        </p>
                        <p class="h-full flex items-center justify-end">
                            {{ formatCurrency(pep.materials_ejecutados) }}
                        </p>
                        <p class="h-full flex items-center justify-end"
                            :class="(pep.materials - pep.materials_ejecutados) < 0 ? 'text-danger' : ''">
                            {{ formatCurrency((pep.materials - pep.materials_ejecutados)) }}
                        </p>
                    </span>
                    <span v-if="option == 'obra'" class="col-span-3 grid grid-cols-3">
                        <p class="h-full flex items-center justify-end">
                            {{ formatCurrency(pep.labor) }}
                        </p>
                        <p class="h-full flex items-center justify-end">
                            {{ formatCurrency(pep.labor_ejecutados) }}
                        </p>
                        <p class="h-full flex items-center justify-end"
                            :class="(pep.labor - pep.labor_ejecutados) < 0 ? 'text-danger' : ''">
                            {{ formatCurrency((pep.labor - pep.labor_ejecutados)) }}
                        </p>
                    </span>
                    <span v-if="option == 'servicio'" class="col-span-3 grid grid-cols-3">
                        <p class="h-full flex items-center justify-end">
                            {{ formatCurrency(pep.services) }}
                        </p>
                        <p class="h-full flex items-center justify-end">
                            {{ formatCurrency(pep.services_ejecutados) }}
                        </p>
                        <p class="h-full flex items-center justify-end"
                            :class="(pep.services - pep.services_ejecutados) < 0 ? 'text-danger' : ''">
                            {{ formatCurrency((pep.services - pep.services_ejecutados)) }}
                        </p>
                    </span>
                    <span v-if="option == 'total'" class="col-span-3 grid grid-cols-3">
                        <p class="h-full flex items-center justify-end">
                            {{ formatCurrency(parseInt(pep.materials) + parseInt(pep.labor) + parseInt(pep.services)) }}
                        </p>
                        <p class="h-full flex items-center justify-end">
                            {{ formatCurrency(parseInt(pep.materials_ejecutados) + parseInt(pep.labor_ejecutados) +
        parseInt(pep.services_ejecutados)) }}
                        </p>
                        <p class="h-full flex items-center justify-end"
                            :class="((parseInt(pep.materials) + parseInt(pep.labor) + parseInt(pep.services)) -
        (parseInt(pep.materials_ejecutados) + parseInt(pep.labor_ejecutados) + parseInt(pep.services_ejecutados))) < 0 ? 'text-danger' : ''">
                            {{
        formatCurrency(((parseInt(pep.materials) + parseInt(pep.labor) + parseInt(pep.services)) -
            (parseInt(pep.materials_ejecutados) + parseInt(pep.labor_ejecutados) +
                parseInt(pep.services_ejecutados))))
    }}
                        </p>
                    </span>
                </div>
            </template>
            <div class="max-h-[50vh] overflow-y-auto overflow-x-hidden">
                <AccordionBudget v-if="pep.peps" :data="pep.peps" :option="option" />
                <AccordionBudget v-if="pep.grafos" :data="pep.grafos" :option="option" level="grafo" />
                <span v-if="level == 'grafo'">
                    <div v-for="operacion in pep.operaciones" class=" border grid sm:grid-cols-4 w-full p-1">
                        <span class="">
                            <p>
                                {{ operacion.identification }}
                            </p>
                            <p class="text-xs text-gray-600 italic"> {{ operacion.grafo_id }}</p>
                        </span>
                        <span v-if="option == 'material'" class="col-span-3 grid grid-cols-3">
                            <p class="h-full flex items-center justify-end">
                                {{ formatCurrency(operacion.materials) }}
                            </p>
                            <p class="h-full flex items-center justify-end">
                                {{ formatCurrency(operacion.materials_ejecutados) }}
                            </p>
                            <p class="h-full flex items-center justify-end"
                                :class="(parseInt(operacion.materials) - operacion.materials_ejecutados) < 0 ? 'text-danger' : ''">
                                {{ formatCurrency((parseInt(operacion.materials) - operacion.materials_ejecutados)) }}
                            </p>
                        </span>
                        <span v-if="option == 'obra'" class="col-span-3 grid grid-cols-3">
                            <p class="h-full flex items-center justify-end">
                                {{ formatCurrency(operacion.labor) }}
                            </p>
                            <p class="h-full flex items-center justify-end">
                                {{ formatCurrency(operacion.labor_ejecutados) }}
                            </p>
                            <p class="h-full flex items-center justify-end"
                                :class="(parseInt(operacion.labor) - operacion.labor_ejecutados) < 0 ? 'text-danger' : ''">
                                {{ formatCurrency((parseInt(operacion.labor) - operacion.labor_ejecutados)) }}
                            </p>
                        </span>
                        <span v-if="option == 'servicio'" class="col-span-3 grid grid-cols-3">
                            <p class="h-full flex items-center justify-end">
                                {{ formatCurrency(operacion.services) }}
                            </p>
                            <p class="h-full flex items-center justify-end">
                                {{ formatCurrency(operacion.services_ejecutados) }}
                            </p>
                            <p class="h-full flex items-center justify-end"
                                :class="(operacion.services - operacion.services_ejecutados) < 0 ? 'text-danger' : ''">
                                {{ formatCurrency((operacion.services - operacion.services_ejecutados)) }}
                            </p>
                        </span>
                        <span v-if="option == 'total'" class="col-span-3 grid grid-cols-3">
                            <p class="h-full flex items-center justify-end">
                                {{
        formatCurrency(parseInt(operacion.materials) + parseInt(operacion.labor) +
            parseInt(operacion.services))
    }}
                            </p>
                            <p class="h-full flex items-center justify-end">
                                {{
            formatCurrency(operacion.materials_ejecutados + operacion.labor_ejecutados +
                operacion.services_ejecutados)
        }}
                            </p>
                            <p class="h-full flex items-center justify-end"
                                :class="((parseInt(operacion.materials) + parseInt(operacion.labor) + parseInt(operacion.services)) - parseInt(operacion.materials_ejecutados + operacion.labor_ejecutados + operacion.services_ejecutados)) < 0 ? 'text-danger' : ''">
                                {{
        formatCurrency(((parseInt(operacion.materials) + parseInt(operacion.labor) +
                                parseInt(operacion.services)) - parseInt(operacion.materials_ejecutados +
                                operacion.labor_ejecutados + operacion.services_ejecutados)))
                                }}
                            </p>
                        </span>
                    </div>
                </span>
            </div>
        </AccordionTab>
    </Accordion>
</template>