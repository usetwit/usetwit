<script setup>
import { computed, defineProps, useTemplateRef } from 'vue'

const props = defineProps({
    disabled: { type: Boolean, default: false },
    invalid: { type: Boolean, default: false },
});

const setClasses = computed(() => {
    const disabled = 'bg-gray-100 dark:bg-gray-600 border-gray-400 cursor-not-allowed'
    const invalid = 'bg-white dark:bg-gray-900 border-red-600 focus:outline-red-600/50 hover:border-red-500'
    const normal = 'bg-white dark:bg-gray-900 border-gray-400 hover:border-gray-500 dark:hover:border-gray-300 focus:outline-slate-400/50'

    return props.disabled ? disabled : props.invalid ? invalid : normal
});

const model = defineModel()

const inputElement = useTemplateRef('input')

defineExpose({
    inputElement,
})
</script>

<template>
    <input type="text"
           class="ring-0 outline-offset-0 focus:outline focus:invalid:outline-red-600/50 hover:invalid:border-red-500 invalid:border-red-600 focus:invalid:border-red-600 leading-5 border align-middle py-2 px-2 text-gray-800 dark:text-gray-300"
           :class="setClasses"
           :disabled="disabled"
           v-model="model"
           ref="input"
    >
</template>
