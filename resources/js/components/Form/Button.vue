<script setup>
import { computed, useSlots } from "vue"

const props = defineProps({
    loading: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    icon: { type: String, default: null },
    label: { type: String, default: null },
    severity: { type: String, default: 'primary' },
    border: { type: Boolean, default: false },
    badge: { type: [String, Number], default: null },
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

const severityClasses = {
    normal: {
        normal: {
            primary: 'text-white bg-slate-800 border-slate-800 hover:bg-slate-700 hover:border-slate-700 dark:hover:bg-slate-100 dark:hover:border-slate-100 dark:bg-slate-200 dark:border-slate-200 dark:text-slate-900',
            secondary: 'text-white bg-gray-600 border-gray-600 hover:bg-gray-500 hover:border-gray-500',
            success: 'text-white bg-green-500 border-green-500 hover:bg-green-400 hover:border-green-400',
            warning: 'text-white bg-yellow-500 border-yellow-500 hover:bg-yellow-400 hover:border-yellow-400',
            danger: 'text-white bg-red-500 border-red-500 hover:bg-red-400 hover:border-red-400',
        },
        loading: {
            primary: 'text-white bg-slate-700 border-slate-700 dark:bg-slate-200 dark:border-slate-200 dark:text-slate-900',
            secondary: 'text-white bg-gray-500 border-gray-500',
            success: 'text-white bg-green-400 border-green-400',
            warning: 'text-white bg-yellow-400 border-yellow-400',
            danger: 'text-white bg-red-400 border-red-400',
        },
    },

    border: {
        normal: {
            primary: 'dark:text-slate-50 text-slate-500 bg-slate-100 border-slate-500 hover:bg-slate-50 dark:bg-slate-700 dark:hover:bg-slate-600',
            secondary: 'dark:text-gray-50 text-gray-500 bg-gray-100 border-gray-500 hover:bg-gray-50 dark:bg-gray-700 dark:hover:bg-gray-600',
            success: 'dark:text-green-50 text-green-500 bg-green-100 border-green-500 hover:bg-green-50 dark:bg-green-700 dark:hover:bg-green-600',
            warning: 'dark:text-yellow-50 text-yellow-500 bg-yellow-100 border-yellow-500 hover:bg-yellow-50 dark:bg-yellow-700 dark:hover:bg-yellow-600',
            danger: 'dark:text-red-50 text-red-500 bg-red-100 border-red-500 hover:bg-red-50 dark:bg-red-700 dark:hover:bg-red-600',
        },
        loading: {
            primary: 'dark:text-slate-50 text-slate-500 bg-slate-50 border-slate-500 dark:bg-slate-600',
            secondary: 'dark:text-gray-50 text-gray-500 bg-gray-50 border-gray-500 dark:bg-gray-600',
            success: 'dark:text-green-50 text-green-500 bg-green-50 border-green-500 dark:bg-green-600',
            warning: 'dark:text-yellow-50 text-yellow-500 bg-yellow-50 border-yellow-500 dark:bg-yellow-600',
            danger: 'dark:text-red-50 text-red-500 bg-red-50 border-red-500 dark:bg-red-600',
        },
    }
}

const setClasses = computed(() => {
    const severity = props.severity in severityClasses.normal.normal ? props.severity : 'primary'

    if (props.border) {
        return props.loading ? `${severityClasses.border.loading[severity]} cursor-not-allowed` : severityClasses.border.normal[severity]
    } else {
        return props.loading ? `${severityClasses.normal.loading[severity]} cursor-not-allowed` : severityClasses.normal.normal[severity]
    }
})
</script>

<template>
    <button class="border rounded leading-3 py-2.5 px-3 inline-flex justify-center items-center"
            :class="setClasses"
            :aria-label="ariaLabel"
            :disabled="disabled || loading"
            :aria-disabled="disabled || loading"
    >
        <i v-if="loading" class="pi pi-spin pi-spinner-dotted mr-2"></i>
        <i v-if="props.icon && !loading" :class="props.icon" class="mr-2"></i>
        <span v-if="label || slots.default">{{ label }}<slot></slot></span>
        <span v-if="props.badge"
              class="ml-2 flex items-center justify-center h-4 p-1 min-w-4 bg-slate-500 text-white text-xs rounded-full">
            {{ props.badge }}
        </span>
    </button>
</template>
