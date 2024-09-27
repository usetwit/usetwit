<script setup>
import { useDropdown } from '../../composables/useDropdown.js'
import { computed, ref } from 'vue'
import { isEqual } from 'lodash'

const props = defineProps({
    placeholder: { type: String, default: 'Select an option' },
    options: { type: Array, required: true },
    optionLabel: { type: String, required: true },
    optionValue: { type: String, default: null },
    isLoading: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    invalid: { type: Boolean, default: false },
    showClear: { type: Boolean, default: false },
    dropdownClasses: { type: String },
})

const model = defineModel()
const emit = defineEmits(['selected'])
defineOptions({
    inheritAttrs: false,
})

const text = computed(() => {
    if (typeof props.optionValue === 'string' && typeof model.value === 'string') {
        const initialValue = props.options.find(option => option[props.optionValue] === model.value)

        if (initialValue) {
            return initialValue[props.optionLabel]
        }
    } else if (model.value !== null && typeof model.value === 'object') {
        props.options.forEach(option => {
            if (isEqual(option, model.value)) {
                return option[props.optionLabel]
            }
        })
    }

    return props.placeholder
})

const {
    inputRef,
    dropdownStyle,
    showDropdown,
} = useDropdown('left', 'bottom', true)

const optionSelected = option => {
    if (props.optionValue) {
        model.value = option[props.optionValue]
    } else {
        model.value = option
    }

    showDropdown.value = false
    emit('selected', option)
}

const clear = () => {
    showDropdown.value = false
    model.value = null
}

const toggleDropdown = () => {
    showDropdown.value = !props.isLoading && !props.disabled && showDropdown.value === false
}

const setClasses = computed(() => {
    const disabled = 'bg-gray-100 dark:bg-gray-600 border-gray-400 cursor-not-allowed'
    const invalid = 'bg-white dark:bg-gray-900 border-red-600 focus:outline-red-600/50 hover:border-red-500'
    const normal = 'bg-white dark:bg-gray-900 border-gray-300 hover:border-gray-400 focus:outline-slate-400/50'

    return props.disabled ? disabled : props.invalid ? invalid : normal
})
</script>

<template>
    <div @click="toggleDropdown"
         class="inline-flex cursor-pointer bg-white rounded-md leading-5 border align-middle px-1 py-0.5 text-gray-800 dark:text-gray-300"
         :class="setClasses"
         ref="inputRef"
         v-bind="$attrs"
    >
        <span class="px-2 py-1.5 flex-1">{{ text }}</span>
        <span v-if="showClear && model && !isLoading" @click.stop="clear" class="inline-flex items-center p-2">
            <i class="pi pi-times"></i>
        </span>
        <span v-if="!isLoading" class="inline-flex items-center p-2"><i class="pi pi-angle-down"></i></span>
        <span v-if="isLoading" class="inline-flex items-center p-2"><i class="pi pi-spinner pi-spin"></i></span>
    </div>

    <Teleport to="body">
        <div v-if="showDropdown"
             ref="dropdownRef"
             class="rounded absolute z-[350] bg-white shadow border-gray-200 border flex flex-col overflow-y-auto p-1"
             :class="dropdownClasses"
             :style="dropdownStyle"
        >
            <ul v-if="options.length">
                <li v-for="option in options"
                    @click.stop="optionSelected(option)"
                    class="flex cursor-pointer hover:bg-gray-100 text-gray-700 items-center px-2 py-1.5 rounded text-nowrap"
                >
                    <slot name="option" :option="option">
                        {{ option[optionLabel] }}
                    </slot>
                </li>
            </ul>
        </div>
    </Teleport>
</template>
