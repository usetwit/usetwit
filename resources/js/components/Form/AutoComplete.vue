<script setup>
import { useDropdownPosition } from "../../composables/useDropdownPosition.js";
import InputText from "./InputText.vue";
import { ref } from "vue";

defineProps({
    items: { type: Array, default: [] },
})

const { inputRef, dropdownStyle, showDropdown, toggleDropdown } = useDropdownPosition();

const model = defineModel()
</script>

<template>
    <InputText ref="inputRef" v-model="model"/>

    <div v-if="showDropdown"></div>
</template>
