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
import { useSweetalert } from '@/composable/sweetAlert'
import axios from 'axios';
import Swal from 'sweetalert2';
import { router } from '@inertiajs/vue3';
import OverlayPanel from 'primevue/overlaypanel';
import FileUpload from 'primevue/fileupload';
const props = defineProps({
    estimadores: Object,
    customers: Array,
    typeships: Array,
    quote:Object
})
const errors = ref({
    name: false,
    expeted_answer_date: false,
    customer_id: false
})
const { toast } = useSweetalert();
const newQuote = ref(true)
const quote = ref({})
const minDate = new Date();
const oferta = ['ROM', 'FINAL']

const quoteData = ref()
if (props.quote){
    newQuote.value=false
    quoteData.value=props.quote
}

const quoteSave = () => {
    Swal.fire({
        title: '¿Desea guardar la solicitud de estimacion?',
        icon: 'warning',
        showDenyButton: true,
        confirmButtonText: 'Guardar',
        denyButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            axios.post(route('quotes.store', quote.value)).then((res) => {
                console.log(res)
                if (res.data.status) {
                    Swal.fire({
                        title: 'La estimacion: ' + quote.value.name + ' se ha creado exitosamente ¿Desea agregar datos a las clases?',
                        icon: 'success',
                        showDenyButton: true,
                        confirmButtonText: 'Sí',
                        denyButtonText: 'No'
                    }).then((result2) => {
                        if (result2.isConfirmed) {
                            newQuote.value = false
                            quoteData.value = res.data.quote
                        } else if (result2.isDenied) {
                            router.get(route('quotes.index'))
                        }
                    })
                }
            }).catch((e) => {
                console.log(e)
                errors.value = e.response.data.errors
                toast('Hay errores en los datos a guardar', 'error')
            })
        }
    })
}



const op = ref();
const toggle = (event) => {
    op.value.toggle(event);
}
</script>

