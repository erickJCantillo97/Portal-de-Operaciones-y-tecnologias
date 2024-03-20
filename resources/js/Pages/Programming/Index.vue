<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import WeekTable from '@/Components/WeekTable.vue'

/**
 * The above code is a JavaScript function that takes a time string in 24-hour format (e.g., "13:30")
// and converts it to a 12-hour format with AM/PM indicator. It creates a new Date object with the time
// string appended to a date string ("1970-01-01T") and then uses the toLocaleString method with
// options to format the time in 12-hour format with the 'es-CO' locale and 'h23' hour cycle.
//
 * @param {*} hora
 */
function format24h(hora) {
    return new Date("1970-01-01T" + hora).toLocaleString('es-CO',
        { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' })
}
//#endregion
</script>

<template>
    <AppLayout>
        <div class="h-[89vh] ">
            <div class="flex justify-between items-center px-4 h-min">
                <span class="text-xl font-bold text-primary h-full items-center flex">
                    <p> Programación de Actividades </p>
                    <!-- <CustomInput type="week" v-model:input="date" @change="getTask('date'); getPersonalData()" /> -->
                </span>
                <div class="flex items-center space-x-2">
                    <Link :href="route('programming.create')">
                    <Button label="Programar Personal" severity="success" icon="fa-solid fa-plus" />
                    </Link>
                </div>
            </div>
            <div class="mt-2 px-4 h-[79vh] overflow-y-auto">
                <WeekTable />
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.custom-image {
    width: 200px;
    height: 50px;
    /* object-position: 50% 30%; */
    border-radius: 5px 10px;
    object-fit: cover;
    /* Opciones: 'cover', 'contain', 'fill', etc. */
}

.info-resto {
    font-size: 15px;
    text-wrap: balance;
    opacity: .5;
}
</style>