<script setup>
import { useDropdownPosition } from '../../composables/useDropdownPosition'
import InputText from './InputText.vue'
import InputGroup from './InputGroup.vue'
import { onMounted, ref } from 'vue'

const props = defineProps({
    items: { type: Array, default: [] },
    dropdown: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    positionX: { type: String, default: 'left' },
    positionY: { type: String, default: 'bottom' },
    minWidth: { type: [Number, Boolean], default: null },
    maxHeight: { type: Number, default: null },
    optionLabel: { type: String, required: true },
    optionGroupLabel: { type: String, default: null },
    optionGroupItems: { type: String, default: null },
})

const {
    inputRef,
    dropdownRef,
    buttonRef,
    dropdownStyle,
    showDropdown,
    toggleDropdown
} = useDropdownPosition(props.positionX, props.positionY, props.minWidth, props.maxHeight)

const model = defineModel()

const inputTextRef = ref(null)

onMounted(() => {
    if (inputTextRef.value?.inputElement instanceof HTMLElement) {
        inputRef.value = inputTextRef.value.inputElement
    }
})

const x = (item) => {
    model.value = item[props.optionLabel]
}
</script>

<template>
    <div class="inline-block">
        <InputGroup>
            <InputText v-model="model" ref="inputTextRef" style="width: 600px" :disabled="props.disabled"/>
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
                 class="rounded absolute z-40 bg-white shadow border-gray-400 border flex flex-col"
                 :style="dropdownStyle"
            >
                <ul v-if="!props.optionGroupLabel && items.length">
                    <li v-for="item in items"
                        @click="x(item)"
                        class="flex cursor-pointer hover:bg-gray-100 text-gray-700 items-center p-2">
                        <slot name="item" v-bind="item">{{ item[props.optionLabel] }}</slot>
                    </li>
                </ul>
                <ul v-else-if="props.optionGroupLabel && items.length">
                    <li v-for="item in items">
                        <slot name="optiongroup" v-bind="item">
                            <div class="font-bold">{{ item[props.optionGroupLabel] }}</div>
                        </slot>
                        <ul v-if="item[props.optionGroupItems] && item[props.optionGroupItems].length">
                            <li v-for="subitem in item[props.optionGroupItems]">
                                <slot name="item" v-bind="subitem">{{ subitem[props.optionLabel] }}</slot>
                            </li>
                        </ul>
                    </li>
                </ul>
                <div v-else>
                    No Results
                </div>
            </div>
        </Teleport>
    </div>
</template>
