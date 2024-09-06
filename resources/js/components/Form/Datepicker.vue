<script setup>
import { useDropdown } from '../../composables/useDropdown'
import { useDates } from "../../composables/useDates";
import '../../prototype-mods/date'
import { computed, onMounted, ref, useTemplateRef } from "vue";
import InputGroup from "./InputGroup.vue";
import InputText from "./InputText.vue";

const props = defineProps({
    disabled: { type: Boolean, default: false },
    dropdown: { type: Boolean, default: false },
    date: { type: Date, default: new Date() },
    numberOfMonths: { type: Number, default: 1 },
})

const { today, month, year, makeMonth, changeMonth, monthTexts, dayTexts, months } = useDates(props.date);
const { inputRef, dropdownStyle, showDropdown, toggleDropdown } = useDropdown('center', 'top')

const model = defineModel()

const inputTextRef = useTemplateRef('inputTextComp')

const handleInput = (e) => {

}

onMounted(() => {
    if (inputTextRef.value?.inputElement instanceof HTMLElement) {
        inputRef.value = inputTextRef.value.inputElement
    }
})
</script>

<template>
    <div class="inline-block">
        <InputGroup>
            <InputText ref="inputTextComp"
                       v-model="model"
                       :disabled="props.disabled"
                       @input="handleInput"
                       @focus="showDropdown = true"/>
            <button v-if="props.dropdown"
                    class="inline-flex bg-gray-200 hover:bg-gray-100 text-gray-700 items-center py-2.5 px-3 align-middle"
                    @click="toggleDropdown"
                    type="button"
                    ref="buttonRef"
            >
                <i class="pi pi-calendar"></i>
            </button>
        </InputGroup>
    </div>

    <Teleport to="body">
        <div v-if="showDropdown"
             ref="dropdownRef"
             class="rounded absolute z-50 bg-white shadow border-gray-400 border flex flex-col overflow-y-auto p-3"
             :style="dropdownStyle"
        >
            <div class="flex justify-between items-center pb-3 mb-3 border-b border-b-gray-200">
                <button type="button" @click="changeMonth('decrease')" class="mr-2 align-middle inline-flex">
                    <i class="pi pi-angle-left"></i>
                </button>
                <div class="flex justify-between items-center">
                    <button type="button">{{ monthTexts[month] }}</button>
                    <button type="button">{{ year }}</button>
                </div>
                <button type="button" @click="changeMonth('increase')" class="ml-2 align-middle inline-flex">
                    <i class="pi pi-angle-right"></i>
                </button>
            </div>

            <div class="flex">
                <div v-for="(month, monthIndex) in months" :key="monthIndex" class="grid grid-cols-7 w-full gap-2">
                    <template v-for="day in makeMonth(month.year, month.month, true, true)" :key="day">
                        <span v-if="day.getMonth() !== month.month" class="text-gray-500 p-1.5 text-sm">
                            {{ day.getDate() }}
                        </span>
                        <button v-else class="rounded-full text-gray-900 p-1.5 text-sm hover:bg-gray-100" :class="{'bg-slate-200': today(day)}">
                            {{ day.getDate() }}
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<style scoped>

</style>
