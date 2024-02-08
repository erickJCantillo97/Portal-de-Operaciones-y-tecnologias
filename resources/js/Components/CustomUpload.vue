<script setup>
import Button from 'primevue/button';
import CustomModal from './CustomModal.vue';
import { ref } from 'vue';
import FileUpload from 'primevue/fileupload';
import CustomInput from './CustomInput.vue';
import Empty from './Empty.vue';

const props = defineProps({
    labelButton: {
        type: String,
        default: 'Subir archivo'
    },
    iconButton: {
        type: String,
        default: 'fa-solid fa-cloud-arrow-up'
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
        default: 'image/*'
    },
    maxFileSize: {
        type: String,
        default: '1000000'
    },
    multiple: {
        type: Boolean,
        default: false
    }
});
const visible = ref(false)

const onRemoveTemplatingFile = (file, removeFileCallback, index) => {
    removeFileCallback(index);
    totalSize.value -= parseInt(formatSize(file.size));
    totalSizePercent.value = totalSize.value / 10;
};
const onClearTemplatingUpload = (clear) => {
    clear();
    totalSize.value = 0;
    totalSizePercent.value = 0;
};

const onSelectedFiles = (event) => {
    files.value = event.files;
    files.value.forEach((file) => {
        totalSize.value += parseInt(formatSize(file.size));
    });
};

const uploadEvent = (callback) => {
    totalSizePercent.value = totalSize.value / 10;
    callback();
};

const onTemplatedUpload = () => {
    toast.add({ severity: "info", summary: "Success", detail: "File Uploaded", life: 3000 });
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

</script>
<template>
    <Button :label="labelButton" :icon="iconButton" @click="visible = true" />
    <CustomModal v-model:visible="visible" width="30rem" :icon="iconModal" :titulo="titleModal">
        <template #body>
            <FileUpload :mode :accept :maxFileSize :multiple>
                <template #header="{ chooseCallback, uploadCallback, clearCallback, files }">
                    <div class="flex flex-wrap justify-content-between align-items-center flex-1 gap-2">
                        <div class="flex gap-2">
                            <Button @click="chooseCallback()" icon="fa-solid fa-file-import" text
                                label="Seleccionar"></Button>
                            <Button @click="uploadEvent(uploadCallback)" icon="fa-solid fa-cloud-arrow-up" text
                                label="Subir" severity="success" :disabled="!files || files.length === 0"></Button>
                            <Button @click="clearCallback()" icon="fa-solid fa-circle-xmark" text severity="danger"
                                label="Quitar todos" :disabled="!files || files.length === 0"></Button>
                        </div>
                    </div>
                </template>
                <template #content="{ files, uploadedFiles, removeUploadedFileCallback, removeFileCallback }">
                    <div v-if="files.length > 0">
                        <h5>Pending</h5>
                        <div class="flex flex-wrap p-0 sm:p-5 gap-5">
                            <div v-for="(file, index) of files" :key="file.name + file.type + file.size"
                                class="card m-0 px-6 flex flex-column border-1 surface-border align-items-center gap-3">
                                <div>
                                    <img role="presentation" :alt="file.name" :src="file.objectURL" width="100"
                                        height="50" />
                                </div>
                                <span class="font-semibold">{{ file.name }}</span>
                                <div>{{ formatSize(file.size) }}</div>
                                <Badge value="Pending" severity="warning" />
                                <Button icon="pi pi-times" @click="onRemoveTemplatingFile(file, removeFileCallback, index)"
                                    outlined rounded severity="danger" />
                            </div>
                        </div>
                    </div>
                    <div v-if="uploadedFiles.length > 0">
                        <h5>Completed</h5>
                        <div class="flex flex-wrap p-0 sm:p-5 gap-5">
                            <div v-for="(file, index) of uploadedFiles" :key="file.name + file.type + file.size"
                                class="card m-0 px-6 flex flex-column border-1 surface-border align-items-center gap-3">
                                <div>
                                    <img role="presentation" :alt="file.name" :src="file.objectURL" width="100"
                                        height="50" />
                                </div>
                                <span class="font-semibold">{{ file.name }}</span>
                                <div>{{ formatSize(file.size) }}</div>
                                <Badge value="Completed" class="mt-3" severity="success" />
                                <Button icon="pi pi-times" @click="removeUploadedFileCallback(index)" outlined rounded
                                    severity="danger" />
                            </div>
                        </div>
                    </div>
                </template>
                <template #empty>
                    <Empty message="Arrastra aqui" />
            </template>
        </FileUpload>
    </template>
</CustomModal></template>