<template>
    <div class="px-2 shadow-md py-4 rounded-b-xl max-w-screen">
        <div class="flex items-center">
            <div class="flex-auto">
                <h1 class="text-xl capitalize font-semibold leading-6 text-primary">{{title}}</h1>
            </div>

            <div class="mt-4 ml-16" v-show="add">
                <Button type="button" @click="open = !open" severity="success">
                    <PlusIcon class="h-6 w-6 mr-2" aria-hidden="true" />
                    Nuevo
                </Button>
            </div>
        </div>
        <div class="mt-2 flow-root">
            <div class="m-2">
                <div class="inline-block min-w-full py-2 align-middle h-64 hover:overflow-y-auto overflow-y-hidden custom-scroll">
                    <table class="min-w-full border-separate border-spacing-0">
                        <thead>
                            <tr>
                                <th v-for="( header, index) of headers" :key="header" scope="col" class=" sticky -top-2 border-b border-gray-300 bg-gray-200 bg-opacity-75 py-3.5 pl-8 pr-3 text-left text-sm font-semibold text-gray-900 capitalize backdrop-blur backdrop-filter sm:pl-6 lg:pl-8"
                                    :class="index == 0 ?'hidden lg:table-cell':''">{{header}}</th>
                            </tr>
                        </thead>
                        <tbody >
                            <tr v-for="(person, personIdx) in elements" :key="person.email">
                                <td v-for="(header, index) of headers" :class="[personIdx !== elements.length - 1 ? 'border-b border-gray-200' : '', 'whitespace-normal  py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6 lg:pl-8', index == 0 ? 'hidden lg:table-cell':'']">
                                    {{ person[header] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- modal -->
    <TransitionRoot as="template" :show="open">
        <Dialog as="div" class="relative z-10" @close="open = false">
            <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                <div class="fixed h-screen w-screen inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-999" />
            </TransitionChild>

            <div class="fixed inset-0 z-100 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200" leave-from="opacity-100 translate-y-0 sm:scale-100"
                        leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                        <DialogPanel class="relative transform overflow-hidden rounded-lg bg-white px-2 pb-4 pt-2 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg ">
                            <div>
                                <div class="text-center mt-2">
                                    <DialogTitle as="h3" class="text-xl font-semibold text-primary ">Crear {{title}}</DialogTitle>
                                    <div class="mt-2 space-y-4 border border-gray-200 p-2 rounded-lg">
                                        <TextInput class="mt-2 text-left" :label="header" v-for="header of headers" v-model="form.object[header]">
                                        </TextInput>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2 flex space-x-4">
                                <Button severity="danger" @click="open = false" >Cancelar</Button>
                                <Button severity="success" @click="submit">Guardar</Button>
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
import { PlusIcon } from '@heroicons/vue/24/outline'
import { router, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import TextInput from './TextInput.vue';

const form = useForm({
    object: {}
})
const open = ref(false)

onMounted(() => {
    for (let header of props.headers) {
        form.object[header] = '';
    }
})

const submit = () => {
    router.get(route('simple.crud'), form.object,{
        onSuccess: (res) => {
            console.log(res);
        }
    })
}

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
    title: String,
})
</script>
