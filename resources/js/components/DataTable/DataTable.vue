<script setup>
import { ref, provide } from 'vue'
import HeaderCell from './HeaderCell.vue'

const props = defineProps({
    rows: { type: Array, required: true },
    isLoading: { type: Boolean, default: false },
})

const columns = ref(new Set())
const emit = defineEmits(['sort'])
const activeData = defineModel()

provide('registerColumn', column => {
    columns.value.add(column)
})

provide('deregisterColumn', column => {
    columns.value.delete(column)
})
</script>

<template>
    <slot/>
    <div class="mt-8 overflow-x-auto relative">
        <table class="min-w-full">
            <thead>
            <tr>
                <HeaderCell v-for="col in columns" v-model="activeData" :column="col" @sort="$emit('sort')"/>
            </tr>
            </thead>
            <tbody>
            <tr v-for="row in rows" :key="row.id">
                <td v-for="col in columns"
                    :key="col.field + '_' + row.id.toString()"
                    class="px-4 py-3 border-b border-gray-200 bg-white"
                    :class="{'sticky left-0': col.sticky}"
                >
                    <component v-if="col.body" :is="col.body" :row="row"/>
                    <template v-else>{{ row[col.field] }}</template>
                </td>
            </tr>
            </tbody>
        </table>
        <div v-if="isLoading"
             class="bg-opacity-50 bg-gray-100 z-[150] w-full h-full left-0 top-0 absolute text-[2rem] text-gray-700 flex items-center justify-center"
        >
            <i class="pi pi-spinner pi-spin"></i>
        </div>
    </div>
</template>
