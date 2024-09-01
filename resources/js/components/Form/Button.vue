<script setup>
import { computed, useSlots } from "vue"

const props = defineProps({
    loading: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    icon: { type: String, default: null },
    label: { type: String, default: null },
    severity: { type: String, default: 'primary' },
    border: { type: Boolean, default: false }
})

const slots = useSlots()
const ariaLabel = computed(() => {
    if (props.label) {
        return props.label
    }

    if (slots.default) {
        return slots.default()[0].children
    }
})

const severityClasses = computed(() => {
    const normalClass = {
        normal: {
            primary: 'bg-slate-700 border-slate-700 hover:bg-slate-600',
            secondary: 'bg-gray-600 border-gray-600 hover:bg-gray-500',
            success: 'bg-green-600 border-green-600 hover:bg-green-500',
            warning: 'bg-yellow-500 border-yellow-500 hover:bg-yellow-400',
            danger: 'bg-red-600 border-red-600 hover:bg-red-500',
        },
        loading: {
            primary: 'bg-slate-600 border-slate-600',
            secondary: 'bg-gray-500 border-gray-500',
            success: 'bg-green-400 border-green-400',
            warning: 'bg-yellow-400 border-yellow-400',
            danger: 'bg-red-400 border-red-400',
        },
        base: 'text-white',
    };

    const borderClass = {
        normal: {
            primary: 'dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 bg-white border-slate-700 text-slate-700',
            secondary: 'dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 bg-white border-gray-500 text-gray-500',
            success: 'dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 bg-white border-green-600 text-green-600',
            warning: 'dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 bg-white border-yellow-600 text-yellow-600',
            danger: 'dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 bg-white border-red-600 text-red-600',
        },
        loading: {
            primary: 'bg-slate-100 border-slate-700 text-slate-700',
            secondary: 'bg-gray-100 border-gray-500 text-gray-500',
            success: 'bg-slate-100 border-green-600 text-green-600',
            warning: 'bg-slate-100 border-yellow-600 text-yellow-600',
            danger: 'bg-slate-100 border-red-600 text-red-600',
        },
    };

    const severity = props.severity in normalClass.normal ? props.severity : 'primary';

    if (props.border) {
        return props.loading ? borderClass.loading[severity] : borderClass.normal[severity]
    } else {
        if (props.loading) {
            return `${normalClass.loading[severity]} ${normalClass.base}`
        } else {
            return `${normalClass.normal[severity]} ${normalClass.base}`
        }
    }
})
</script>

<template>
    <button class="border rounded leading-3 py-3 px-3 inline-flex justify-center items-center"
            :class="severityClasses"
            :aria-label="ariaLabel"
            :disabled="disabled || loading"
            :aria-disabled="disabled"
    >
        <i v-if="loading" class="pi pi-spin pi-spinner mr-2"></i>
        <i v-if="props.icon && !loading" :class="props.icon" class="mr-2"></i>
        <span v-if="label">{{ label }}</span>
        <span v-else><slot></slot></span>
    </button>
</template>

<style scoped>

</style>
