<script setup>
import { useDropdownPosition } from "../../composables/useDropdownPosition.js";
import InputText from "./InputText.vue";
import InputGroup from "./InputGroup.vue";
import { onMounted, ref, watch } from "vue";

const props = defineProps({
    items: { type: Array, default: [] },
    dropdown: { type: Boolean, default: false },
})

const { inputRef, dropdownStyle, showDropdown, toggleDropdown } = useDropdownPosition();

const model = defineModel()

const inputTextRef = ref(null);

onMounted(() => {
    if (inputTextRef.value?.inputElement instanceof HTMLElement) {
        inputRef.value = inputTextRef.value.inputElement;
    }
})
</script>

<template>
    <div class="relative bg-orange-500 inline-block">
        <InputGroup>
            <InputText v-model="model" ref="inputTextRef"/>
            <button v-if="props.dropdown"
                    class="inline-flex bg-gray-200 text-gray-700 items-center py-2.5 px-3 align-middle"
                    @click="toggleDropdown"
            >
                <i class="pi pi-chevron-down"></i>
            </button>
        </InputGroup>
        <div v-if="showDropdown" class="absolute">ffffffffffffff</div>
    </div>
</template>
