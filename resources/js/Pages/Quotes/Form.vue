<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Calendar from 'primevue/calendar';
import Dropdown from 'primevue/dropdown';
import InputText from 'primevue/inputtext';
import MultiSelect from 'primevue/multiselect';
import Editor from 'primevue/editor';
import { ref } from 'vue';
import Button from 'primevue/button';
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';
import Avatar from 'primevue/avatar';

const props = defineProps({
    estimadores: Object,
    customers: Array,
    typeships: Array
})

const quote = ref({})
const minDate = new Date();
const oferta = ['ROM', 'FINAL']

const quoteShip = ref([{}],[{}])

</script>

<template>
    <AppLayout>
        <div class="overflow-y-scroll space-y-2">
            <p class="text-primary font-bold text-xl">Crear solicitud de estimacion</p>
            <div class="grid grid-cols-2 gap-1 border p-1 rounded-md">
                <div class="grid grid-cols-2 gap-1">
                    <span class="col-span-2">
                        <p for="username">Nombre</p>
                        <InputText v-model="quote.name" filter optionLabel="name"
                            placeholder="Escriba el nombre de la estimacion" class="w-full md:w-14rem !h-8" showClear />
                    </span>
                    <span class="col-span-2">
                        <p for="username">Clase(s) de buque</p>
                        <MultiSelect v-model="quote.typeShips" display="chip" filter :options="typeships" optionLabel="name"
                            placeholder="Selecciona la(s) clase(s) de buque" class="w-full md:w-20rem !h-8" :pt="{
                                label: '!p-0 !px-2 !flex !items-center !h-8',
                                token: '!p-0 !px-1',
                                tokenLabel: '!text-sm'
                            }" />
                    </span>
                    <span class="">
                        <p for="username">Cliente</p>
                        <Dropdown v-model="quote.customer" :options="customers" filter optionLabel="name"
                            placeholder="Selecciona un cliente" class="w-full md:w-14rem !h-8" showClear :pt="{
                                input: '!p-0 !pt-1 !px-1 '
                            }" />
                    </span>
                    <span class="">
                        <p for="username">Estimador</p>
                        <Dropdown v-model="quote.estimador" :options="Object.values(estimadores)" filter optionLabel="name"
                            placeholder="Selecciona un estimador" class="w-full md:w-14rem !h-8 " showClear :pt="{
                                input: '!p-0 !pt-1 !px-1 '
                            }" />
                    </span>
                    <span class="">
                        <p for="username">Fecha estimada de respuesta</p>
                        <Calendar v-model="quote.fechaEstimada" showIcon :minDate="minDate" class="!h-8 w-full"
                            placeholder="Fecha de respuesta" :pt="{
                                input: '!p-0 !pt-1 !px-1'
                            }" />
                    </span>
                    <span class="">
                        <p for="username">Tipo de oferta</p>
                        <span class="flex space-x-2">
                            <Dropdown v-model="quote.type" :options="oferta" placeholder="Selecciona tipo de oferta"
                                class="w-full md:w-14rem !h-8" showClear :pt="{
                                    input: '!p-0 !pt-1 !px-1'
                                }" />
                        </span>
                    </span>
                </div>
                <span class="space-y-2">
                    <Editor v-model="quote.notes" editorStyle="height: 210px"
                        placeholder="Escriba aqui las sugerencias que seran enviadas al estimador">
                    </Editor>
                    <span class="w-full justify-end flex">
                        <Button severity="success" icon="fa-solid fa-floppy-disk" label="Guardar solicitud"
                            class="!h-8"></Button>
                    </span>
                </span>
            </div>
            <span class="grid grid-cols-4 gap-3">
                <TabView class="tabview-custom col-span-3 border rounded-md" :scrollable="true">
                    <TabPanel v-for="buque, index in quote.typeShips">
                        <template #header>
                            <div class="flex items-center space-x-1">
                                <Avatar :image="buque.render ? buque.render : '/images/generic-boat.png'" shape="circle" />
                                <span class="flex-col flex">
                                    <span class="font-bold white-space-nowrap">{{ buque.name }}</span>
                                    <span class="text-xs">$999.999.999</span>
                                </span>
                            </div>
                        </template>
                        <div class="m-0 border rounded-md grid grid-cols-4 gap-2 ">
                            <span class="">
                                <p for="username">Alcance</p>
                                <InputText v-model="quoteShip[index].scope" filter optionLabel="name"
                                    placeholder="Escriba el nombre de la estimacion" class="w-full md:w-14rem !h-8"
                                    showClear />
                            </span>
                            <span class="">
                                <p for="username">Tipo de proyecto</p>
                                <InputText v-model="quoteShip[index].project_type" filter optionLabel="name"
                                    placeholder="Escriba el nombre de la estimacion" class="w-full md:w-14rem !h-8"
                                    showClear />
                            </span>
                            <span class="">
                                <p for="username">Madurez</p>
                                <InputText v-model="quoteShip[index].maturity" filter optionLabel="name"
                                    placeholder="Escriba el nombre de la estimacion" class="w-full md:w-14rem !h-8"
                                    showClear />
                            </span>
                            <span class="">
                                <p for="username">Unidades</p>
                                <InputText v-model="quoteShip[index].units" filter optionLabel="name"
                                    placeholder="Escriba el nombre de la estimacion" class="w-full md:w-14rem !h-8"
                                    showClear />
                            </span>
                            <span class="">
                                <p for="username">Moneda</p>
                                <InputText v-model="quoteShip[index].coin" filter optionLabel="name"
                                    placeholder="Escriba el nombre de la estimacion" class="w-full md:w-14rem !h-8"
                                    showClear />
                            </span>
                            <span class="">
                                <p for="username">Precio compra USD</p>
                                <InputText v-model="quoteShip[index].rate_buy_usd" filter optionLabel="name"
                                    placeholder="Escriba el nombre de la estimacion" class="w-full md:w-14rem !h-8"
                                    showClear />
                            </span>
                            <span class="">
                                <p for="username">Precio de compra EUR</p>
                                <InputText v-model="quoteShip[index].rate_buy_eur" filter optionLabel="name"
                                    placeholder="Escriba el nombre de la estimacion" class="w-full md:w-14rem !h-8"
                                    showClear />
                            </span>
                            <span class="">
                                <p for="username">Precio antes de IVA</p>
                                <InputText v-model="quoteShip[index].price_before_iva_original" filter optionLabel="name"
                                    placeholder="Escriba el nombre de la estimacion" class="w-full md:w-14rem !h-8"
                                    showClear />
                            </span>
                            <span class="">
                                <p for="username">IVA</p>
                                <InputText v-model="quoteShip[index].iva" filter optionLabel="name"
                                    placeholder="Escriba el nombre de la estimacion" class="w-full md:w-14rem !h-8"
                                    showClear />
                            </span>
                            <span class="">
                                <p for="username">Margen</p>
                                <InputText v-model="quoteShip[index].margin" filter optionLabel="name"
                                    placeholder="Escriba el nombre de la estimacion" class="w-full md:w-14rem !h-8"
                                    showClear />
                            </span>
                            <span class="">
                                <p for="username">Tipo de documento tecnico</p>
                                <InputText v-model="quoteShip[index].white_paper" filter optionLabel="name"
                                    placeholder="Escriba el nombre de la estimacion" class="w-full md:w-14rem !h-8"
                                    showClear />
                            </span>
                            <span class="">
                                <p for="username">Numero de documento tecnico</p>
                                <InputText v-model="quoteShip[index].no_white_paper" optionLabel="name"
                                    placeholder="Escriba el nombre de la estimacion" class="w-full md:w-14rem !h-8"
                                    showClear />
                            </span>
                        </div>
                    </TabPanel>
                </TabView>
                <span class="flex flex-col items-center justify-center space-y-2">
                    <div class="space-y-2 pt-5">
                        <span class="p-float-label">
                            <InputText for="ruta" v-model="quote.name" filter optionLabel="name"
                                placeholder="Escriba la direccion" class="w-full md:w-14rem !h-8" />
                            <label for="ruta">Ruta</label>
                        </span>
                        <span class="p-float-label">
                            <InputText v-model="quote.name" filter optionLabel="name"
                                placeholder="Escriba el nombre de la estimacion" class="w-full md:w-14rem !h-8" showClear />
                            <label for="username">Archivo</label>
                        </span>
                    </div>
                    <div class=" flex flex-col space-y-2">
                        <Button severity="success" icon="fa-solid fa-floppy-disk" label="Guardar" class="!h-8"></Button>
                        <Button severity="primary" icon="fa-solid fa-paper-plane" label="Enviar a revision"
                            class="!h-8"></Button>
                    </div>

                </span>
            </span>
        </div>
    </AppLayout>
</template>
