<script setup>
import AppLayout from "@/Layouts/AppLayout.vue"
import KanbanQuotes from "@/Pages/Dashboards/Quotes/KanbanQuotes.vue"
import PayrollTable from "./PayrollTable.vue"
import Planning from "@/Pages/Dashboards/Projects/Planning.vue"
import Quotes from "./Dashboards/Quotes.vue"
import QuotesCard from "@/Pages/Dashboards/Quotes/QuotesCards.vue"
import Task from "./Dashboards/Personal/Task.vue"
import UserHeader from "@/Components/sections/UserHeader.vue"
import WareHouse from "./Dashboards/WareHouse.vue"

const props = defineProps({
    projects: Array,
    contracts: Array
})

// const broadcastChannel = () => {
//     setTimeout(() => {
//         window.Echo.private('testing')
//             .listen('.MyWebSocket', (e) => {
//                 alert(e.data);aa
//             })
//     }, 200);
// }
</script>

<template>
    <AppLayout>
        <div class="size-full overflow-y-scroll space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 max-w-full">
                <UserHeader class="col-span-1" />
                <QuotesCard class="col-span-1" :showIndicators="false"
                    v-if="$page.props.auth.user.oficina == 'DEPPC' || $page.props.auth.user.oficina=='OFTIC'" />
            </div>

            <!-- <Task v-if="$page.props.auth.user.username == 'ecantillo' || $page.props.auth.user.oficina=='OFTIC'" /> -->
            <KanbanQuotes v-if="$page.props.auth.user.username == 'ecantillo' || $page.props.auth.user.oficina=='OFTIC'" />
            <!-- <PayrollTable /> -->
            <Quotes v-if="$page.props.auth.user.oficina == 'DEEST' || $page.props.auth.user.username == 'elara'|| $page.props.auth.user.oficina =='OFTIC'" />
            <!-- <Planning v-if="$page.props.auth.user.oficina == 'DEPPC' || $page.props.auth.user.oficina=='OFTIC'" :projects="props.projects" /> -->
            <!-- <Tools v-if="hasPermission('tool edit')" :projects="props.projects" /> -->
            <!-- <Projects v-else /> -->
            <!-- <CustomUpload mode="advanced" :multiple="true" accept=".xlsx,.xls" url="prueba"/> -->
        </div>
    </AppLayout>
</template>
