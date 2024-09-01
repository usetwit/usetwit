<script setup>
import { computed, defineProps } from 'vue';

const props = defineProps({
    disabled: { type: Boolean, default: false },
    ariaLabel: { type: String, default: null },
    ariaLabelledby: { type: String, default: null },
    invalid: { type: Boolean, default: false },
    fluid: { type: Boolean, default: false },
});

const setClasses = computed(() => {
    const disabled ='bg-gray-100 dark:bg-gray-600 border-gray-200'
    const normal  = 'bg-white    dark:bg-gray-800 border-gray-600'
    const invalid = 'bg-white    dark:bg-gray-800 border-red-500'

    return props.disabled ? disabled : props.invalid ? invalid : normal
});

const setFluidClass = () => {
    return props.fluid ? 'w-full' : 'w-56';
}
</script>

<template>
    <input
        type="text"
        class="border text-gray-800 dark:text-gray-300 transition-colors duration-200 ease-in-out px-3 py-2 rounded"
        :class="[setClasses, setFluidClass]"
        :aria-labelledby="ariaLabelledby"
        :aria-label="ariaLabel"
        :disabled="disabled"
    />
</template>
