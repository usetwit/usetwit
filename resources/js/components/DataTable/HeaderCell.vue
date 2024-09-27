<script setup>
import { computed, useTemplateRef } from 'vue'
import Filter from './Filter.vue'

const props = defineProps({
    column: { type: Object, required: true },
})

const emit = defineEmits(['sort'])
const activeData = defineModel()

let sortObj = computed(() => {
    const index = activeData.value.sort.findIndex(obj => obj.field === props.column.field)

    if (index !== -1) {
        const { field, order } = activeData.value.sort[index]
        return { field, order, position: index + 1 }
    }

    return null
})

const filter = useTemplateRef('filterRef')

const updateSort = (event, column, removeOtherSorts = false) => {
    if (filter.value?.inputRef.childNodes && Array.from(filter.value?.inputRef.childNodes).includes(event.target) || event.target === filter.value?.inputRef) {
        return
    }

    const sortField = activeData.value.sort.find(col => col.field === column.field)

    if (sortField) {
        if (sortField.order === 'desc') {
            activeData.value.sort = activeData.value.sort.filter(col => col.field !== column.field)
        } else {
            sortField.order = 'desc'
        }
    } else {
        activeData.value.sort.push({ field: column.field, order: 'asc' })
    }

    if (removeOtherSorts) {
        activeData.value.sort = activeData.value.sort.filter(col => col.field === column.field)
    }

    emit('sort')
}

const singleClick = (event, column) => {
    updateSort(event, column, true)
}

const ctrlClick = (event, column) => {
    updateSort(event, column)
}
</script>

<template>
    <th class="border-b border-t border-gray-200 p-0 select-none"
        :class="{
            'sticky left-0': column.sticky,
            'bg-white text-gray-800': !sortObj,
            'hover:bg-gray-100': !sortObj && column.sortable,
            'bg-slate-800 text-white hover:bg-slate-700': sortObj,
            'cursor-pointer': column.sortable,
        }"
    >
        <div v-if="column.label"
             class="px-4 py-3 flex justify-between items-center"
             @click.exact="singleClick($event, column)"
             @click.ctrl="ctrlClick($event, column)"
        >
            <div class="inline-flex items-center">
                <span v-if="column.label" class="py-2">{{ column.label }}</span>
                <span v-if="sortObj" class="text-white inline-flex ml-2">
                    <i v-if="sortObj.order === 'asc'" class="pi pi-sort-amount-up-alt"></i>
                    <i v-if="sortObj.order === 'desc'" class="pi pi-sort-amount-down-alt"></i>
                    <span
                        class="ml-2 flex items-center justify-center h-5 p-1.5 min-w-5 bg-slate-500 text-white text-xs rounded-full"
                    >
                        {{ sortObj.position }}
                    </span>
                </span>
                <span v-else-if="column.sortable" class="text-gray-500 ml-2">
                    <i class="pi pi-sort-alt"></i>
                </span>
            </div>
            <Filter v-if="column.type" v-model="activeData" :column="column" :sort-obj="sortObj" ref="filterRef"/>
        </div>
    </th>
</template>
