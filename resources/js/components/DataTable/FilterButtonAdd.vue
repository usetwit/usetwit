<script setup>
const props = defineProps({
    type: { type: String },
})
const model = defineModel()

const addConstraint = () => {
    const modeMapping = {
        number: 'equals',
        date: 'date_equals',
    }

    const mode = modeMapping[props.type] || 'contains'

    model.value.push({ value: null, mode })
}
</script>

<template>
    <button type="button"
            class="text-xs mt-1 rounded-md p-1 bg-white flex items-center justify-center text-blue-500 hover:bg-blue-100"
            @click.stop="addConstraint"
    >
        <i class="pi pi-plus mr-2"></i> Add Constraint
    </button>
</template>

<style scoped>

</style>
