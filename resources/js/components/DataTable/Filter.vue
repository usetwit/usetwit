<script setup>
import { useDropdown } from '../../composables/useDropdown.js'
import Select from '../Form/Select.vue'
import InputText from "../Form/InputText.vue";

const props = defineProps({
    column: { type: Object, required: true },
    sortObj: { type: Object, default: null },
})

const operatorOptions = [
    { label: 'Match All', value: 'and' },
    { label: 'Match Any', value: 'or' },
]

const modeTexts = {
    contains: 'Contains',
    starts_with: 'Starts with',
    ends_with: 'Ends with',
    equals: 'Equals',
    not_equals: 'Does not equal',
    gt: 'Greater than',
    gte: 'Greater than or equal',
    lt: 'Less than',
    lte: 'Less than or equal',
}

let stringModes = [
    'contains',
    'starts_with',
    'ends_with',
    'equals',
    'not_equals',
    'gt',
    'gte',
    'lt',
    'lte',
]

stringModes = stringModes.map(mode => ({ mode: mode, label: modeTexts[mode] }))

const activeData = defineModel()

const {
    inputRef,
    dropdownStyle,
    showDropdown,
    toggleDropdown,
} = useDropdown('right')

defineExpose({ inputRef })
</script>

<template>
    <button v-if="column.type"
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
             class="rounded absolute z-50 bg-white shadow border-gray-200 border flex flex-col overflow-y-auto p-1 min-w-64"
             :style="dropdownStyle"
        >
            <template v-if="column.type === 'string'">
                <Select :options="operatorOptions"
                        v-model="activeData.filters[column.field].operator"
                        option-label="label"
                        option-value="value"
                        class="text-sm"
                >
                    <template #option="{ option }">
                        <span class="text-sm">{{ option.label }}</span>
                    </template>
                </Select>
                <template v-for="constraint in activeData.filters[column.field].constraints">
                    <Select :options="stringModes"
                            v-model="constraint.mode"
                            option-label="label"
                            option-value="mode"
                            class="mt-1 text-sm"
                            dropdown-classes="max-h-64"
                    >
                        <template #option="{ option }">
                            <span class="text-sm">{{ option.label }}</span>
                        </template>
                    </Select>
                    <InputText v-model="constraint.value" class="text-sm mt-1 rounded-md" />
                </template>
            </template>
        </div>
    </Teleport>
</template>
