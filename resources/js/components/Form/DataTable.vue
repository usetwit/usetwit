<script setup>
import { ref, provide } from 'vue';

// Props for DataTable
const props = defineProps({
    rows: Array // The array of data to display
});

// Array to hold columns registered by each Column component
const columns = ref([]);

// Provide a way for Column children to register themselves
provide('registerColumn', (column) => {
    columns.value.push(column);
});
</script>

<template>
    <table>
        <thead>
        <tr>
            <th v-for="col in columns" :key="col.field">{{ col.header }}</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="row in rows" :key="row.id">
            <td v-for="col in columns" :key="col.field">
                <slot name="body" :data="row" :field="col.field">
                    {{ row[col.field] }}
                </slot>
            </td>
        </tr>
        </tbody>
    </table>
</template>

<style scoped>

</style>