<template>
    <AppLayout>
        <div class="overflow-y-scroll space-y-2">
            <span>
                <span class="flex justify-between p-1">
                    <p class="text-primary font-bold text-xl">
                        {{
                            newQuote ? 'Crear solicitud de estimacion' : 'Editar estimacion #' +
                            quoteData.quote.created_at.slice(0, 4) + '-' + quoteData.quote.consecutive + '-V' +
                        quoteData.version
                        }}
                    </p>
                    <Button v-if="!newQuote" label="Ver sugerencia" icon="fa-solid fa-file-circle-question" class="!h-8"
                        @click="toggle" />
                </span>

                <div class="grid grid-cols-2 gap-1 border p-1 rounded-md">
                    <div class="grid grid-cols-2 gap-1">
                        <span class="col-span-2">
                            <p>Nombre</p>
                            <InputText v-model="quote.name" filter optionLabel="name"
                                :class="errors.name ? 'p-invalid' : ''" placeholder="Escriba el nombre de la estimacion"
                                class="w-full md:w-14rem !h-8" />
                            <small v-if="errors.name" class="p-error" id="text-error">La estimacion debe tener un
                                nombre</small>
                        </span>
                        <span class="col-span-2">
                            <p>Clase(s) de buque</p>
                            <MultiSelect v-model="quote.type_ships" display="chip" filter optionValue="id"
                                :options="typeships" optionLabel="name" placeholder="Selecciona la(s) clase(s) de buque"
                                class="w-full md:w-20rem !h-8" :pt="{
                                    label: '!p-0 !px-2 !flex !items-center !h-8',
                                    token: '!p-0 !px-1',
                                    tokenLabel: '!text-sm'
                                }" />
                        </span>
                        <span class="">
                            <p>Cliente</p>
                            <Dropdown v-model="quote.customer_id" :options="customers" filter optionLabel="name"
                                optionValue="id" placeholder="Selecciona un cliente" class="w-full md:w-14rem !h-8"
                                showClear :pt="{
                                    input: '!p-0 !pt-1 !px-1 '
                                }" />
                        </span>
                        <span class="" v-if="newQuote">
                            <p>Estimador</p>
                            <Dropdown v-model="quote.estimador_id" :options="Object.values(estimadores)" filter
                                optionLabel="name" optionValue="user_id" placeholder="Selecciona un estimador"
                                :class="errors.estimador_id ? 'p-invalid' : ''" class="w-full md:w-14rem !h-8 " showClear
                                :pt="{
                                    input: '!p-0 !pt-1 !px-1 '
                                }" />
                            <small v-if="errors.estimador_id" class="p-error" id="text-error">Debe seleccionar un
                                estimador</small>
                        </span>
                        <span class="" v-if="newQuote">
                            <p>Fecha estimada de respuesta</p>
                            <Calendar v-model="quote.expeted_answer_date" dateFormat="dd/mm/yy" showIcon :minDate="minDate"
                                class="!h-8 w-full" placeholder="Fecha de respuesta"
                                :class="errors.expeted_answer_date ? 'p-invalid' : ''" :pt="{
                                    input: '!p-0 !pt-1 !px-1'
                                }" />
                            <small v-if="errors.expeted_answer_date" class="p-error" id="text-error">Debe seleccionar una
                                fecha</small>
                        </span>
                        <span class="">
                            <p>Tipo de oferta</p>
                            <span class="flex space-x-2">
                                <Dropdown v-model="quote.offer_type" :options="oferta"
                                    placeholder="Selecciona tipo de oferta" class="w-full md:w-14rem !h-8" showClear :pt="{
                                        input: '!p-0 !pt-1 !px-1'
                                    }" />
                                <span v-if="!newQuote" class="w-full justify-end flex">
                                    <Button severity="success" @click="quoteSave()" icon="fa-solid fa-floppy-disk"
                                        label="Guardar cambios" class="!h-8"></Button>
                                </span>
                            </span>
                        </span>
                    </div>
                    <span class="space-y-2 w">
                        <Editor v-model="quote.observation" editorStyle="height: 210px" v-if="newQuote"
                            placeholder="Escriba aqui las sugerencias que seran enviadas al estimador">
                        </Editor>
                        <span v-if="!newQuote" class="flex flex-col h-full justify-between">
                            <div class="space-y-2">
                                <span class="">
                                    <p>Ruta</p>
                                    <InputText for="ruta" v-model="quote.name" filter optionLabel="name"
                                        placeholder="Escriba la direccion" class="w-full md:w-14rem !h-8" />
                                </span>
                                <span class="">
                                    <p class="mt-1">Archivo a subir</p>
                                    <FileUpload mode="basic" accept="application/pdf"  class="!h-8 !min-w-[200px]" />
                                </span>

                            </div>
                            <span class="flex justify-end gap-2">
                                <Button severity="success" icon="fa-solid fa-floppy-disk" label="Guardar"
                                    class="!h-8"></Button>
                                <Button severity="primary" icon="fa-solid fa-paper-plane" label="Enviar a revision"
                                    class="!h-8"></Button>
                            </span>

                        </span>
                        <span v-if="newQuote" class="w-full justify-end flex">
                            <Button severity="success" @click="quoteSave()" icon="fa-solid fa-floppy-disk"
                                label="Guardar solicitud" class="!h-8"></Button>
                        </span>
                    </span>
                </div>
            </span>
            <span class="" v-if="quoteData">
                <TabView class="tabview-custom" :scrollable="true">
                    <TabPanel v-for="buque, index in quoteData.quote_type_ships">
                        <template #header>
                            <div class="flex items-center space-x-1">
                                <Avatar :image="buque.render ? buque.render : '/images/generic-boat.png'" shape="circle" />
                                <span class="flex-col flex">
                                    <span class="font-bold white-space-nowrap">{{ buque.name }}</span>
                                    <span class="text-xs">$999.999.999</span>
                                </span>
                            </div>
                        </template>
                        <div class="grid grid-cols-4 gap-2 ">
                            <span class="">
                                <p for="username">Alcance</p>
                                <InputText v-model="buque.scope" filter optionLabel="name" placeholder="Alcance"
                                    class="w-full md:w-14rem !h-8" showClear />
                            </span>
                            <span class="">
                                <p for="username">Tipo de proyecto</p>
                                <InputText v-model="buque.project_type" filter optionLabel="name"
                                    placeholder="Tipo de proyecto" class="w-full md:w-14rem !h-8" showClear />
                            </span>
                            <span class="">
                                <p for="username">Madurez</p>
                                <InputText v-model="buque.maturity" filter optionLabel="name" placeholder="Madurez"
                                    class="w-full md:w-14rem !h-8" showClear />
                            </span>
                            <span class="">
                                <p for="username">Unidades</p>
                                <InputText v-model="buque.units" filter optionLabel="name" placeholder="Unidades a producir"
                                    class="w-full md:w-14rem !h-8" showClear />
                            </span>
                            <span class="">
                                <p for="username">Moneda</p>
                                <InputText v-model="buque.coin" filter optionLabel="name" placeholder="Tipo de moneda"
                                    class="w-full md:w-14rem !h-8" showClear />
                            </span>
                            <span class="">
                                <p for="username">Precio compra USD</p>
                                <InputText v-model="buque.rate_buy_usd" filter optionLabel="name"
                                    placeholder="Precio de compra en Dolares" class="w-full md:w-14rem !h-8" showClear />
                            </span>
                            <span class="">
                                <p for="username">Precio de compra EUR</p>
                                <InputText v-model="buque.rate_buy_eur" filter optionLabel="name"
                                    placeholder="Precio de compra en Euros" class="w-full md:w-14rem !h-8" showClear />
                            </span>
                            <span class="">
                                <p for="username">Precio antes de IVA</p>
                                <InputText v-model="buque.price_before_iva_original" filter optionLabel="name"
                                    placeholder="Precio antes de IVA" class="w-full md:w-14rem !h-8" showClear />
                            </span>
                            <span class="">
                                <p for="username">IVA</p>
                                <InputText v-model="buque.iva" filter optionLabel="name" placeholder="IVA"
                                    class="w-full md:w-14rem !h-8" showClear />
                            </span>
                            <span class="">
                                <p for="username">Margen</p>
                                <InputText v-model="buque.margin" filter optionLabel="name" placeholder="Margen estimado"
                                    class="w-full md:w-14rem !h-8" showClear />
                            </span>
                            <span class="">
                                <p for="username">Tipo de documento tecnico</p>
                                <InputText v-model="buque.white_paper" filter optionLabel="name" placeholder="Tipo de DT"
                                    class="w-full md:w-14rem !h-8" showClear />
                            </span>
                            <span class="">
                                <p for="username">Numero de documento tecnico</p>
                                <InputText v-model="buque.no_white_paper" optionLabel="name" placeholder="Numero DT"
                                    class="w-full md:w-14rem !h-8" showClear />
                            </span>
                        </div>
                    </TabPanel>
                </TabView>
            </span>
        </div>
    </AppLayout>

    <OverlayPanel ref="op" class="w-96">
        <Editor v-model="quote.observation" editorStyle="height: 210px" :pt="{
            toolbar: '!hidden',
            content: '!border-0',
            root: 'border p-1 !rounded-md'
        }">
        </Editor>
    </OverlayPanel>
</template>
