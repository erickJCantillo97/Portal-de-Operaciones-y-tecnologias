<script setup>
const { currencyFormat } = useCommonUtilities()
import { useCommonUtilities } from '@/composable/useCommonUtilities';
import Accordion from 'primevue/accordion';
import AccordionBudget from '@/Pages/Project/Budget/Components/AccordionBudget.vue'
import AccordionTab from 'primevue/accordiontab';

const props = defineProps({
    data: Object,
    option: {
        type: String,
        default: 'total'
    },
    level: String
})
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
                            {{ currencyFormat(pep.materials) }}
                        </p>
                        <p class="h-full flex items-center justify-end">
                            {{ currencyFormat(pep.materials_ejecutados) }}
                        </p>
                        <p class="h-full flex items-center justify-end"
                            :class="(pep.materials - pep.materials_ejecutados) < 0 ? 'text-danger' : ''">
                            {{ currencyFormat((pep.materials - pep.materials_ejecutados)) }}
                        </p>
                    </span>
                    <span v-if="option == 'obra'" class="col-span-3 grid grid-cols-3">
                        <p class="h-full flex items-center justify-end">
                            {{ currencyFormat(pep.labor) }}
                        </p>
                        <p class="h-full flex items-center justify-end">
                            {{ currencyFormat(pep.labor_ejecutados) }}
                        </p>
                        <p class="h-full flex items-center justify-end"
                            :class="(pep.labor - pep.labor_ejecutados) < 0 ? 'text-danger' : ''">
                            {{ currencyFormat((pep.labor - pep.labor_ejecutados)) }}
                        </p>
                    </span>
                    <span v-if="option == 'servicio'" class="col-span-3 grid grid-cols-3">
                        <p class="h-full flex items-center justify-end">
                            {{ currencyFormat(pep.services) }}
                        </p>
                        <p class="h-full flex items-center justify-end">
                            {{ currencyFormat(pep.services_ejecutados) }}
                        </p>
                        <p class="h-full flex items-center justify-end"
                            :class="(pep.services - pep.services_ejecutados) < 0 ? 'text-danger' : ''">
                            {{ currencyFormat((pep.services - pep.services_ejecutados)) }}
                        </p>
                    </span>
                    <span v-if="option == 'total'" class="col-span-3 grid grid-cols-3">
                        <p class="h-full flex items-center justify-end">
                            {{ currencyFormat(parseInt(pep.materials) + parseInt(pep.labor) + parseInt(pep.services)) }}
                        </p>
                        <p class="h-full flex items-center justify-end">
                            {{ currencyFormat(parseInt(pep.materials_ejecutados) + parseInt(pep.labor_ejecutados) +
        parseInt(pep.services_ejecutados)) }}
                        </p>
                        <p class="h-full flex items-center justify-end"
                            :class="((parseInt(pep.materials) + parseInt(pep.labor) + parseInt(pep.services)) -
        (parseInt(pep.materials_ejecutados) + parseInt(pep.labor_ejecutados) + parseInt(pep.services_ejecutados))) < 0 ? 'text-danger' : ''">
                            {{
        currencyFormat(((parseInt(pep.materials) + parseInt(pep.labor) + parseInt(pep.services)) -
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
                                {{ currencyFormat(operacion.materials) }}
                            </p>
                            <p class="h-full flex items-center justify-end">
                                {{ currencyFormat(operacion.materials_ejecutados) }}
                            </p>
                            <p class="h-full flex items-center justify-end"
                                :class="(parseInt(operacion.materials) - operacion.materials_ejecutados) < 0 ? 'text-danger' : ''">
                                {{ currencyFormat((parseInt(operacion.materials) - operacion.materials_ejecutados)) }}
                            </p>
                        </span>
                        <span v-if="option == 'obra'" class="col-span-3 grid grid-cols-3">
                            <p class="h-full flex items-center justify-end">
                                {{ currencyFormat(operacion.labor) }}
                            </p>
                            <p class="h-full flex items-center justify-end">
                                {{ currencyFormat(operacion.labor_ejecutados) }}
                            </p>
                            <p class="h-full flex items-center justify-end"
                                :class="(parseInt(operacion.labor) - operacion.labor_ejecutados) < 0 ? 'text-danger' : ''">
                                {{ currencyFormat((parseInt(operacion.labor) - operacion.labor_ejecutados)) }}
                            </p>
                        </span>
                        <span v-if="option == 'servicio'" class="col-span-3 grid grid-cols-3">
                            <p class="h-full flex items-center justify-end">
                                {{ currencyFormat(operacion.services) }}
                            </p>
                            <p class="h-full flex items-center justify-end">
                                {{ currencyFormat(operacion.services_ejecutados) }}
                            </p>
                            <p class="h-full flex items-center justify-end"
                                :class="(operacion.services - operacion.services_ejecutados) < 0 ? 'text-danger' : ''">
                                {{ currencyFormat((operacion.services - operacion.services_ejecutados)) }}
                            </p>
                        </span>
                        <span v-if="option == 'total'" class="col-span-3 grid grid-cols-3">
                            <p class="h-full flex items-center justify-end">
                                {{
        currencyFormat(parseInt(operacion.materials) + parseInt(operacion.labor) +
            parseInt(operacion.services))
    }}
                            </p>
                            <p class="h-full flex items-center justify-end">
                                {{
            currencyFormat(parseInt(operacion.materials_ejecutados) +
                parseInt(operacion.labor_ejecutados) +
                parseInt(operacion.services_ejecutados))
        }}
                            </p>
                            <p class="h-full flex items-center justify-end"
                                :class="((parseInt(operacion.materials) + parseInt(operacion.labor) + parseInt(operacion.services)) - parseInt(operacion.materials_ejecutados + operacion.labor_ejecutados + operacion.services_ejecutados)) < 0 ? 'text-danger' : ''">
                                {{
        currencyFormat(((parseInt(operacion.materials) + parseInt(operacion.labor) +
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
