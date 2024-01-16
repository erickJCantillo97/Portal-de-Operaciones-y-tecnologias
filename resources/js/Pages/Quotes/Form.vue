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
    quote: Object,
    action: Number,
})
const errors = ref({
    name: false,
    expeted_answer_date: false,
    customer_id: false
})
const { toast } = useSweetalert();
const newQuote = ref(true)
const dataQuoteNew = ref({})
const minDate = new Date();
const oferta = ['ROM', 'FINAL']
const iva = ['5%', '16%', '19%', 'Exento', 'Excluido']
const moneda = ['USD', 'COP', 'EUR']
const alcance = ['ADQUISICIÓN Y ENTREGA', 'CO DESARROLL DISEÑO Y CONSTRUCCIÓN', 'CO PRODUCCIÓN', 'CONSTRUCCIÓN', 'DISEÑO BUQUE', 'DISEÑO Y CONSTRUCCIÓN', 'SERVICIOS INDUSTRIALES']
const madurez = ['CONCEPTUAL', 'PRELIMINAR', 'CONTRACTUAL', 'PORTAFOLIO']
const docTecnico = ['PENDIENTE', 'HT', 'ET', 'PTB', 'DG', 'AT']
const modEdit = ref(false)
const loadingButton = ref(false)
const quoteShips = ref({})

const editActive = () => {
    modEdit.value = true
}
const editInactive = () => {
    modEdit.value = false
    dataQuoteNew.expeted_answer_date = props.quote.expeted_answer_date
    dataQuoteNew.value.name = props.quote.quote.name
    dataQuoteNew.value.estimador_id = props.quote.estimador_id
    dataQuoteNew.value.type_ships = props.quote.quote_type_ships.map((ship => parseInt(ship.type_ship_id)))
    dataQuoteNew.value.observation = props.quote.observation
    dataQuoteNew.value.customer_id = parseInt(props.quote.customer_id)
    dataQuoteNew.value.offer_type = props.quote.offer_type
    dataQuoteNew.value.route = props.quote.route
    dataQuoteNew.value.coin = props.quote.coin
}

if (props.quote) {
    dataQuoteNew.value.expeted_answer_date = props.quote.expeted_answer_date
    dataQuoteNew.value.name = props.quote.quote.name
    dataQuoteNew.value.estimador_id = props.quote.estimador_id
    dataQuoteNew.value.type_ships = props.quote.quote_type_ships.map((ship => parseInt(ship.type_ship_id)))
    dataQuoteNew.value.observation = props.quote.observation
    dataQuoteNew.value.customer_id = parseInt(props.quote.customer_id)
    dataQuoteNew.value.offer_type = props.quote.offer_type
    dataQuoteNew.value.route = props.quote.route
    dataQuoteNew.value.coin = props.quote.coin
    quoteShips.value = props.quote.quote_type_ships
    if (props.action == null) {
        newQuote.value = false
    } else {

    }
}

