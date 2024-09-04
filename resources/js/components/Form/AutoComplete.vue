<script setup>
import { useDropdown } from '../../composables/useDropdown'
import InputText from './InputText.vue'
import InputGroup from './InputGroup.vue'
import { nextTick, onMounted, useTemplateRef } from 'vue'

const props = defineProps({
    items: { type: Array, default: [] },
    dropdown: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    positionX: { type: String, default: 'left' },
    positionY: { type: String, default: 'bottom' },
    minWidth: { type: [Number, String, Boolean], default: null },
    maxHeight: { type: [Number, String], default: null },
    optionLabel: { type: String, required: true },
    optionGroupLabel: { type: String, default: '' },
    optionGroupItems: { type: String, default: '' },
})

const {
    inputRef,
    dropdownStyle,
    showDropdown,
    toggleDropdown
} = useDropdown(props.positionX, props.positionY, props.minWidth, props.maxHeight)

const model = defineModel()
const inputTextRef = useTemplateRef('inputTextComp')

onMounted(() => {
    if (inputTextRef.value?.inputElement instanceof HTMLElement) {
        inputRef.value = inputTextRef.value.inputElement
    }
})

const itemSelected = (item) => {
    model.value = item[props.optionLabel]
    showDropdown.value = false
}

const handleInput = () => {
    nextTick(() => {
        showDropdown.value = model.value.length > 0
    })
}
</script>

<template>
    <div class="inline-block">
        <InputGroup>
            <InputText ref="inputTextComp" v-model="model" :disabled="props.disabled" @input="handleInput"/>
            <button v-if="props.dropdown"
                    class="inline-flex bg-gray-200 hover:bg-gray-100 text-gray-700 items-center py-2.5 px-3 align-middle"
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
                 class="rounded absolute z-50 bg-white shadow border-gray-400 border flex flex-col overflow-y-auto"
                 :style="dropdownStyle"
            >
                        <ul v-if="!props.optionGroupLabel && items.length">
                            <li v-for="item in items"
                                @click="itemSelected(item)"
                                class="flex cursor-pointer hover:bg-gray-100 text-gray-700 items-center px-2 py-1.5 mx-1 rounded text-nowrap"
                            >
                                <slot name="item" v-bind="item">{{ item[props.optionLabel] }}</slot>
                            </li>
                        </ul>
                        <ul v-else-if="props.optionGroupLabel && items.length">
                            <li v-for="item in items">
                                <slot name="optiongroup" v-bind="item">
                                    <div class="font-bold px-2 py-1.5 mx-1 rounded">{{
                                            item[props.optionGroupLabel]
                                        }}
                                    </div>
                                </slot>
                                <ul v-if="item[props.optionGroupItems] && item[props.optionGroupItems].length">
                                    <li v-for="subitem in item[props.optionGroupItems]"
                                        @click="itemSelected(subitem)"
                                        class="flex cursor-pointer hover:bg-gray-100 text-gray-700 items-center px-2 py-1.5 mx-1 rounded text-nowrap"
                                    >
                                        <slot name="item" v-bind="subitem">{{ subitem[props.optionLabel] }}</slot>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                        <div v-else class="italic px-2 py-1.5 mx-1">
                            No Results
                        </div>
                    </div>
        </Teleport>
    </div>
</template>
