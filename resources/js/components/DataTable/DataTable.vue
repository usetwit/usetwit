<script setup>
import { ref, provide, computed, inject } from 'vue'
import HeaderCell from './HeaderCell.vue'
import ColumnSelect from './ColumnSelect.vue'

const props = defineProps({
    rows: { type: Array, required: true },
    isLoading: { type: Boolean, default: false },
})

const columns = ref(new Set())
defineEmits(['sort', 'filter'])
const activeData = defineModel()

provide('registerColumn', column => {
    columns.value.add(column)
})

provide('deregisterColumn', column => {
    columns.value.delete(column)
})

const orderedColumns = computed(() => {
    const columnsArray = Array.from(columns.value)

    columnsArray.sort((a, b) => {
        if (a.order === undefined && b.order !== undefined) {
            return -1
        } else if (a.order !== undefined && b.order === undefined) {
            return 1
        }

        return a.order - b.order
    })

    return columnsArray
})

const { save, filter } = inject('tableInstance')
</script>

<template>
    <slot/>
    <ColumnSelect v-model="activeData.columns"/>

    <div class="mt-8 overflow-x-auto relative">
        <table class="min-w-full">
            <thead>
            <tr>
                <HeaderCell v-for="col in orderedColumns" v-model="activeData" :column="col" @sort="$emit('sort');save()" @filter="$emit('filter');filter()"/>
            </tr>
            </thead>
            <tbody>
            <tr v-for="row in rows" :key="row.id">
                <td v-for="col in orderedColumns"
                    :key="col.field + '_' + row.id.toString()"
                    class="px-4 py-3 border-b border-gray-200 bg-white"
                    :class="{'sticky left-0': col.sticky}"
                    v-bind="col.attributes"
                >
                    <component v-if="col.body" :is="col.body" :row="row"/>
                    <template v-else>{{ row[col.field] }}</template>
                </td>
            </tr>
            </tbody>
        </table>
        <div v-if="isLoading"
             class="bg-opacity-50 bg-slate-100 z-[150] w-full h-full left-0 top-0 absolute text-[2rem] text-slate-700 flex items-center justify-center"
        >
            <i class="pi pi-spinner pi-spin"></i>
        </div>
    </div>
</template>
