<template>
    <div class="w-full">
        <!-- <div class="flex items-center  px-10">
            <div class="flex-auto mt-4">
                <h1 class="text-xl capitalize font-semibold leading-6 text-primary">{{getPlurar(title)}}</h1>
            </div>

            <div class="ml-16 " v-show="add">
                <Button type="button" @click="addItem()" severity="success">
                    <PlusIcon class="h-6 w-6 mr-2" aria-hidden="true" />
                    Nuevo
                </Button>
            </div>
        </div> -->
        <div class="flow-root">
            <div class="">
                  <dataTableCustomized :add="props.add" :edit-item="props.edit" :delete="props.delete" :url="props.url" height="300px" :headers="headers" :title="title" :loading="loading"></dataTableCustomized>
            </div>
        </div>
    </div>

    <!-- modal -->
    <TransitionRoot as="template" :show="open">
        <Dialog as="div" class="relative z-30" @close="open = false">
            <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                <div class="fixed h-screen w-screen inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-30" />
            </TransitionChild>

            <div class="fixed inset-0 z-50 overflow-y-auto h-screen">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200" leave-from="opacity-100 translate-y-0 sm:scale-100"
                        leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                        <DialogPanel :class="props.heigthDialog" class="relative transform overflow-hidden rounded-lg bg-white px-2 pb-4 pt-2 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg ">
                            <div>
                                <div class="text-center mt-2 px-2">
                                    <DialogTitle as="h3" class="text-xl font-semibold text-primary ">Crear {{title}}</DialogTitle>
                                    <div class="mt-2 space-y-4 border border-gray-200 p-2 rounded-lg">
                                        <template v-for="header of headers">
                                            <TextInput  class="mt-2 text-left"
                                            v-if="header.input !== false && header.type == 'text'"
                                            :label="header.header"
                                            :error="router.page.props.errors[header.field]"
                                            v-model="form.object[header.field]"></TextInput>

                                            <div v-if="header.input !== false && header.type == 'multiple'">
                                                <label for="password" class="block capitalize text-left text-sm text-gray-900">{{header.field}}</label>
                                                <multiselect
                                                v-model="form.object[header.field]"
                                                class="custom-multiselect"
                                                :multiple="true"
                                                :searchable="true"
                                                label="name"
                                                track-by="id"
                                                :close-on-select="false"

                                                :placeholder="'Seleccionar '+ header.header"
                                                :options="header.options">
                                                </multiselect>
                                            </div>

                                        </template>

                                    </div>
                                </div>
                            </div>
                            <div class="mt-2 flex space-x-4 px-2">

                                <Button class="hover:bg-danger text-danger border-danger" severity="danger" @click="open = false" >Cancelar</Button>
                                <Button severity="success" :loading="loading" class="text-success hover:bg-success" @click="submit">Guardar</Button>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script setup>
import Button from './Button.vue';
import { PlusIcon,PencilIcon,TrashIcon } from '@heroicons/vue/24/outline'
import { router, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import TextInput from './TextInput.vue';
import Multiselect from '@suadelabs/vue3-multiselect'
import {useSweetalert} from '@/composable/sweetAlert'
import '@suadelabs/vue3-multiselect/dist/vue3-multiselect.css';
import plural from 'pluralize-es'
import axios from 'axios';
import dataTableCustomized from '@/Components/DataTableCustom.vue'
const {toast} = useSweetalert();

const {confirmDelete} = useSweetalert();

const elementos = ref(props.elements);
const props = defineProps({
    headers: {
        type: Array,
        required: true
    },
    elements: {
        type: Array,
        default: []
    },
    add: {
        type: Boolean,
        default: false
    },
    edit: {
        type: Boolean,
        default: false
    },
    delete: {
        type: Boolean,
        default: false
    },
    url: {
        type: String,
        required: false
    },
    heigthDialog: {
        type: String,
        default: ''
    },
    title: String,
})
const open = ref(false)
const loading = ref(false)

const selected = ref()

const form = useForm({
    object: {}
})

//Function

/* Funcion Incial de componente*/
onMounted(() => {
    getElements()
})

/* Funcion para AGREGAR Elementos al modelo*/
const submit = () => {
    loading.value = true;
    router.post(route(props.url+'.store'), form.object,{
        onSuccess: (res) => {
            open.value = false;
            toast(props.title + ' Creado Exitosamente', 'success');
            getElements()
        },
        onError: (errors) => {
            toast('Ha surgido un error', 'error');
        },
        onFinish: visit => {
            loading.value = false;
        }
    })
}

/* Funcion para ELIMINAR Elementos al modelo*/


/* Funcion para abril el modal con los campos vacios*/
const addItem = () => {
    resetValues();
    open.value = true;
}

/* Limpiar todos los campo*/
const resetValues = () => {
    for (let header of props.headers) {
        form.object[header.field] = header.type === 'multiple' ? []:'';
        router.page.props.errors[header.field] = null
    }
}

/* Obtener los elementos del modelo */
const getElements = () => {
    loading.value = true
    if(props.elements.length == 0 && props.url){
        axios.get(route(props.url+'.index')).then((res) => {
            elementos.value = res.data[0];
            loading.value = false;
        })
    }
}

/* Poner en Plurar un srt*/
const getPlurar = (str) =>{
    return plural(str);
}

</script>
