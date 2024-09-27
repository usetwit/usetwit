<script setup>
import { useDropdown } from '../../composables/useDropdown.js'
import Select from '../Form/Select.vue'
import InputText from "../Form/InputText.vue";
import FilterSelectOperator from "./FilterSelectOperator.vue";
import FilterButtonRemove from "./FilterButtonRemove.vue";
import FilterButtonAdd from "./FilterButtonAdd.vue";
import FilterSelectMode from "./FilterSelectMode.vue";

const props = defineProps({
    column: { type: Object, required: true },
    sortObj: { type: Object, default: null },
})


const filters = defineModel()

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
             class="rounded absolute z-50 bg-white shadow border-gray-200 border flex flex-col overflow-y-auto p-1 min-w-64 max-h-80"
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
                <FilterButtonAdd v-model="filters[column.field].constraints" type="string"/>
            </template>
        </div>
    </Teleport>
</template>
