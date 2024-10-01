<script setup>
import { inject, nextTick, onBeforeUnmount, onMounted, provide, ref, useTemplateRef, watch } from 'vue'
import HeaderCell from '@/components/DataTable/HeaderCell.vue'
import Cell from '@/components/DataTable/Cell.vue'
import Button from '@/components/Form/Button.vue'
import Paginator from '@/components/DataTable/Paginator.vue'
import InputText from '@/components/Form/InputText.vue'
import InputGroup from '@/components/Form/InputGroup.vue'
import InputGroupAddon from '@/components/Form/InputGroupAddon.vue'

const props = defineProps({
    isLoading: { type: Boolean, default: false },
    paginationSettings: { type: Object, required: true },
    dateSettings: { type: Object },
})

const columnSet = ref(new Set())
const columns = ref([])
const activeData = defineModel()
const rows = defineModel('rows', {required: true, default: []})
const style = ref({ top: 0, height: 0 })
const resizeLeftStyle = ref(null)

const tableRef = useTemplateRef('tableRef')

const updateStyle = () => {
    style.value = {
        top: String(tableRef.value.getBoundingClientRect().top + window.scrollY) + 'px',
        height: tableRef.value.getBoundingClientRect().height.toString() + 'px',
    }
}

onMounted(() => {
    updateStyle()
    window.addEventListener('scroll', updateStyle)
})

onBeforeUnmount(() => {
    window.removeEventListener('scroll', updateStyle)
})

provide('registerColumn', column => {
    columnSet.value.add(column)
})

provide('deregisterColumn', column => {
    columnSet.value.delete(column)
})

provide('dateSettings', props.dateSettings)

watch(columnSet, (newValue) => {
    columns.value = Array.from(newValue).sort((a, b) => {
        if (a.order === undefined && b.order !== undefined) {
            return -1
        } else if (a.order !== undefined && b.order === undefined) {
            return 1
        }

        return a.order - b.order
    })
}, { deep: true })

watch(rows, () => {
    nextTick(()=>updateStyle())
})

const { fetch, filter, getFilteredFields, reset, clearFilters } = inject('tableInstance')
</script>

<template>
    <slot/>
<Flash/>
    <div class="flex justify-between items-start sm:flex-row flex-col">
        <div>
            <Button :badge="getFilteredFields().length"
                    @click="reset"
                    class="mr-2"
                    icon="pi pi-refresh"
                    label="Reset Filters"
                    :loading="isLoading"
            />
            <Button :badge="getFilteredFields().length"
                    @click="clearFilters"
                    variant="secondary"
                    border
                    icon="pi pi-filter-slash"
                    label="Clear Filters"
                    :loading="isLoading"
            />
        </div>
        <InputGroup class="sm:mt-0 mt-2 w-full sm:w-80">
            <InputText v-model="activeData.filters.global.constraints[0].value"
                       placeholder="Search..."
                       @input="filter"
                       class="w-full"
            />
            <InputGroupAddon>
                <i class="pi pi-search"></i>
            </InputGroupAddon>
        </InputGroup>
    </div>

    <Paginator v-model="activeData.pagination" :settings="paginationSettings.per_page" @changed="fetch" class="mt-8"/>

    <div class="my-3 overflow-x-auto relative">

        <Teleport to="body" v-if="resizeLeftStyle">
            <div class="absolute w-[2px] bg-slate-500 z-[999]" :style="[resizeLeftStyle, style]"></div>
        </Teleport>

        <table ref="tableRef">
            <thead>
            <tr>
                <HeaderCell v-for="(col, i) in columns"
                            v-model="activeData"
                            v-model:resize-left-style="resizeLeftStyle"
                            :is-last="i === columns.length - 1"
                            :column="col"
                            :table="tableRef"
                            @sort="fetch"
                            @filter="filter"
                />
            </tr>
            </thead>
            <tbody>
            <tr v-if="rows.length"
                v-for="(row, i) in rows"
                :key="row.id || i"
                class="hover:bg-gray-100 body-row"
                :class="{'even': i % 2 === 1, 'odd': i % 2 === 0}"
            >
                <Cell v-for="col in columns"
                      :key="col.field + '_' + row.id.toString()"
                      :class="{'sticky left-0': col.sticky}"
                      v-bind="col.attrs"
                      :col="col"
                      :row="row"
                />
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

    <Paginator v-model="activeData.pagination" :settings="paginationSettings.per_page" @changed="fetch"/>
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
