<script setup>
import { computed, inject } from 'vue'
import useDropdown from '../../composables/useDropdown.js'
import Checkbox from '../Form/Checkbox.vue'

const columns = defineModel()

const sorted = computed(() => {
    return columns.value.sort((a, b) => a.label.localeCompare(b.label))
})

const { clearFilter, clearSort, filter, getSortedFields, getFilteredFields } = inject('tableInstance')

const clearFilterAndSort = field => {
    const fetch = getSortedFields().includes(field) || getFilteredFields().includes(field)

    clearSort(field)
    clearFilter(field)
    filter(fetch)
}

const {
    inputRef,
    dropdownStyle,
    showDropdown,
    toggleDropdown,
} = useDropdown()
</script>

<template>
    <button type="button"
            ref="inputRef"
            class="p-1.5 rounded inline-flex items-center bg-slate-800 text-white hover:bg-slate-700"
            @click="toggleDropdown"
            title="Select Columns"
    >
        <i class="pi pi-bars"></i>
    </button>
    <Teleport to="body">
        <div v-if="showDropdown"
             ref="dropdownRef"
             class="rounded absolute z-50 bg-white shadow border-gray-200 border flex flex-col overflow-y-auto p-1 min-w-64 max-h-80"
             :style="dropdownStyle"
        >
            <ul>
                <li v-for="col in sorted" :key="col.field" class="text-sm">
                    <Checkbox :label="col.label"
                              :id="col.field"
                              v-model="col.visible"
                              @update:model-value="clearFilterAndSort(col.field)"
                              class="select-none w-full px-3 py-1.5"
                    />
                </li>
            </ul>
        </div>
    </Teleport>
</template>
