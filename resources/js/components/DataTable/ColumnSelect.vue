<script setup>
import { computed } from 'vue'
import { useDropdown } from "../../composables/useDropdown.js";
import Checkbox from "../Form/Checkbox.vue";

const columns = defineModel()

const visible = computed(() => {
    return columns.value.filter(col => col.visible === true)
})

const sorted = computed(() => {
    return [...columns.value].sort((a, b) => a.label.localeCompare(b.label))
})

const removeCol = col => {
    col.visible = false
}

const {
    inputRef,
    dropdownStyle,
    showDropdown,
    toggleDropdown,
} = useDropdown('left', 'bottom', 200, 200)
</script>

<template>
    <div class="pb-4 overflow-x-auto">
        <fieldset class="flex border border-gray-200 text-gray-700 rounded-lg pb-2 px-1.5">
            <legend class="text-sm ml-1 px-1">Select Columns</legend>

            <button type="button"
                    ref="inputRef"
                    class="rounded-full bg-slate-800 text-white inline-flex items-center align-middle px-3 mx-0.5 text-sm hover:bg-slate-700"
                    @click="toggleDropdown"
            >
                <i class="pi pi-bars mr-2"></i>Columns
            </button>
            <Teleport to="body">
                <div v-if="showDropdown"
                     ref="dropdownRef"
                     class="rounded absolute z-50 bg-white shadow border-gray-200 border flex flex-col overflow-y-auto w-max p-1"
                     :style="dropdownStyle"
                >
                    <ul>
                        <li v-for="col in sorted" :key="col.field" class="text-sm">
                            <Checkbox :label="col.label" :id="col.field" v-model="col.visible"
                                      class="w-full px-3 py-1.5"/>
                        </li>
                    </ul>
                </div>
            </Teleport>

            <span v-for="col in visible"
                  :key="col.field"
                  class="pl-2.5 pr-2 py-1 mx-0.5 text-sm inline-flex items-center bg-gray-200 text-gray-800 rounded-full text-nowrap"
            >
            {{ col.label }}
            <button type="button"
                    @click="removeCol(col)"
                    class="inline-flex items-center ml-0.5 p-1 text-sm hover:bg-gray-100 rounded-full"
            >
                <i class="pi pi-times-circle"></i>
            </button>
        </span>
        </fieldset>
    </div>
</template>
