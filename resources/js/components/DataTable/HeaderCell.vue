<script setup>
import { computed } from 'vue'

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

const updateSort = (column, removeOtherSorts = false) => {
    const sortFieldIndex = activeData.value.sort.findIndex(col => col.field === column.field)

    if (sortFieldIndex !== -1) {
        const sortField = activeData.value.sort[sortFieldIndex]

        if (sortField.order === 'desc') {
            activeData.value.sort.splice(sortFieldIndex, 1)
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

const headerSingleClick = column => {
    updateSort(column, true)
}

const headerCtrlClick = column => {
    updateSort(column)
}
</script>

<template>
    <th class="border-b border-t border-gray-200 p-0 select-none"
        :class="{
            'bg-white sticky left-0': column.sticky,
            'bg-white text-gray-800 hover:bg-gray-100': !sortObj && column.sortable,
            'bg-slate-800 text-white hover:bg-slate-700': sortObj,
            'cursor-pointer': column.sortable,
        }"
    >
        <div v-if="column.field"
             class="px-4 py-3 flex justify-between items-center"
             @click.exact="headerSingleClick(column)"
             @click.ctrl="headerCtrlClick(column)"
        >
            <div class="inline-flex items-center">
                <span v-if="column.label" class="p-2">{{ column.label }}</span>
                <span v-if="sortObj" class="text-white inline-flex">
                    <i v-if="sortObj.order === 'asc'" class="pi pi-sort-amount-up-alt"></i>
                    <i v-if="sortObj.order === 'desc'" class="pi pi-sort-amount-down-alt"></i>
                    <span
                        class="ml-2 flex items-center justify-center h-4 p-1 min-w-4 bg-slate-500 text-white text-xs rounded-full"
                    >
                        {{ sortObj.position }}
                    </span>
                </span>
                <span v-else-if="column.sortable" class="text-gray-500">
                    <i class="pi pi-sort-alt"></i>
                </span>
            </div>
            <button v-if="column.filter"
                    type="button"
                    class="inline-flex items-center p-2 rounded-full ml-2"
                    :class="{'text-gray-600 hover:bg-gray-200': !sortObj, 'text-gray-100 hover:bg-slate-600': sortObj}"
                    @click.stop=""
            >
                <i class="pi pi-filter"></i>
            </button>
        </div>
    </th>
</template>
