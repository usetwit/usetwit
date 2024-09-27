<script setup>
import { useDropdown } from '../../composables/useDropdown.js'
import InputText from '../Form/InputText.vue'
import FilterSelectOperator from './FilterSelectOperator.vue'
import FilterButtonRemove from './FilterButtonRemove.vue'
import FilterButtonAdd from './FilterButtonAdd.vue'
import FilterSelectMode from './FilterSelectMode.vue'
import FilterButtonClearApply from './FilterButtonClearApply.vue'
import { computed } from "vue";
import { useTable } from "../../composables/useTable.js";

const props = defineProps({
    column: { type: Object, required: true },
    sortObj: { type: Object, default: null },
    filtered: { type: Array, required: true },
})

const filters = defineModel()

const emit = defineEmits(['filter', 'apply'])

const {
    inputRef,
    dropdownStyle,
    showDropdown,
    toggleDropdown,
} = useDropdown('right')

defineExpose({ inputRef })

const apply = () => {
    showDropdown.value = false
    emit('filter')
}

const { getModifiedFields } = useTable()

const filtered = computed(() => props.filtered.includes(props.column.field))
const modified = computed(() => getModifiedFields(filters.value, props.filtered))
</script>

<template>
    {{ modified }}
    <button v-if="column.type"
            ref="inputRef"
            type="button"
            class="inline-flex items-center p-2 rounded-full ml-2"
            :class="{'text-gray-600 hover:bg-gray-200': !sortObj && !filtered, 'hover:bg-slate-600': sortObj, 'text-yellow-500': filtered, 'text-white': !filtered}"
            @click="toggleDropdown"
    >
        <i v-if="!filtered" class="pi pi-filter"></i>
        <i v-if="filtered" class="pi pi-filter-fill"></i>
    </button>

    <Teleport to="body">
        <div v-if="showDropdown"
             ref="dropdownRef"
             class="rounded absolute z-50 bg-white shadow border-gray-200 border flex flex-col overflow-y-auto p-1 w-64 max-h-80"
             :style="dropdownStyle"
        >
            <template v-if="column.type === 'string'">
                <FilterSelectOperator v-model="filters[column.field].operator"/>
                <template v-for="(constraint, i) in filters[column.field].constraints">
                    <FilterSelectMode type="string" v-model="constraint.mode"/>
                    <InputText v-model="constraint.value"
                               class="text-sm mt-1 rounded-md"
                               :placeholder="'Search by ' + column.label"
                    />
                    <FilterButtonRemove v-if="filters[column.field].constraints.length > 1"
                                        v-model="filters[column.field].constraints"
                                        :index="i"
                    />
                </template>
                <FilterButtonAdd v-model="filters[column.field].constraints" :type="column.type"/>
                <FilterButtonClearApply v-model="filters[column.field].constraints" :type="column.type"
                                        @clear="$emit('filter')" @apply="apply"/>
            </template>
            <template v-if="column.type === 'boolean'">

            </template>
        </div>
    </Teleport>
</template>
