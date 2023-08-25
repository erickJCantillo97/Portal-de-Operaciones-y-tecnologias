<script setup>
import {useSweetalert} from '@/composable/sweetAlert'

const {confirmDelete} = useSweetalert();

defineProps({
    url: {
        type: String,
        required: true,
    },
    id: {
        type: Number,
        required: true
    },
    title:{
        type: String,
        required: true
    }
})

const deleteAction = () => {
    confirmDelete(id, title, url)
}
</script>
<template>
    <Button severity="danger" @click="deleteAction">
        <TrashIcon class="h-4 w-4 " aria-hidden="true" />
    </Button>
</template>
