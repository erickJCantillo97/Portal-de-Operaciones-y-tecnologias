<script setup>
const { currencyFormat } = useCommonUtilities()
const { hasRole, hasPermission } = usePermissions()
const { toast } = useSweetalert()
import { useCommonUtilities } from '@/composable/useCommonUtilities';
import { Dialog, DialogPanel, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { Link } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import { usePermissions } from '@/composable/permission';
import { useSweetalert } from '@/composable/sweetAlert'
import { XMarkIcon } from '@heroicons/vue/24/outline'
import Accordion from 'primevue/accordion'
import AccordionTab from 'primevue/accordiontab'
import Button from 'primevue/button'
import Calendar from 'primevue/calendar'
import CustomModal from '@/Components/CustomModal.vue'
import Dropdown from 'primevue/dropdown'
import Feed from '@/Components/Feed.vue'
import FeedWithComments from '@/Components/FeedWithComments.vue'
import Moment from 'moment'
import Swal from 'sweetalert2'

const emit = defineEmits(['closeSlideOver'])

onMounted(() => {
    // getVersions()
})

const checked = ref(false)
const dateSelected = ref()
const openStatusDialog = ref(false)
const openCommentsDialog = ref(false)
const showDateResponse = ref(true)
const statusSelected = ref()
const statusOptions = ref(
    [
        { id: 0, name: 'Proceso' },
        { id: 1, name: 'Entregada' },
        { id: 2, name: 'Pendiente por Firma' },
        { id: 3, name: 'Firmada' },
        { id: 4, name: 'No Firmada' },
        { id: 5, name: 'Contratada' }
    ])

const icons = [
    'fa-solid fa-user-clock', //0 -> Proceso
    'fa-solid fa-thumbs-up', //1 -> Entregada
    'fa-solid fa-calendar-days', //2 -> Pendiente por Firma
    'fa-solid fa-check', //3 -> Firmada
    'fa-solid fa-xmark', //4 -> No Firmada
    'fa-solid fa-dollar-sign', //5 -> Contratada
]

const colors = [
    'orange', //0 -> Proceso
    'purple', //1 -> Entregada
    'yellow', //2 -> Pendiente por Firma
    'blue', //3 -> Firmada
    'red', //4 -> No Firmada
    'emerald', //5 -> Contratada
]

const props = defineProps(
    {
        show: {
            default: false
        },
        quote: {
            type: Object,
            required: false
        }
    }
)

const openStatusModal = () => {
    openStatusDialog.value = true
}

const closeStatusDialog = () => {
    openStatusDialog.value = false
}

const openCommentsModal = () => {
    openCommentsDialog.value = true
}

const closeCommentsModal = () => {
    openCommentsDialog.value = false
}

const key = ref(0)
const saveStatus = async () => {
    try {
        await axios.post(route('quotestatus.store', {
            status: statusSelected.value,
            quote_version_id: props.quote.version_id,
            fecha: dateSelected.value
        })).then(res => {
            key.value++;
        })
    } catch (error) {
        console.log(error)
    }
}

//#region API's
const getVersions = () => {
    try {
        axios.get(route('get.quotes.versions')
            .then(res => {
                console.log('Hello ' + res.data)
            }))
    } catch (error) {
        console.log(error)
    }
}

const deleteQuoteVersion = () => {
    try {
        Swal.fire({
            title: `¿Está seguro de eliminar la estimación \n ${props.quote.name} ${props.quote.consecutive}?`,
            icon: 'warning',
            showDenyButton: true,
            confirmButtonText: 'Eliminar',
            denyButtonText: 'Cancelar'
        }).then((response) => {
            if (response.isConfirmed) {
                router.delete(route('quotesversion.destroy', props.quote.version_id), {
                    onSuccess: () => {
                        toast(`Se ha eliminado la estimación \n ${props.quote.name} ${props.quote.consecutive} satisfactoriamente`, 'success')
                        emit('closeSlideOver')
                    },
                    onError: (error) => {
                        toast(`Ha ocurrido un error al eliminar la versión \n ${props.quote.name} ${props.quote.consecutive}`, 'error')
                        emit('closeSlideOver')
                    }
                }).then((res) => {
                })
            }
        })
    } catch (error) {
        console.log(error)
    }
}
//#endregion
</script>
<template>
    <TransitionRoot as="template" :show="show">
        <Dialog as="div" class="relative z-10" @close="$emit('closeSlideOver')">
            <TransitionChild as="template" enter="ease-in-out duration-500" enter-from="opacity-0" enter-to="opacity-100"
                leave="ease-in-out duration-500" leave-from="opacity-100" leave-to="opacity-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
            </TransitionChild>
            <div class="fixed inset-0 overflow-hidden">
                <div class="absolute inset-0 overflow-hidden">
                    <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                        <TransitionChild as="template" enter="transform transition ease-in-out duration-500 sm:duration-700"
                            enter-from="translate-x-full" enter-to="translate-x-0"
                            leave="transform transition ease-in-out duration-500 sm:duration-700" leave-from="translate-x-0"
                            leave-to="translate-x-full">
                            <DialogPanel class="pointer-events-auto relative w-96">
                                <TransitionChild as="template" enter="ease-in-out duration-500" enter-from="opacity-0"
                                    enter-to="opacity-100" leave="ease-in-out duration-500" leave-from="opacity-100"
                                    leave-to="opacity-0">
                                    <div class="absolute left-0 top-0 -ml-8 flex pr-2 pt-4 sm:-ml-10 sm:pr-4">
                                        <button type="button"
                                            class="relative rounded-md text-gray-300 hover:text-white focus:outline-none focus:ring-2 focus:ring-white"
                                            @click="$emit('closeSlideOver')">
                                            <span class="absolute -inset-2.5" />
                                            <span class="sr-only">Close panel</span>
                                            <XMarkIcon class="size-6" aria-hidden="true" />
                                        </button>
                                    </div>
                                </TransitionChild>
                                <div class="h-full overflow-y-auto bg-white p-2">
                                    <div class="absolute w-96 -m-2 z-50 py-4 bg-blue-900 text-white uppercase p-2">
                                        <h2 class="text-lg text-center font-bold text-white">
                                            {{ quote.name }} {{ quote.consecutive }}
                                        </h2>
                                    </div>
                                    <header class="w-full mt-20">
                                        <div class="flex flex-nowrap text-center justify-center items-center"
                                            v-if="quote.version != 1">
                                            <ul class=" text-md text-center    w-10 cursor-pointer"
                                                v-for="version in parseInt(quote.version)"
                                                :class="quote.version == version ? 'border-b border-black font-extrabold text-blue-900' : 'hover:border-b text-blue-700'">
                                                <li>{{ version }}</li>
                                            </ul>
                                        </div>
                                        <div class="flex flex-nowrap space-x-2 p-2 justify-center rounded-lg">
                                            <Button @click="openStatusModal()" size="small" label="Estados"
                                                icon="pi pi-list" />
                                            <Button @click="openCommentsModal()" severity="help" size="small"
                                                label="Comentarios" icon="pi pi-comments" />
                                        </div>
                                        <div class="border border-solid rounded-lg p-2 mb-2">
                                            <dl class="divide-y divide-gray-200 border-b border-t border-gray-200">
                                                <div class="flex justify-between py-3 text-sm font-medium">
                                                    <dt class="text-gray-500">Estimador:</dt>
                                                    <dd class="text-gray-900 uppercase">{{ quote.estimador }}</dd>
                                                </div>
                                                <div class="flex justify-between py-3 text-sm font-medium">
                                                    <dt class="text-gray-500">Cliente:</dt>
                                                    <dd class="text-gray-900">{{ quote.customer }}</dd>
                                                </div>
                                                <div class="flex justify-between py-3 text-sm font-medium">
                                                    <dt class="text-gray-500">Tipo de Oferta:</dt>
                                                    <dd class="text-gray-900">{{ quote.offer_type }}</dd>
                                                </div>
                                                <div class="flex justify-between py-3 text-sm font-medium">
                                                    <dt class="text-gray-500">Fecha de Solicitud:</dt>
                                                    <dd class="text-gray-900">{{ Moment(quote.created_at).format('DD/MM/YY')
                                                    }}</dd>
                                                </div>
                                                <div class="flex justify-between py-3 text-sm font-medium">
                                                    <dt class="text-gray-500">Fecha Respuesta Esperada:</dt>
                                                    <dd class="text-gray-900">{{
                                                        Moment(quote.expected_answer_date).format('DD/MM/YY') }}</dd>
                                                </div>
                                                <div v-if="showDateResponse"
                                                    class="flex justify-between py-3 text-sm font-medium">
                                                    <dt class="text-gray-500">Fecha de Respuesta:</dt>
                                                    <dd class="text-gray-900">{{ Moment().add(6, 'days').format('DD/MM/YY')
                                                    }}</dd>
                                                </div>
                                                <div v-if="showDateResponse"
                                                    class="flex justify-between py-3 text-sm font-medium">
                                                    <dt class="text-gray-500">Precio antes de Iva:</dt>
                                                    <dd class="text-gray-900">{{ currencyFormat(quote.total_cost) }}</dd>
                                                </div>
                                                <div class="flex justify-between py-3 text-sm font-medium">
                                                    <dt class="text-gray-500">Estado:</dt>
                                                    <dd class="text-gray-900">
                                                        <span
                                                            :class="['bg-' + colors[quote.status] + '-500', 'inline-flex items-center rounded-md px-2 py-1 text-xs font-medium text-white ring-1 ring-inset ring-green-600/20']">
                                                            <i :class="icons[quote.status]" class="mr-2"
                                                                aria-hidden="true" />
                                                            {{ quote.get_status }}
                                                        </span>
                                                    </dd>
                                                </div>
                                            </dl>
                                        </div>
                                    </header>
                                    <Accordion>
                                        <AccordionTab v-for="product in quote.products" :pt="{
                                            root: '!border-0 !ring-0',
                                            headerAction: '!bg-slate-200 !px-4 !py-1 mb-1',
                                            headerTitle: '!text- sm'
                                        }">
                                            <template #header>
                                                <div class="block gap-2 w-full">
                                                    <p class="white-space-nowrap font-semibold">{{ product.name }}</p>
                                                    <p class=" white-space-nowrap text-xs">{{
                                                        currencyFormat(product.price_before_iva_original)
                                                    }}</p>

                                                </div>
                                            </template>
                                            <div class="space-y-6">
                                                <div>
                                                    <h3 class="font-medium text-gray-900">Información del producto</h3>
                                                    <dl
                                                        class="mt-2 divide-y divide-gray-200 border-b border-t border-gray-200">
                                                        <div class="flex justify-between py-3 text-sm font-medium">
                                                            <dt class="text-gray-500">Clase:</dt>
                                                            <dd class="text-gray-900">{{ product.name }}</dd>
                                                        </div>
                                                        <div class="flex justify-between py-3 text-sm font-medium">
                                                            <dt class="text-gray-500">Alcance:</dt>
                                                            <dd class="text-gray-900">{{ product.scope == null ? 'N/A' :
                                                                product.scope }}</dd>
                                                        </div>
                                                        <div class="flex justify-between py-3 text-sm font-medium">
                                                            <dt class="text-gray-500">Madurez:</dt>
                                                            <dd class="text-gray-900">{{ product.maturity == null ? 'N/A' :
                                                                product.maturity }}</dd>
                                                        </div>
                                                        <div class="flex justify-between py-3 text-sm font-medium">
                                                            <dt class="text-gray-500">Cantidad:</dt>
                                                            <dd class="text-gray-900">{{ product.units == null ? 'N/A' :
                                                                product.units }}</dd>
                                                        </div>
                                                    </dl>
                                                </div>
                                                <div>
                                                    <h3 class="font-medium text-gray-900">Información de la Oferta</h3>
                                                    <dl
                                                        class="mt-2 divide-y divide-gray-200 border-b border-t border-gray-200">
                                                        <div class="flex justify-between py-3 text-sm font-medium">
                                                            <dt class="text-gray-500">Margen Estimado:</dt>
                                                            <dd class="text-gray-900">{{ product.margin }}%</dd>
                                                        </div>
                                                        <div class="flex justify-between py-3 text-sm font-medium">
                                                            <dt class="text-gray-500">Precio Original:</dt>
                                                            <dd class="text-gray-900">{{
                                                                currencyFormat(product.price_before_iva_original) }}</dd>
                                                        </div>
                                                        <div class="flex justify-between py-3 text-sm font-medium">
                                                            <dt class="text-gray-500">Tasa de Venta:</dt>
                                                            <dd class="text-gray-900">0</dd>
                                                        </div>
                                                        <div class="flex justify-between py-3 text-sm font-medium">
                                                            <dt class="text-gray-500">IVA:</dt>
                                                            <dd class="text-gray-900">{{ product.iva }}</dd>
                                                        </div>
                                                    </dl>
                                                </div>
                                            </div>
                                        </AccordionTab>
                                    </Accordion>
                                    <section class="border border-gray-200 p-4">
                                        <div class="mb-4">
                                            <h3 class="font-semibold text-gray-900 text-center">Documentos</h3>
                                        </div>
                                        <section class="grid grid-cols-2 space-x-2 text-center">
                                            <div class="col-span-1 space-y-2 items-center text-center">
                                                <!--Botón Clonar-->
                                                <Button size="small" label="Clonar" :pt="{
                                                    root: '!w-full !bg-emerald-600 !hover:bg-emerald-500',
                                                    label: '!text-center',
                                                }
                                                    " v-if="hasPermission('quote create')" />

                                                <!--Botón Editar-->
                                                <Link :href="route('quotesversion.edit', props.quote.version_id)"
                                                    class="flex">
                                                <Button size="small" label="Editar" :pt="{
                                                    root: '!w-full !bg-warning !hover:bg-orange-500',
                                                    label: '!text-center',
                                                }
                                                    " v-if="hasPermission('quote edit')" />
                                                </Link>
                                            </div>

                                            <!--Botón Actualizar-->
                                            <div class="col-span-1 space-y-2 items-center">
                                                <Link :href="route('quotesversion.updating', props.quote.version_id)"
                                                    class="flex">
                                                <Button size="small" label="Actualizar" :pt="{
                                                    root: '!w-full !bg-primary !hover:bg-blue-500',
                                                    label: '!text-center',
                                                }
                                                    " v-if="hasPermission('quote edit')" />
                                                </Link>

                                                <!--Botón Eliminar-->
                                                <Button @click="(deleteQuoteVersion())" size="small" label="Eliminar" :pt="{
                                                    root: '!w-full !bg-danger !hover:bg-red-500',
                                                    label: '!text-center',
                                                }
                                                    " v-if="hasPermission('quote delete')" />
                                            </div>
                                        </section>
                                    </section>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>

    <!--MODAL DE ASIGNACIÓN DE ESTADOS-->
    <CustomModal v-model:visible="openStatusDialog" :closable="true" :footer="false" width="50rem">
        <template #icon>
            <span class="text-white material-symbols-outlined text-3xl">
                manage_history
            </span>
        </template>
        <template #titulo>
            <span class="text-xl font-bold text-white white-space-nowrap">Cambiar Estado</span>
        </template>
        <template #body>
            <div class="flex flex-nowrap p-4 space-x-2 justify-center items-center align-items-center">
                <div>
                    <label for="dd-city">Estado de la Estimación</label>
                    <Dropdown v-model="statusSelected" option-label="name" option-value="id" :options="statusOptions"
                        showClear placeholder="Seleccione Estado de la Estimación" class="w-full md:w-14rem" :pt="{
                            filterContainer: '!h-12',
                        }" />
                </div>
                <div>
                    <label for="dd-city">Seleccione Fecha</label>
                    <Calendar v-model="dateSelected" showIcon :manualInput="true" placeholder="Fecha de Estado" />
                </div>
                <div class="mt-8">
                    <Button @click="saveStatus()" label="Guardar" icon="pi pi-save" severity="success" size="small"
                        v-if="hasPermission('quote create')" />
                </div>
            </div>
            <div>
                <Feed :quoteId="quote.version_id" :key="key" />
            </div>
        </template>
    </CustomModal>

    <!--MODAL DE COMENTARIOS-->
    <CustomModal v-model:visible="openCommentsDialog" :closable="true" :footer="false" width="40rem">
        <template #icon>
            <span class="text-white material-symbols-outlined text-3xl">
                chat
            </span>
        </template>
        <template #titulo>
            <span class="text-xl font-bold text-white white-space-nowrap">Comentarios</span>
        </template>
        <template #body>
            <FeedWithComments :quoteId="quote.version_id" />
        </template>
    </CustomModal>
</template>
