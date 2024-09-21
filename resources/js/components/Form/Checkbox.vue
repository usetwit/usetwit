<script setup>
const props = defineProps({
    label: { type: String, default: null },
    id: { type: String, default: null },
})

const model = defineModel()
</script>

<template>
    <label class="inline-flex items-center hover:bg-gray-100 rounded-lg" :for="props.id">
        <input type="checkbox"
               v-model="model"
               :name="props.id"
               :id="props.id"
               class="form-checkbox h-4 w-4 accent-teal-600 rounded-md hover:accent-teal-600 hover:ring hover:ring-gray-500 hover:ring-opacity-25 focus:ring focus:ring-teal-500 focus:ring-opacity-25">
        <slot><span class="ml-2 text-gray-700">{{ props.label }}</span></slot>
    </label>
</template>
