<script setup>
import { ref, provide } from 'vue'

const props = defineProps({
    rows: { type: Array, required: true },
})

const emit = defineEmits(['sort'])

const columns = ref(new Set())
const activeData = defineModel()

provide('registerColumn', column => {
    columns.value.add(column)
})

provide('deregisterColumn', column => {
    columns.value.delete(column)
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

const sorted = column => {
    const index = activeData.value.sort.findIndex(obj => obj.field === column.field)

    if (index !== -1) {
        const field = activeData.value.sort.find(obj => obj.field === column.field)

        return { field: field.field, order: field.order, position: index + 1 }
    }

    return null
}
</script>

<template>
    <slot/>
    <div class="overflow-x-auto">
        <table class="mt-8 min-w-full">
            <thead>
            <tr>
                <th v-for="(col, i) in columns" :key="col.field || i"
                    class="border-b border-t border-gray-200 p-0 select-none"
                    :class="{
                                'sticky left-0': col.sticky,
                                'bg-white text-gray-800 hover:bg-gray-100': !sorted(col) && col.sortable,
                                'bg-slate-800 text-white hover:bg-slate-700': sorted(col),
                                'cursor-pointer': col.sortable,
                            }"
                >
                    <div class="px-4 py-3 flex justify-between items-center"
                         :class="{}"
                         @click.exact="headerSingleClick(col)"
                         @click.ctrl="headerCtrlClick(col)"
                    >
                        <div class="inline-flex items-center">
                            <span v-if="col.label" class="p-2">{{ col.label }}</span>
                            <span v-if="sorted(col)" class="text-white inline-flex">
                                <i v-if="sorted(col).order === 'asc'" class="pi pi-sort-amount-up-alt"></i>
                                <i v-if="sorted(col).order === 'desc'" class="pi pi-sort-amount-down-alt"></i>
                                <span
                                    class="ml-2 flex items-center justify-center h-4 p-1 min-w-4 bg-slate-500 text-white text-xs rounded-full"
                                >
                                    {{ sorted(col).position }}
                                </span>
                            </span>
                            <span v-else-if="col.sortable" class="text-gray-500">
                                <i class="pi pi-sort-alt"></i>
                            </span>
                        </div>
                        <button v-if="col.filter"
                                type="button"
                                class="inline-flex items-center p-2 rounded-full ml-2"
                                :class="{'text-gray-600 hover:bg-gray-100': !sorted(col), 'text-gray-100 hover:bg-slate-600': sorted(col)}"
                        >
                            <i class="pi pi-filter"></i>
                        </button>
                    </div>
                </th>
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
    </div>
</template>
