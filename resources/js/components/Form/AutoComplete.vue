<script setup>
import { useDropdownPosition } from '../../composables/useDropdownPosition'
import InputText from './InputText.vue'
import InputGroup from './InputGroup.vue'
import { onMounted, ref } from 'vue'

const props = defineProps({
    items: { type: Array, default: [] },
    dropdown: { type: Boolean, default: false },
})

const {
    inputRef,
    dropdownRef,
    buttonRef,
    dropdownStyle,
    showDropdown,
    toggleDropdown
} = useDropdownPosition('left', 'bottom', false, 400)

const model = defineModel()

const inputTextRef = ref(null)

onMounted(() => {
    if (inputTextRef.value?.inputElement instanceof HTMLElement) {
        inputRef.value = inputTextRef.value.inputElement
    }
})
</script>

<template>
    <div class="inline-block">
        <InputGroup>
            <InputText v-model="model" ref="inputTextRef" style="width: 600px"/>
            <button v-if="props.dropdown"
                    class="inline-flex bg-gray-200 text-gray-700 items-center py-2.5 px-3 align-middle"
                    @click="toggleDropdown"
                    type="button"
                    ref="buttonRef"
            >
                <i class="pi pi-chevron-down"></i>
            </button>
        </InputGroup>

        <Teleport to="body">
            <div v-if="showDropdown"
                 ref="dropdownRef"
                 class="rounded absolute z-40 bg-white shadow border-gray-400 border p-5"
                 :style="dropdownStyle"
            >
                ffffffffffffff
            </div>
        </Teleport>
    </div>
</template>
