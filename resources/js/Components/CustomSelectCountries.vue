<script setup>
import axios from 'axios';
import Dropdown from 'primevue/dropdown';
import { ref } from 'vue';

const paises = ref()
axios.get('https://restcountries.com/v3.1/all?fields=flags,translations,name').then(
    (res) => {
        paises.value = res.data.map(pais => {
            pais.translations.spa.common = pais.translations.spa.common.toUpperCase()
            return pais
        })
    }
)

const selected = defineModel('selected', {
    required: true
})

</script>
<template>
    <Dropdown v-if="paises" v-model="selected" :options="paises" filter resetFilterOnHide placeholder="Selecciona el pais"
        optionLabel="translations.spa.common" class="w-full -mt-1 rounded-md md:w-14rem" :pt="{
            root: {
                class: 'h-10 !ring-gray-300 !ring-inset ring-1 !border-0 !shadow-sm '
            },
            input: {
                class: '!text-sm pt-3 pl-2'
            },
            item: {
                class: '!text-sm'
            }
        }">
        <template #value="slotProps">
            <div v-if="slotProps.value" class="flex space-x-1">
                {{ console.log(slotProps.value) }}
                <img :src="slotProps.value.flags.svg" width="30" :alt="slotProps.value">
                <p class="">{{ slotProps.value.translations.spa.common }}</p>
            </div>
            <span v-else>
                {{ slotProps.placeholder }}
            </span>
        </template>
        <template #option="slotProps">
            <div class="flex space-x-1">
                <img :src="slotProps.option.flags.svg" width="30" :alt="slotProps.option.translations.spa.common">
                <p>{{ slotProps.option.translations.spa.common }}</p>
            </div>
        </template>

    </Dropdown>
</template>

