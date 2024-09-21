<script setup>
import { computed } from 'vue'
import { useDropdown } from "../../composables/useDropdown.js";
import Checkbox from "./Checkbox.vue";

const chips = defineModel()

const visible = computed(() => {
    return chips.value.filter(col => col.visible === true)
})

const sorted = computed(() => {
    return [...chips.value].sort((a, b) => a.label.localeCompare(b.label))
})

const removeChip = chip => {
    chip.visible = false
}

const {
    inputRef,
    dropdownStyle,
    showDropdown,
    toggleDropdown,
} = useDropdown('left', 'bottom', 200, 200)
</script>

<template>
    <fieldset class="overflow-x-auto flex border border-gray-200 text-gray-700 rounded-lg pb-2 px-1.5">
        <legend class="text-sm ml-1 px-1">Select Columns</legend>

        <button type="button"
                ref="inputRef"
                class="rounded-full bg-slate-800 text-white inline-flex items-center align-middle px-3 mx-0.5 text-sm hover:bg-slate-700"
                @click="toggleDropdown"
        >
            <i class="pi pi-bars mr-1"></i> Columns
        </button>
        <Teleport to="body">
            <div v-if="showDropdown"
                 ref="dropdownRef"
                 class="rounded absolute z-50 bg-white shadow border-gray-400 border flex flex-col overflow-y-auto w-max"
                 :style="dropdownStyle"
            >
                <ul>
                    <li v-for="chip in sorted" :key="chip.field" class="text-sm">
                        <Checkbox :label="chip.label" v-model="chip.visible" class="w-full px-2 py-1"/>
                    </li>
                </ul>
            </div>
        </Teleport>

        <span v-for="chip in visible"
              :key="chip.field"
              class="pl-2.5 pr-2 py-1 mx-0.5 text-sm inline-flex items-center bg-gray-200 text-gray-800 rounded-full text-nowrap"
        >
            {{ chip.label }}
            <button type="button"
                    @click="removeChip(chip)"
                    class="inline-flex items-center ml-0.5 p-1 text-sm hover:bg-gray-100 rounded-full"
            >
                <i class="pi pi-times-circle"></i>
            </button>
        </span>
    </fieldset>
</template>
