<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { router } from '@inertiajs/vue3';
import Image from 'primevue/image';
import OverlayPanel from 'primevue/overlaypanel';
import { ref } from 'vue';
import { useSweetalert } from '@/composable/sweetAlert'
import Loading from "@/Components/Loading.vue";

const props = defineProps({
    suggestions: Array,
    permission: {
        type: Boolean,
        default: false
    }
})
const op = ref();
const { toast } = useSweetalert();
const suggestionSelect = ref()
const loading = ref(false)
const date = ref(null)
const today = ref(new Date().toISOString().split('T')[0])
const type = ref(null)
const updateSuggestion = (s, n) => {
    router.put(route('suggestion.update', s), { answer: n }, {
        onSuccess: () => {
            toast('¡Respuesta guardada!', 'success');
        }
    })
}

const filter = () => {
    loading.value = true
    router.get(route('suggestion.index'),
        {
            type: type.value,
            date: date.value
        },
        {
            preserveState: true,
            only: ['suggestions'],
            onSuccess: () => {
                loading.value = false
                suggestionSelect.value=null
            }
        }
    )
}

function formatDateTime24h(dateTime) {
    return new Date(dateTime).toLocaleString('es-CO',
        { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', hour12: false })
}

const toggle = (event) => {
    op.value.toggle(event);
}
</script>
<template>
    <AppLayout>
        <div class="flex justify-between h-10 min-w-full p-1 border-b border-gray-200">
                <button :class="type == null && date == null ? 'bg-primary text-white scale-105' : ''"
                    class="w-8 border rounded-md shadow-xl text-primary hover:text-white border-primary alturah8 pi pi-filter-slash hover:bg-primary"
                    @click="type = null; date = null; filter()">
                </button>
                <div class="shadow-xl w-72">
                    <button type="button" @click="type = 'Sugerencia'; filter()"
                        :class="type == 'Sugerencia' ? 'bg-primary text-white scale-105' : ''"
                        class="relative inline-flex items-center justify-center w-1/3 text-sm font-semibold border border-r-0 hover:text-white hover:border-r hover:bg-primary hover:scale-105 text-primary alturah8 rounded-l-md 0 border-primary focus:z-10">
                        Sugerencias</button>
                    <button type="button" @click="type = 'Opinion'; filter()"
                        :class="type == 'Opinion' ? 'bg-primary text-white scale-105' : ''"
                        class="relative inline-flex items-center justify-center w-1/3 text-sm font-semibold border hover:text-white text-primary alturah8 border-primary hover:bg-primary hover:scale-105 focus:z-10">
                        Opinion</button>
                    <button type="button" @click="type = 'Error'; filter()"
                        :class="type == 'Error' ? 'bg-danger text-white scale-105' : ''"
                        class="relative inline-flex items-center justify-center w-1/3 text-sm font-semibold border border-danger hover:text-white text-danger alturah8 rounded-r-md hover:bg-danger hover:scale-105 focus:z-10">
                        Errores</button>
                </div>
                <input :class="date != null ? 'bg-primary text-white scale-105 border-white fill-white' : ''"
                    class="relative inline-flex items-center justify-center text-sm font-semibold border rounded-md shadow-xl text-primary alturah8 border-primary hover:scale-105 focus:z-10"
                    type="date" name="date" id="date" v-model="date" :max="today" @change="filter()">
        </div>
        <div class="flex max-w-full max-h-full min-w-full min-h-full" v-if="!loading">
            <div class="grid grid-cols-3" v-if="props.suggestions.length > 0">
                <div class="p-3 space-y-2 overflow-y-auto custom-scroll">
                    <div v-for="suggestion in props.suggestions"
                        @click="suggestionSelect == suggestion ? suggestionSelect = null : suggestionSelect = suggestion"
                        :class="suggestion.type == 'Error' ? 'border-danger' : 'border-primary', suggestion == suggestionSelect ? 'bg-blue-400' : ''"
                        class="w-full p-2 border rounded-md shadow-lg cursor-pointer hover:scale-105">
                        <div class="grid grid-cols-10">
                            <div class="flex items-center justify-center h-full">
                                <svg class="fill-yellow-400" v-if="suggestion.type == 'Sugerencia'" height="2em"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 384 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                    <path
                                        d="M297.2 248.9C311.6 228.3 320 203.2 320 176c0-70.7-57.3-128-128-128S64 105.3 64 176c0 27.2 8.4 52.3 22.8 72.9c3.7 5.3 8.1 11.3 12.8 17.7l0 0c12.9 17.7 28.3 38.9 39.8 59.8c10.4 19 15.7 38.8 18.3 57.5H109c-2.2-12-5.9-23.7-11.8-34.5c-9.9-18-22.2-34.9-34.5-51.8l0 0 0 0c-5.2-7.1-10.4-14.2-15.4-21.4C27.6 247.9 16 213.3 16 176C16 78.8 94.8 0 192 0s176 78.8 176 176c0 37.3-11.6 71.9-31.4 100.3c-5 7.2-10.2 14.3-15.4 21.4l0 0 0 0c-12.3 16.8-24.6 33.7-34.5 51.8c-5.9 10.8-9.6 22.5-11.8 34.5H226.4c2.6-18.7 7.9-38.6 18.3-57.5c11.5-20.9 26.9-42.1 39.8-59.8l0 0 0 0 0 0c4.7-6.4 9-12.4 12.7-17.7zM192 128c-26.5 0-48 21.5-48 48c0 8.8-7.2 16-16 16s-16-7.2-16-16c0-44.2 35.8-80 80-80c8.8 0 16 7.2 16 16s-7.2 16-16 16zm0 384c-44.2 0-80-35.8-80-80V416H272v16c0 44.2-35.8 80-80 80z" />
                                </svg>
                                <svg class="fill-primary" v-if="suggestion.type == 'Opinion'" height="2em"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 576 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                    <path
                                        d="M287.9 0c9.2 0 17.6 5.2 21.6 13.5l68.6 141.3 153.2 22.6c9 1.3 16.5 7.6 19.3 16.3s.5 18.1-5.9 24.5L433.6 328.4l26.2 155.6c1.5 9-2.2 18.1-9.6 23.5s-17.3 6-25.3 1.7l-137-73.2L151 509.1c-8.1 4.3-17.9 3.7-25.3-1.7s-11.2-14.5-9.7-23.5l26.2-155.6L31.1 218.2c-6.5-6.4-8.7-15.9-5.9-24.5s10.3-14.9 19.3-16.3l153.2-22.6L266.3 13.5C270.4 5.2 278.7 0 287.9 0zm0 79L235.4 187.2c-3.5 7.1-10.2 12.1-18.1 13.3L99 217.9 184.9 303c5.5 5.5 8.1 13.3 6.8 21L171.4 443.7l105.2-56.2c7.1-3.8 15.6-3.8 22.6 0l105.2 56.2L384.2 324.1c-1.3-7.7 1.2-15.5 6.8-21l85.9-85.1L358.6 200.5c-7.8-1.2-14.6-6.1-18.1-13.3L287.9 79z" />
                                </svg>
                                <svg class="fill-danger" v-if="suggestion.type == 'Error'"
                                    xmlns="http://www.w3.org/2000/svg" height="2em"
                                    viewBox="0 0 512 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                    <path
                                        d="M256 0c53 0 96 43 96 96v3.6c0 15.7-12.7 28.4-28.4 28.4H188.4c-15.7 0-28.4-12.7-28.4-28.4V96c0-53 43-96 96-96zM41.4 105.4c12.5-12.5 32.8-12.5 45.3 0l64 64c.7 .7 1.3 1.4 1.9 2.1c14.2-7.3 30.4-11.4 47.5-11.4H312c17.1 0 33.2 4.1 47.5 11.4c.6-.7 1.2-1.4 1.9-2.1l64-64c12.5-12.5 32.8-12.5 45.3 0s12.5 32.8 0 45.3l-64 64c-.7 .7-1.4 1.3-2.1 1.9c6.2 12 10.1 25.3 11.1 39.5H480c17.7 0 32 14.3 32 32s-14.3 32-32 32H416c0 24.6-5.5 47.8-15.4 68.6c2.2 1.3 4.2 2.9 6 4.8l64 64c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0l-63.1-63.1c-24.5 21.8-55.8 36.2-90.3 39.6V240c0-8.8-7.2-16-16-16s-16 7.2-16 16V479.2c-34.5-3.4-65.8-17.8-90.3-39.6L86.6 502.6c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3l64-64c1.9-1.9 3.9-3.4 6-4.8C101.5 367.8 96 344.6 96 320H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H96.3c1.1-14.1 5-27.5 11.1-39.5c-.7-.6-1.4-1.2-2.1-1.9l-64-64c-12.5-12.5-12.5-32.8 0-45.3z" />
                                </svg>
                            </div>
                            <div class="flex flex-col justify-between col-span-9 pl-2">
                                <p class="text-xs font-bold">{{ suggestion.details }}</p>
                                <div class="flex pt-1 mt-1 border-t border-gray-300 ">
                                    <span v-if="suggestion.type == 'Error'"
                                        class="flex justify-center px-2 text-xs font-medium rounded-md"
                                        :class="suggestion.status == 1 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-success'">{{
                                            suggestion.status == 1 ? 'Pendiente' : 'Resuelto'
                                        }}</span>
                                    <p class="w-full text-xs text-end">{{
                                        formatDateTime24h(suggestion.created_at) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="h-full col-span-2 px-2 overflow-y-auto custom-scroll " v-if="suggestionSelect">
                    <span class="flex justify-center w-full p-2 text-lg font-bold text-primary">Detalles del reporte</span>
                    <p class="w-full h-auto p-2 border border-blue-100 rounded-md">{{ suggestionSelect.details }}</p>
                    <span class="flex justify-center w-full p-2 text-lg text-primary">Datos tecnicos</span>
                    <div class="grid grid-cols-2 gap-2 ">
                        <div class="grid grid-cols-4 gap-1 border border-red-500">
                            <span class="flex items-center col-span-1 text-primary">URL:</span>
                            <p class="flex items-center max-w-full col-span-3 break-all">{{ suggestionSelect.urlAddress }}</p>
                            <span class="flex items-center col-span-1 text-primary">Tipo:</span>
                            <p class="flex items-center col-span-3">{{ suggestionSelect.type }}</p>
                            <span class="flex items-center col-span-1 text-primary">Usuario:</span>
                            <div class="flex justify-between col-span-3">
                                <p class="flex items-center">{{ suggestionSelect.user.short_name }}</p>
                                <button @click="toggle" class="flex items-center justify-center w-8 fill-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="100%"
                                        viewBox="0 0 576 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                        <path
                                            d="M112 48a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm40 304V480c0 17.7-14.3 32-32 32s-32-14.3-32-32V256.9L59.4 304.5c-9.1 15.1-28.8 20-43.9 10.9s-20-28.8-10.9-43.9l58.3-97c17.4-28.9 48.6-46.6 82.3-46.6h29.7c33.7 0 64.9 17.7 82.3 46.6l44.9 74.7c-16.1 17.6-28.6 38.5-36.6 61.5c-1.9-1.8-3.5-3.9-4.9-6.3L232 256.9V480c0 17.7-14.3 32-32 32s-32-14.3-32-32V352H152zM432 224a144 144 0 1 1 0 288 144 144 0 1 1 0-288zm0 240a24 24 0 1 0 0-48 24 24 0 1 0 0 48zM368 321.6V328c0 8.8 7.2 16 16 16s16-7.2 16-16v-6.4c0-5.3 4.3-9.6 9.6-9.6h40.5c7.7 0 13.9 6.2 13.9 13.9c0 5.2-2.9 9.9-7.4 12.3l-32 16.8c-5.3 2.8-8.6 8.2-8.6 14.2V384c0 8.8 7.2 16 16 16s16-7.2 16-16v-5.1l23.5-12.3c15.1-7.9 24.5-23.6 24.5-40.6c0-25.4-20.6-45.9-45.9-45.9H409.6c-23 0-41.6 18.6-41.6 41.6z" />
                                    </svg>
                                </button>
                                <OverlayPanel class="w-96" ref="op">
                                    <div class="grid justify-center grid-cols-5 gap-2 text-xs">
                                        <div class="flex-col items-center justify-center h-full col-span-4 align-middle">
                                            <div class="grid w-full grid-cols-4">
                                                <span class="flex items-center col-span-1 text-primary">Nombre:</span>
                                                <p class="flex items-center col-span-3 ">
                                                    {{ suggestionSelect.user.name }}
                                                </p>
                                                <span class="flex items-center col-span-1 text-primary">Cargo:</span>
                                                <p class="flex items-center col-span-3">
                                                    {{ suggestionSelect.user.cargo }}
                                                </p>
                                                <span class="flex items-center col-span-1 text-primary">Oficina:</span>
                                                <p class="flex items-center col-span-3">
                                                    {{ suggestionSelect.user.oficina }}
                                                </p>
                                                <span class="flex items-center col-span-1 text-primary">Gerencia:</span>
                                                <p class="flex items-center col-span-3">
                                                    {{ suggestionSelect.user.gerencia }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="grid items-center grid-cols-1">
                                            <Image :src="suggestionSelect.user.photo" alt="Image" preview class=""
                                                imageClass="rounded-md border border-gray-200 shadow-lg" />
                                        </div>
                                    </div>
                                </OverlayPanel>
                            </div>
                            <span class="flex items-center col-span-1 text-primary">Fecha:</span>
                            <p class="flex items-center col-span-3">{{ formatDateTime24h(suggestionSelect.created_at) }}</p>
                            <span class="flex items-center col-span-1 text-primary" v-if="suggestionSelect.type == 'Error'">Estado:</span>
                            <span v-if="suggestionSelect.type == 'Error'"
                                class="flex items-center justify-center col-span-3 font-medium rounded-md "
                                :class="suggestionSelect.status == 1 ? 'bg-red-100 text-danger' : 'bg-green-100 text-success'">
                                {{ suggestionSelect.status == 1 ? 'Pendiente' : 'Resuelto' }}
                            </span>
                        </div>
                        <div>
                            <Image :src="suggestionSelect.capture" alt="Image" preview class=""
                                imageClass="rounded-md border border-gray-200 shadow-lg" />
                        </div>
                    </div>
                    <span class="flex justify-center w-full p-2 text-lg text-primary">Notas del desarrollador</span>
                    <textarea v-model="suggestionSelect.answer" rows="4"
                        class="w-full rounded-md custom-scroll border-primary focus:border-primary" />
                    <div class="flex justify-between">
                        <div class="flex space-x-2">
                            <span class="flex items-center text-primary">Fecha ultima respuesta:</span>
                            <p class="flex items-center">{{ suggestionSelect.created_at == suggestionSelect.updated_at ?
                                'Sin respuesta' : formatDateTime24h(suggestionSelect.updated_at) }}</p>
                        </div>
                        <button @click="updateSuggestion(suggestionSelect.id, suggestionSelect.answer)"
                            v-if="props.permission" v-tooltip="'¿Solucionado?'"
                            class="items-center justify-center p-1 space-x-2 border rounded-md shadow-lg hover:scale-105 hover:text-white hover:bg-primary hover:fill-white border-primary fill-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" height="2em"
                                viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                <path
                                    d="M64 32C28.7 32 0 60.7 0 96V416c0 35.3 28.7 64 64 64H384c35.3 0 64-28.7 64-64V173.3c0-17-6.7-33.3-18.7-45.3L352 50.7C340 38.7 323.7 32 306.7 32H64zm0 96c0-17.7 14.3-32 32-32H288c17.7 0 32 14.3 32 32v64c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V128zM224 288a64 64 0 1 1 0 128 64 64 0 1 1 0-128z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="flex flex-col items-center justify-center w-full h-full col-span-2 px-2 " v-else>
                    <Loading message="Seleccione un reporte"/>
                </div>
            </div>
            <div v-else class="flex flex-col items-center justify-center w-full h-full pt-10">
                <Loading message="No hay reportes que mostrar"/>
            </div>
        </div>
        <div v-else class="flex flex-col items-center justify-center px-2 pt-10">
            <Loading message="Cargando reportes"/>
        </div>
    </AppLayout>
</template>

