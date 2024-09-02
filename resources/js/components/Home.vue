<script setup>
import AutoComplete from "./Form/AutoComplete.vue";
import { ref } from "vue";

// const items = ref([
//     {
//         name: 'Lee', id: 1,
//     },
//     {
//         name: 'Sandra', id: 2,
//     },
// ])

const groupedCities = ref([
    {
        label: 'Germany',
        code: 'DE',
        items: [
            { label: 'Berlin', value: 'Berlin' },
            { label: 'Frankfurt', value: 'Frankfurt' },
            { label: 'Hamburg', value: 'Hamburg' },
            { label: 'Munich', value: 'Munich' }
        ]
    },
    {
        label: 'USA',
        code: 'US',
        items: [
            { label: 'Chicago', value: 'Chicago' },
            { label: 'Los Angeles', value: 'Los Angeles' },
            { label: 'New York', value: 'New York' },
            { label: 'San Francisco', value: 'San Francisco' }
        ]
    },
    {
        label: 'Japan',
        code: 'JP',
        items: [
            { label: 'Kyoto', value: 'Kyoto' },
            { label: 'Osaka', value: 'Osaka' },
            { label: 'Tokyo', value: 'Tokyo' },
            { label: 'Yokohama', value: 'Yokohama' }
        ]
    }
])

const value = ref('jhjhjh')
</script>

<template>
    <AutoComplete v-model="value" :items="groupedCities" optionLabel="label" optionGroupLabel="label"
                  optionGroupItems="items" dropdown min-width/>
</template>
