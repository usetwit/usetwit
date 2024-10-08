<script setup>
import useDropdown from '@/composables/useDropdown.js'
import { computed, onMounted, useTemplateRef } from 'vue'
import InputText from './InputText.vue'
import zxcvbn from 'zxcvbn'

defineOptions({
    inheritAttrs: false,
})

const {
    inputRef,
    dropdownStyle,
    showDropdown,
} = useDropdown('left', 'bottom', true)

const model = defineModel()
const inputTextRef = useTemplateRef('inputText')

onMounted(() => {
    if (inputTextRef.value?.inputElement instanceof HTMLElement) {
        inputRef.value = inputTextRef.value.inputElement
    }
})

const strength = computed(() => zxcvbn(model.value || '').score)
const width = computed(() => ((strength.value + 1) / 5 * 100) + '%')
const texts = ['Very weak', 'Weak', 'Fair', 'Good', 'Strong']
const strengthText = computed(() => !model.value ? 'Enter a password' : texts[strength.value])
</script>

<template>
    <InputText v-model="model"
               type="password"
               placeholder="••••••••"
               ref="inputText"
               class="rounded-md"
               v-bind="$attrs"
               @focus="showDropdown = true"
               @blur="showDropdown = false"
    />

    <Teleport to="body" v-if="showDropdown">
        <div ref="dropdownRef"
             class="dropdown z-[350] max-h-60"
             :style="dropdownStyle"
        >
            <div class="bg-gray-200 rounded-full h-4 m-2">
                <div :style="{'width': width}"
                     class="rounded-full h-4"
                     :class="{'bg-red-500': strength < 2, 'bg-amber-500': strength < 4, 'bg-green-500': strength === 4}"
                ></div>
            </div>
            <div class="mx-2 mb-2">{{ strengthText }}<i v-if="strength === 4" class="pi pi-face-smile ml-2"></i></div>
        </div>
    </Teleport>
</template>

<style scoped lang="postcss">

</style>
