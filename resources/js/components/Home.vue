<script setup>
import AutoComplete from "./Form/AutoComplete.vue";
import { ref } from "vue";

const items = ref([
    {
        name: 'Lee', id: 1,
    },
    {
        name: 'Sandra', id: 2,
    },
])

const value = ref('jhjhjh')
</script>

<template>
    <AutoComplete v-model="value" :items="items" dropdown/>
</template>
