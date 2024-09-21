<script setup>
const props = defineProps({
    label: { type: String, default: null },
})

const model = defineModel()
</script>

<template>
    <label class="inline-flex items-center">
        <input type="checkbox"
               v-model="model"
               class="form-checkbox h-4 w-4 accent-teal-600 rounded-md hover:ring hover:ring-gray-500 hover:ring-opacity-25 focus:ring focus:ring-teal-500 focus:ring-opacity-25">
        <slot><span class="ml-2 text-gray-700">{{ props.label }}</span></slot>
    </label>
</template>

<style scoped>

</style>
