<script setup>
import { ref } from "vue";
import Column from "./Form/Column.vue";
import DataTable from "./Form/DataTable.vue";

const props = defineProps({
    format: { type: String, required: true },
    displayFormat: { type: String, required: true },
    regex: { type: String, required: true },
    separator: { type: String, required: true },
})


</script>

<template>
    <DataTable>
        <Column header="Name" field="name"></Column>
    </DataTable>
</template>
