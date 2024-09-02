<script setup>
import { useDropdownPosition } from "../../composables/useDropdownPosition.js";
import InputText from "./InputText.vue";
import InputGroup from "./InputGroup.vue";

const props = defineProps({
    items: { type: Array, default: [] },
    dropdown: { type: Boolean, default: false },
})

const { inputRef, dropdownStyle, showDropdown, toggleDropdown } = useDropdownPosition();

const model = defineModel()
</script>

<template>
    <InputGroup>
        <InputText ref="inputRef" v-model="model"/>
        <button v-if="props.dropdown"
                class="inline-flex bg-gray-200 text-gray-700 items-center py-2.5 px-3 align-middle"
                @click="toggleDropdown">
            <i class="pi pi-chevron-down"></i>
        </button>
    </InputGroup>

    <div v-if="showDropdown">ffffffffffffff</div>
</template>
