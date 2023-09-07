<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, reactive } from 'vue';
import { BryntumGantt } from '@bryntum/gantt-vue-3';
import { useGanttConfig } from '@/AppConfig';
import "@bryntum/gantt/gantt.material.css";
import '@bryntum/gantt/locales/gantt.locale.Es.js';
import { LocaleManager } from '@bryntum/gantt/gantt.module.js';

<<<<<<< HEAD

const props = defineProps({
    project: Object,
})


LocaleManager.applyLocale('Es');
=======
LocaleManager.applyLocale('Es');
const props = defineProps({
    project: Number
});
>>>>>>> 35106b40e89ef0836949407e9ec275b6e27c7c1b
const barMargin = ref(4);
const ganttConfig = reactive(useGanttConfig(props.project));

</script>
<template>
    <AppLayout title="">
        <div class="">
            <div class="h-screen">
                <div class="overflow-hidden shadow-xl sm:rounded-lg h-full">
                    {{ project.name }}
                    <bryntum-gantt v-bind="ganttConfig" :barMargin="barMargin"/>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
