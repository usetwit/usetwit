<script setup>
import { ref, provide } from 'vue'

const props = defineProps({
    rows: { type: Array, required: true },
})

const emit = defineEmits(['save'])

const columns = ref([])
const activeData = defineModel()

provide('registerColumn', column => {
    const index = activeData.value.columns.findIndex(col => col.field === column.field)
    columns.value.splice(index, 0, column)
})

provide('deregisterColumn', column => {
    const index = columns.value.findIndex(col => col.field === column.field)
    columns.value.splice(index, 1)
})

const headerSingleClick = column => {
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

    activeData.value.sort = activeData.value.sort.filter(col => col.field === column.field)
    emit('save')
}

const headerCtrlClick = column => {

}
</script>

<template>
    <slot/>
    <div>
        <table class="mt-8 w-full">
            <thead>
            <tr>
                <th v-for="col in columns" :key="col.field" class="border-b border-t border-gray-200">
                    <div class="px-4 py-3 flex justify-between items-center"
                         :class="{'cursor-pointer hover:bg-gray-50': col.sortable}"
                         @click="headerSingleClick(col)"
                         @click.ctrl="headerCtrlClick(col)"
                    >
                        <span>{{ col.label }}</span>
                        <button v-if="col.children.filter"
                                type="button"
                                class="inline-flex items-center p-2 rounded-full ml-2 text-gray-600 hover:bg-gray-100 focus:outline-none"
                        >
                            <i class="pi pi-filter"></i>
                        </button>
                    </div>
                </th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="row in rows" :key="row.id">
                <td v-for="col in columns" :key="col.field + row.id.toString()"
                    class="px-4 py-3 border-b border-gray-200">
                    <component v-if="col.children.body" :is="col.children.body" :row="row"/>
                    <template v-else>{{ row[col.field] }}</template>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>
