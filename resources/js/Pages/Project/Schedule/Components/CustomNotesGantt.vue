<script setup>
import Empty from '@/Components/Empty.vue';
import Sidebar from 'primevue/sidebar';
import { ref } from 'vue';

defineProps({
    notes:Object,
    class:String
})
const visibleNotes = ref(false)

const taskFilter = defineModel('taskFilter', {
    type: String,
})


</script>

<template>
    <Button raised :class v-tooltip.bottom="'Ver notas'" :disabled="notes.data.length == 0" :loading="notes.load"
        severity="success" icon="fa-regular fa-note-sticky" @click="visibleNotes = true" />
        <!-- {{ notes }} -->
    <Sidebar v-model:visible="visibleNotes" header="Notas del proyecto" position="right">
        <div class="border-t w-full space-y-2 p-1">
            <div v-if="notes.data.length == 0" class="mt-10">
                <Empty message="No hay notas" />
            </div>
            <div v-else v-for="note, index in notes.data"
                @click="(taskFilter == note.name ? taskFilter = '' : taskFilter = note.name); onFilterChange()"
                class="border cursor-pointer rounded-md p-1 hover:bg-primary-light"
                :class="taskFilter == note.name ? 'bg-success-light' : ''">
                <label :for="index + note.id" class="font-bold w-full text-center">{{ note.name }}</label>
                <p class="w-full border rounded-md overflow-hidden p-1 text-ellipsis">
                    {{ note.note }}
                </p>
            </div>
        </div>
    </Sidebar>
</template>