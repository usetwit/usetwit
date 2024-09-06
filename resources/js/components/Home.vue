<script setup>
import AutoComplete from "./Form/AutoComplete.vue";
import { ref } from "vue";
import Datepicker from "./Form/Datepicker.vue";

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
            { label: 'Berlin', something: 'x', value: 'Berlin' },
            { label: 'Frankfurt', something: 'x', value: 'Frankfurt' },
            { label: 'Hamburg', something: 'x', value: 'Hamburg' },
            { label: 'Munich', something: 'x', value: 'Munich' }
        ]
    },
    {
        label: 'USA',
        code: 'US',
        items: [
            { label: 'Chicago', something: 'x', value: 'Chicago' },
            { label: 'Los Angeles', something: 'x', value: 'Los Angeles' },
            { label: 'New York', something: 'x', value: 'New York' },
            { label: 'San Francisco', something: 'x', value: 'Sjjjwwwwewwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwjjo' }
        ]
    },
    {
        label: 'Japan',
        code: 'JP',
        items: [
            { label: 'Kyoto', something: 'x', value: 'Kyoto' },
            { label: 'Osaka', something: 'x', value: 'Osaka' },
            { label: 'Tokyo', something: 'x', value: 'Tokyo' },
            { label: 'Yokohama', something: 'x', value: 'Yokohama' }
        ]
    }
])

const value = ref()
const date = ref()
</script>

<template>
    <div class="text-nowrap">
        <AutoComplete v-model="value" :items="groupedCities" optionLabel="label"
                      optionGroupLabel="label"
                      optionGroupItems="items" max-height="300" position-x="left" dropdown position-y="top"
        >
            <template #item="item">
                {{ item.label }} - {{ item.value }}
            </template>
        </AutoComplete>

        &nbsp;

        <Datepicker dropdown></Datepicker>

    </div>
</template>
