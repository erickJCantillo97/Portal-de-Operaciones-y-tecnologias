<template>
    <div>
        <bryntum-project-model
            ref="project"
            v-bind="projectConfig"
            :tasks="tasks"
            :dependencies="dependencies"
        />
        <bryntum-gantt
            ref="gantt"
            v-bind="ganttConfig"
        />
    </div>
</template>

<script>
import { ref, reactive, onMounted } from 'vue';

import {
    BryntumProjectModel,
    BryntumGantt
} from '@bryntum/gantt-vue-3';

import { useGanttConfig, useProjectConfig } from '@/composable/AppConfig';

export default {
    name : 'App',

    components : {
        BryntumProjectModel,
        BryntumGantt
    },

    setup() {
        const gantt = ref(null);
        const project = ref(null);

        const ganttConfig = reactive(useGanttConfig());
        const projectConfig = reactive(useProjectConfig());

        const tasks = ref(null);
        const dependencies = ref(null);

        tasks.value = [
            {
                id       : 1,
                name     : 'Write docs',
                expanded : true,
                children : [
                    { id : 6, name : 'Proof-read docs', startDate : '2022-01-02', endDate : '2022-01-09' },
                    { id : 3, name : 'Release docs', startDate : '2022-01-09', endDate : '2022-01-10' }
                ]
            }
        ];
        dependencies.value = [
            { fromTask : 6, toTask : 3 }
        ];

        onMounted(() => {
            gantt.value.instance.value.project = project.value.instance.value;
        });

        return {
            project,
            gantt,
            projectConfig,
            ganttConfig,
            tasks,
            dependencies
        };
    }
};

</script>

<!-- <style lang="scss">
@import './App.scss';
</style> -->