const quoteSave = () => {
    loadingButton.value = true
    Swal.fire({
        title: '¿Desea guardar la solicitud de estimacion?',
        icon: 'warning',
        showDenyButton: true,
        confirmButtonText: 'Guardar',
        denyButtonText: 'Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            await axios.post(route('quotes.store', dataQuoteNew.value)).then((res) => {
                if (res.data.status) {
                    Swal.fire({
                        title: 'La estimacion: ' + dataQuoteNew.value.name + ' se ha creado exitosamente ¿Desea agregar datos a las clases?',
                        icon: 'success',
                        showDenyButton: true,
                        denyButtonText: 'No'
                    }).then((result2) => {
                        if (result2.isConfirmed) {
                            console.log(res.data.quote)
                            router.get(route('quotesversion.edit', res.data.quote.id))
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
        loadingButton.value = false
    })

}

const quoteNewVersion = () => {
    loadingButton.value = true
    Swal.fire({
        title: '¿Desea generar nueva version para la estimacion #' + props.quote.quote.consecutive,
        icon: 'warning',
        showDenyButton: true,
        confirmButtonText: 'Guardar',
        denyButtonText: 'Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            await axios.post(route('quotesversion.store', props.quote.id),
                dataQuoteNew.value).then((res) => {
                    if (res.data.status) {
                        Swal.fire({
                            title: 'La estimacion: ' + dataQuoteNew.value.name + ' se ha creado exitosamente ¿Desea agregar datos a las clases?',
                            icon: 'success',
                            showDenyButton: true,
                            confirmButtonText: 'Sí',
                            denyButtonText: 'No'
                        }).then((result2) => {
                            if (result2.isConfirmed) {
                                console.log(res.data.quote)
                                router.get(route('quotesversion.edit', res.data.quote.id))
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
        loadingButton.value = false
    })

}

const quoteUpdate = () => {
    loadingButton.value = true
    Swal.fire({
        title: '¿Desea actualizar la estimacion?',
        text: 'Los cambios no guardados en las clases de buque se perderan',
        icon: 'warning',
        showDenyButton: true,
        confirmButtonText: 'Guardar',
        denyButtonText: 'Cancelar'
    }).then(async (result) => {
        await axios.put(route('quotesversion.update', props.quote.id), dataQuoteNew.value).then((res) => {
            quoteShips.value = res.data.quote.quote_type_ships
            toast('Estimacion actualizada correctamente', 'success')
            modEdit.value = false
        }).catch((e) => {
            console.log(e)
            errors.value = e.response.data.errors
            toast('Hay errores en los datos a guardar', 'error')
        })
        loadingButton.value = false
    })
}

const quoteShipsSave = (revision) => {
    loadingButton.value = true
    Swal.fire({
        title: '¿Desea actualizar los datos de las clases en la estimacion?',
        icon: 'warning',
        showDenyButton: true,
        confirmButtonText: 'Guardar',
        denyButtonText: 'Cancelar'
    }).then(async (result) => {
        await axios.post(route('quote.version.type_ship.update', props.quote.id), { type_ships: quoteShips.value, revision }).then((res) => {
            quoteShips.value = res.data.quote.quote_type_ships
            toast('Datos de las clases actualizados correctamente', 'success')
            modEdit.value = false
        }).catch((e) => {
            console.log(e)
            errors.value = e.response.data.errors
            toast('Hay errores en los datos a guardar', 'error')
        })
        loadingButton.value = false
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
                            newQuote ? action == 2 ? 'Creando nueva version para estimacion #' + props.quote.quote.consecutive :
                            'Crear solicitud de estimacion' : 'Editar estimacion #' +
                        props.quote.quote.consecutive
                        }}
                    </p>
                    <Button v-if="!newQuote" label="Ver sugerencia" icon="fa-solid fa-file-circle-question" class="!h-8"
                        @click="toggle" />
                </span>

                <div :class="newQuote ? 'grid grid-cols-2 ' : ''" class="gap-1 border p-1 rounded-md">
                    <div class="grid gap-1" :class="newQuote ? 'grid-cols-2' : 'grid-cols-4'">
                        <span class="col-span-2">
                            <p>Nombre</p>
                            <InputText v-model="dataQuoteNew.name" :disabled="newQuote ? false : !modEdit"
                                :class="errors.name ? 'p-invalid' : ''" placeholder="Escriba el nombre de la estimacion"
                                class="w-full md:w-14rem !h-8" />
                            <small v-if="errors.name" class="p-error" id="text-error">La estimacion debe tener un
                                nombre</small>
                        </span>
                        <span class="col-span-2">
                            <p>Clase(s) de buque</p>
                            <MultiSelect v-model="dataQuoteNew.type_ships" display="chip" filter optionValue="id"
                                :disabled="newQuote ? false : !modEdit" :options="typeships" optionLabel="name"
                                placeholder="Selecciona la(s) clase(s) de buque" class="w-full md:w-20rem !h-8" :pt="{
                                    label: '!p-0 !px-2 !flex !items-center !h-8',
                                    token: '!p-0 !px-1',
                                    tokenLabel: '!text-sm'
                                }" />
                        </span>
                        <span class="">
                            <p>Cliente</p>
                            <Dropdown v-model="dataQuoteNew.customer_id" :options="customers" filter optionLabel="name"
                                optionValue="id" placeholder="Selecciona un cliente" class="w-full md:w-14rem !h-8"
                                showClear :disabled="newQuote ? false : !modEdit" :pt="{
                                    input: '!p-0 !pt-1 !px-1 '
                                }" />
                        </span>
                        <span class="" v-if="newQuote">
                            <p>Estimador</p>
                            <Dropdown v-model="dataQuoteNew.estimador_id" :options="Object.values(estimadores)" filter
                                optionLabel="name" optionValue="user_id" placeholder="Selecciona un estimador"
                                :class="errors.estimador_id ? 'p-invalid' : ''" class="w-full md:w-14rem !h-8 " showClear
                                :pt="{
                                    input: '!p-0 !pt-1 !px-1 '
                                }" />
                            <small v-if="errors.estimador_id" class="p-error" id="text-error">
                                Debe seleccionar un estimador
                            </small>
                        </span>
                        <div class="col-span-2 gap-2 w-full"
                            :class="newQuote ? 'grid grid-cols-3 col-span-2' : 'col-span-3 grid grid-cols-3'">
                            <span class="" v-if="newQuote">
                                <p>Fecha estimada</p>
                                <Calendar v-model="dataQuoteNew.expeted_answer_date" dateFormat="dd/mm/yy" showIcon
                                    :minDate="minDate" class="!h-8 w-full" placeholder="Fecha de respuesta"
                                    :class="errors.expeted_answer_date ? 'p-invalid' : ''" :pt="{
                                        input: '!p-0 !pt-1 !px-1'
                                    }" />
                                <small v-if="errors.expeted_answer_date" class="p-error" id="text-error">
                                    Debe seleccionar una fecha
                                </small>
                            </span>
                            <span class="">
                                <p>Tipo de oferta</p>
                                <span class="flex space-x-2">
                                    <Dropdown v-model="dataQuoteNew.offer_type" :options="oferta"
                                        :disabled="newQuote ? false : !modEdit" placeholder="Selecciona tipo de oferta"
                                        class="w-full md:w-14rem !h-8" :pt="{
                                            input: '!p-0 !pt-1 !px-1'
                                        }" />
                                </span>
                            </span>
                            <span :class="newQuote ? '' : 'flex col-span-2 items-end'">
                                <span>
                                    <p for="username">Moneda</p>
                                    <Dropdown v-model="dataQuoteNew.coin" :options="moneda"
                                        :disabled="newQuote ? false : !modEdit" placeholder="Selecciona la moneda"
                                        class="w-full md:w-14rem !h-8" :pt="{
                                            input: '!p-0 !pt-1 !px-1 '
                                        }" />
                                </span>
                                <span v-if="!newQuote" class="w-full justify-end flex gap-2">
                                    <Button title="Editar estimacion" label="Activar edicion" severity="danger"
                                        v-if="!modEdit" @click="editActive" icon="fa-solid fa-pencil" class="!h-8" />
                                    <Button title="Cancelar cambios" label="Cancelar" severity="danger"
                                        :disabled="loadingButton" v-if="modEdit" @click="editInactive"
                                        icon="fa-solid fa-xmark" class="!h-8" />
                                    <Button title="Guardar cambios" label="Guardar" severity="success" v-if="modEdit"
                                        :loading="loadingButton" @click="quoteUpdate" icon="fa-solid fa-floppy-disk"
                                        class="!h-8" />
                                </span>
                            </span>
                        </div>
                    </div>
                    <span class="space-y-2">
                        <Editor v-model="dataQuoteNew.observation" editorStyle="height: 210px" v-if="newQuote"
                            placeholder="Escriba aqui las sugerencias que seran enviadas al estimador" :pt="{
                                link: { class: '!hidden' },
                                image: { class: '!hidden' },
                                codeBlock: { class: '!hidden' },
                                toolbar: { class: '!bg-gray-100' }
                            }">
                        </Editor>
                        <span v-if="newQuote" class="w-full justify-end flex">
                            <Button severity="success" @click="action == 2 ? quoteNewVersion() : quoteSave()"
                                :loading="loadingButton" icon="fa-solid fa-floppy-disk"
                                :label="action == 2 ? 'Generar nueva version' : 'Guardar solicitud'" class="!h-8" />
                        </span>
                    </span>
                </div>
            </span>
            <div class="" v-if="quoteShips && !modEdit && action != 2">
                <div class=" border rounded-md p-1">
                    <TabView class="tabview-custom" :scrollable="true">
                        <TabPanel v-for=" buque, index  in  quoteShips ">
                            <template #header>
                                <div class="flex items-center space-x-1">
                                    <Avatar :image="buque.render ? buque.render : '/images/generic-boat.png'"
                                        shape="circle" />
                                    <span class="flex-col flex">
                                        <span class="font-bold white-space-nowrap">{{ buque.name }}</span>
                                        <span class="text-xs">$ {{ buque?.price_before_iva_original ?? 0 }}</span>
                                    </span>
                                </div>
                            </template>
                            <div class="grid grid-cols-4 gap-2 ">
                                <span class="">
                                    <p for="username">Alcance</p>
                                    <Dropdown v-model="buque.scope" :options="alcance" placeholder="Selecciona el alcance"
                                        class="w-full md:w-14rem !h-8" showClear :pt="{
                                            input: '!p-0 !pt-1 !px-1 '
                                        }
                                            " />
                                </span>
                                <span class="">
                                    <p for="username">Madurez</p>
                                    <Dropdown v-model="buque.maturity" :options="madurez"
                                        placeholder="Selecciona la madurez" class="w-full md:w-14rem !h-8" showClear :pt="{
                                            input: '!p-0 !pt-1 !px-1 '
                                        }
                                            " />
                                </span>
                                <span class="">
                                    <p for="username">Unidades</p>
                                    <InputText v-model="buque.units" filter optionLabel="name"
                                        placeholder="Unidades a producir" class="w-full md:w-14rem !h-8" showClear />
                                </span>
                                <span class="">
                                    <p for="username">Precio compra USD</p>
                                    <InputText v-model="buque.rate_buy_usd" filter optionLabel="name"
                                        placeholder="Precio de compra en Dolares" class="w-full md:w-14rem !h-8"
                                        showClear />
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
                                    <Dropdown v-model="buque.iva" :options="iva" placeholder="Selecciona el IVA"
                                        class="w-full md:w-14rem !h-8" showClear :pt="{
                                            input: '!p-0 !pt-1 !px-1 '
                                        }
                                            " />
                                </span>
                                <span class="">
                                    <p for="username">Margen</p>
                                    <InputText v-model="buque.margin" filter optionLabel="name"
                                        placeholder="Margen estimado" class="w-full md:w-14rem !h-8" showClear />
                                </span>
                                <span class="">
                                    <p for="username">Tipo de documento tecnico</p>
                                    <Dropdown v-model="buque.white_paper" :options="docTecnico"
                                        placeholder="Selecciona el tipo de DT" class="w-full md:w-14rem !h-8" showClear :pt="{
                                            input: '!p-0 !pt-1 !px-1 '
                                        }
                                            " />

                                </span>
                                <span class="">
                                    <p for="username">Numero de documento tecnico</p>
                                    <InputText v-model="buque.no_white_paper" optionLabel="name" placeholder="Numero DT"
                                        class="w-full md:w-14rem !h-8" showClear />
                                </span>
                            </div>
                        </TabPanel>
                    </TabView>
                </div>
                <span v-if="!newQuote" class="px-3 py-2 flex h-full justify-between">
                    <div class="gap-2 w-1/2 flex">
                        <span class="w-full">
                            <p>Ruta</p>
                            <InputText for="ruta" v-model="dataQuoteNew.route" filter optionLabel="name"
                                placeholder="Escriba o pegue la ruta a la carpeta de los documentos"
                                class="w-full md:w-20rem !h-8" />
                        </span>
                        <span class="">
                            <p class="">Archivo a subir</p>
                            <FileUpload mode="basic" accept="application/pdf" class="!h-8 !w-min !min-w-[200px]" />
                        </span>
                    </div>
                    <span class="flex items-end gap-2" v-if="!modEdit">
                        <Button severity="success" @click="quoteShipsSave(false)" icon="fa-solid fa-floppy-disk"
                            :loading="loadingButton" label="Guardar" class="!h-8"></Button>
                        <Button severity="primary" @click="quoteShipsSave(true)" icon="fa-solid fa-paper-plane"
                            :loading="loadingButton" label="Enviar a revision" class="!h-8"></Button>
                    </span>

                </span>
            </div>
        </div>
    </AppLayout>

    <OverlayPanel ref="op" class="w-96">
        <Editor v-model="dataQuoteNew.observation" editorStyle="height: 210px" :pt="{
            toolbar: '!hidden',
            content: '!border-0',
            root: 'border p-1 !rounded-md'
        }
            ">
        </Editor>
    </OverlayPanel>
</template>
