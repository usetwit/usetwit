<script setup>
import { useDropdown } from '../../composables/useDropdown.js'

const props = defineProps({
    column: { type: Object, required: true },
    sortObj: { type: [Object, null], required: true },
    type: { type: String, default: 'string' },
})

const {
    inputRef,
    dropdownStyle,
    showDropdown,
    toggleDropdown,
} = useDropdown('right')

defineExpose({ inputRef })
</script>

<template>
    <button v-if="column.filter"
            ref="inputRef"
            type="button"
            class="inline-flex items-center p-2 rounded-full ml-2"
            :class="{'text-gray-600 hover:bg-gray-200': !sortObj, 'text-gray-100 hover:bg-slate-600': sortObj}"
            @click="toggleDropdown"
    >
        <i class="pi pi-filter"></i>
    </button>

    <Teleport to="body">
        <div v-if="showDropdown"
             ref="dropdownRef"
             class="rounded absolute z-50 bg-white shadow border-gray-200 border flex flex-col overflow-y-auto p-1"
             :style="dropdownStyle"
        >
            ddddd
        </div>
    </Teleport>
</template>
