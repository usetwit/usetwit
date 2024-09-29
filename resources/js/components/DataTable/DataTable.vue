<script setup>
import { ref, provide, computed, inject } from 'vue'
import HeaderCell from './HeaderCell.vue'
import ColumnSelect from './ColumnSelect.vue'
import Button from '../Form/Button.vue'
import Paginator from './Paginator.vue'

const props = defineProps({
    rows: { type: Array, required: true },
    isLoading: { type: Boolean, default: false },
    paginationSettings: { type: Object, required: true },
    dateSettings: { type: Object },
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

provide('dateSettings', props.dateSettings)

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

const { save, filter, getFilteredFields, reset, clearFilters } = inject('tableInstance')
</script>

<template>
    <slot/>
    <ColumnSelect v-model="activeData.columns"/>

    <Button :badge="getFilteredFields().length" @click="reset" icon="pi pi-refresh" label="Reset Filters"
            :loading="isLoading"/>
    <Button :badge="getFilteredFields().length" @click="clearFilters" variant="secondary" border
            icon="pi pi-filter-slash" label="Clear Filters" class="ml-2" :loading="isLoading"/>

    <Paginator v-model="activeData.pagination" :settings="paginationSettings.per_page" @change="save" class="mt-8"/>

    <div class="my-2 overflow-x-auto relative">
        <table class="min-w-full">
            <thead>
            <tr>
                <HeaderCell v-for="col in orderedColumns" v-model="activeData" :column="col"
                            @sort="$emit('sort');save()" @filter="$emit('filter');filter()"/>
            </tr>
            </thead>
            <tbody>
            <tr v-if="rows.length"
                v-for="(row, i) in rows"
                :key="row.id || i"
                class="hover:bg-gray-100 body-row"
                :class="{'even': i % 2 === 1, 'odd': i % 2 === 0}"
            >
                <td v-for="col in orderedColumns"
                    :key="col.field + '_' + row.id.toString()"
                    class="px-4 py-3 border-b border-gray-200"
                    :class="{'sticky left-0': col.sticky}"
                    v-bind="col.attributes"
                >
                    <component v-if="col.body" :is="col.body" :row="row"/>
                    <template v-else>{{ row[col.field] }}</template>
                </td>
            </tr>
            <tr v-else>
                <td class="text-center px-4 py-3 border-b border-gray-200 bg-white italic" :colspan="columns.size">
                    No Results
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

    <Paginator v-model="activeData.pagination" :settings="paginationSettings.per_page" @change="save"/>
</template>

<style scoped lang="postcss">
.even td {
    @apply bg-gray-50
}

.odd td {
    @apply bg-white
}

.body-row:hover td {
    @apply bg-slate-100
}
</style>
