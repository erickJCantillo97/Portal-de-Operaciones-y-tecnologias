<template>
    <div class="px-auto shadow-md py-4 rounded-b-xl w-full">
        <div class="flex items-center px-10">
            <div class="flex-auto">
                <h1 class="text-xl capitalize font-semibold leading-6 text-primary">{{getPlurar(title)}}</h1>
            </div>

            <div class="mt-2 ml-16 " v-show="add">
                <Button type="button" @click="addItem()" severity="success">
                    <PlusIcon class="h-6 w-6 mr-2" aria-hidden="true" />
                    Nuevo
                </Button>
            </div>
        </div>
        <div class="mt-2 flow-root">
            <div class="m-2">
                <div class="inline-block min-w-full py-2 align-middle max-h-64 hover:overflow-y-auto overflow-y-hidden custom-scroll">
                    <table class="min-w-full border-separate border-spacing-0">
                        <thead>
                            <tr>
                                <th v-for="( header, index) of headers" :key="header" scope="col" class=" sticky -top-2 border-b border-gray-300 bg-gray-200 bg-opacity-75 py-3.5 pl-8 pr-3 text-left text-sm font-semibold text-gray-900 capitalize backdrop-blur backdrop-filter sm:pl-6 lg:pl-8"
                                    :class="[index == 0 ? 'hidden lg:table-cell':'', header.show !== false ? '':'hidden']">{{header.header}}</th>

                                <th v-if="props.edit || props.delete" scope="col" class="sticky -top-2 border-b border-gray-300 bg-gray-200 bg-opacity-75 py-3.5 pl-8 pr-3 text-left text-sm font-semibold text-gray-900 capitalize backdrop-blur backdrop-filter sm:pl-6 lg:pl-8"
                                >Actions</th>
                            </tr>
                        </thead>
                        <tbody >
                            <tr v-for="(person, personIdx) in elements" :key="person.email">
                                <td v-for="(header, index) of headers" :class="['border-b border-gray-200 whitespace-normal  py-2 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6 lg:pl-8', index == 0 ? 'hidden lg:table-cell':'', header.show !== false ? '':'hidden']">
                                    {{ index == 0 ? personIdx+1:person[header.field] }}</td>

                                <td v-if="props.edit || props.delete"  :class="['whitespace-normal  py-2 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6 lg:pl-8 flex space-x-2 border-b border-gray-200']">
                                    <div class="" v-if="props.edit">
                                        <Button  severity="primary" class="hover:bg-primary">
                                            <PencilIcon class="h-4 w-4 " aria-hidden="true" />
                                        </Button>
                                    </div>
                                    <div class="" @click="deleteItem(person.id)"  v-if="props.delete">
                                        <Button severity="danger">
                                            <TrashIcon class="h-4 w-4 " aria-hidden="true" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
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
const {toast} = useSweetalert();

const {confirmDelete} = useSweetalert();

const props = defineProps({
    headers: {
        type: Array,
        required: true
    },
    elements: {
        type: Array,
        required: true
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
const form = useForm({
    object: {}
})
const selected = ref()
const  options= ref(['list', 'of', 'options']);
const submit = () => {
    loading.value = true;
    router.post(route(props.url+'.store'), form.object,{
        onSuccess: (res) => {
            open.value = false;
            toast(props.title + ' Creado Exitosamente', 'success');
        },
        onError: (errors) => {
            toast('Ha surgido un error', 'error');
        },
        onFinish: visit => {
            loading.value = false;
        }
    })
}

const deleteItem = (id) => {
    confirmDelete(id, props.title, props.url)
}

const addItem = () => {
    resetValues();
    open.value = true;
}

const resetValues = () => {
    for (let header of props.headers) {
        form.object[header.field] = header.type === 'multiple' ? []:'';
        router.page.props.errors[header.field] = null
    }
}

const getPlurar = (str) =>{
    return plural(str);
}



</script>
