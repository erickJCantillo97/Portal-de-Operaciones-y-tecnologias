<script setup>

import { ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { useToast } from "primevue/usetoast";
import Badge from 'primevue/badge';
import Button from 'primevue/button';
import CustomModal from './CustomModal.vue';
import Empty from './Empty.vue';
import FileUpload from 'primevue/fileupload';
import Loading from './Loading.vue';
import Toast from 'primevue/toast';
const toast = useToast();

const props = defineProps({
    labelButton: {
        type: String,
        default: null
    },
    iconButton: {
        type: String,
        default: 'fa-solid fa-cloud-arrow-up'
    },
    severity: {
        type: String,
        default: 'primary'
    },
    text: {
        type: Boolean,
        default: false
    },
    outlined: {
        type: Boolean,
        default: false
    },
    classButton: {
        type: String,
        default: ''
    },
    titleModal: {
        type: String,
        default: 'Subir archivo'
    },
    iconModal: {
        type: String,
        default: 'fa-solid fa-cloud-arrow-up'
    },
    mode: {
        type: String,
        default: 'basic'
    },
    accept: {
        type: String,
        default: null
    },
    maxFileSize: {
        type: Number,
        default: 1000000000
    },
    multiple: {
        type: Boolean,
        default: false
    },
    url: {
        type: String,
        default: null
    },
    tooltip: {
        type: String,
        default: null
    }
});
const visible = ref(false)

const onRemoveTemplatingFile = (file, removeFileCallback, index) => {
    removeFileCallback(index);
};

const uploadEvent = (callback) => {
    callback();
};

const formatSize = (bytes) => {
    const k = 1024;
    const dm = 3;
    const sizes = ['B', 'KB', 'MB', 'GB'];

    if (bytes === 0) {
        return `0 ${sizes[0]}`;
    }

    const i = Math.floor(Math.log(bytes) / Math.log(k));
    const formattedSize = parseFloat((bytes / Math.pow(k, i)).toFixed(dm));

    return `${formattedSize} ${sizes[i]}`;
}

const errors = ref()

const loading = ref(false)
const uploadArchives = (event) => {
    loading.value = true
    let files
    files = props.multiple ? event.files : event.files[0]
    router.post(props.url, { docs: files }, {
        onSuccess: () => {
            toast.add({ severity: 'success', group: "customToast", text: 'Archivo subido con exito', life: 2000 })
            loading.value = false
            visible.value = false
        },
        onError: (e) => {
            console.log(e)
            errors.value = e
            toast.add({ severity: 'error', group: "customToast", text: 'Error al guardar', life: 2000 })
            loading.value = false
        }
    })
}

</script>
<template>
    <Button v-tooltip.left="tooltip" :class="classButton" :outlined :severity :label="labelButton" :icon="iconButton"
        @click="visible = true" />
    <CustomModal v-model:visible="visible" width="50rem" :icon="iconModal" :titulo="titleModal">
        <template #body>
            <FileUpload :mode :accept :maxFileSize :multiple customUpload @uploader="uploadArchives">
                <template #header="{ chooseCallback, uploadCallback, clearCallback, files }">
                    <div class="flex flex-wrap justify-content-between align-items-center flex-1 gap-2">
                        <div class="flex gap-2">
                            <Button @click="chooseCallback(); errors = null" icon="fa-solid fa-file-import" text
                                label="Seleccionar"></Button>
                            <Button @click="uploadEvent(uploadCallback)" icon="fa-solid fa-cloud-arrow-up" text
                                label="Subir" severity="success" :disabled="!files || files.length === 0"></Button>
                            <Button @click="clearCallback()" icon="fa-solid fa-circle-xmark" text severity="danger"
                                label="Quitar todos" :disabled="!files || files.length === 0"></Button>
                        </div>
                    </div>
                </template>
                <template #content="{ files, removeFileCallback }">
                    <div v-if="files.length > 0">
                        <div class="grid p-0 sm:p-1 gap-3">
                            <div v-for="(file, index) of files" :key="file.name + file.type + file.size"
                                class="flex w-full justify-between items-center hover:bg-gray-100 p-1">
                                <span class="flex space-x-2 cursor-default" v-tooltip.left="file.name">
                                    <span class="w-24 h-14 flex items-center justify-center">
                                        <img v-if="file.type.includes('image')" role="presentation" :alt="file.name"
                                            :src="file.objectURL" />
                                        <i v-else-if="file.type.includes('pdf')"
                                            class="fa-solid fa-file-pdf text-6xl text-red-600" />
                                        <i v-else-if="file.type == 'text/plain'"
                                            class="fa-regular fa-file-lines text-6xl  text-gray-600" />
                                        <i v-else-if="file.type.includes('spreadsheet') || file.type.includes('excel')"
                                            class="fa-solid fa-file-excel text-6xl  text-green-600" />
                                        <i v-else-if="file.type.includes('word')"
                                            class="fa-solid fa-file-word text-6xl  text-primary" />
                                        <i v-else class="fa-solid fa-file text-6xl  text-gray-600" />
                                    </span>
                                    <span>
                                        <p class="font-semibold text-sm truncate w-[35rem]">{{ file.name }}</p>
                                        <p class="text-xs">{{ formatSize(file.size) }}</p>
                                        <Badge value="Pendiente" severity="warning" />
                                    </span>
                                </span>
                                <Button icon="fa-solid fa-trash-can" v-tooltip.rigth="'Quitar de la cola'"
                                    @click="onRemoveTemplatingFile(file, removeFileCallback, index)" text
                                    severity="danger" />
                            </div>
                        </div>
                    </div>
                </template>
                <template #empty>
                    <Loading v-if="loading" message="Subiendo..." />
                    <Empty v-else message="Arrastra aqui" />
                </template>
            </FileUpload>
            <div v-if="errors" class="w-full p-1 text-red-600">
                <p class="text-center font-bold">Hay errores en el archivo</p>
                <div class="max-h-32 overflow-y-auto">
                    <div class="flex space-x-2 items-center" v-for="error in errors">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <p>{{ error }}</p>
                    </div>
                </div>
            </div>
        </template>
    </CustomModal>
    <Toast position="bottom-center" group="customToast"
        :pt="{ content: '!py-1 !items-center !flex !justify-between !px-3 ', }">
        <template #message="slotProps">
            <i v-if="slotProps.message.icon" class="text-3xl" :class="slotProps.message.icon" />
            <i v-else class="text-3xl"
                :class="slotProps.message.severity == 'error' ? 'fa-solid fa-xmark' : slotProps.message.severity == 'success' ? 'fa-solid fa-check' : slotProps.message.severity == 'info' ? 'fa-solid fa-circle-info' : slotProps.message.severity == 'warn' ? 'fa-solid fa-triangle-exclamation' : null" />
            <div class="flex items-center space-x-2">
                <p class="">{{ slotProps.message.text }}</p>
            </div>
        </template>
    </Toast>
</template>