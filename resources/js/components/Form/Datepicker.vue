<script setup>
import { useDropdown } from '../../composables/useDropdown'
import { useDates } from "../../composables/useDates";
import { onMounted, useTemplateRef } from "vue";
import InputGroup from "./InputGroup.vue";
import InputText from "./InputText.vue";
import { DateTime } from "luxon";

const props = defineProps({
    disabled: { type: Boolean, default: false },
    dropdown: { type: Boolean, default: false },
    date: { type: DateTime, default: DateTime.utc() },
    numberOfMonths: { type: Number, default: 1 },
})

const { month, year, makeMonth, changeMonth, monthTexts, dayTexts, months } = useDates(props.date);
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
            <div class="flex month-wrapper w-max">
                <div v-for="(month, monthIndex) in months" :key="monthIndex">

                    <div v-if="monthIndex === 0" class="flex items-center justify-between text-sm border-b border-gray-200 p-1.5 mb-3 text-slate-700">
                        <button type="button" @click="changeMonth('decrease')"
                                class="mr-2 align-middle inline-flex">
                            <i class="pi pi-angle-left"></i>
                        </button>
                        <div class="flex justify-between items-center">
                            <button type="button">{{ monthTexts[month.month - 1] }}</button>
                            <button type="button">{{ year }}</button>
                        </div>
                        <button type="button" @click="changeMonth('increase')" class="ml-2 align-middle inline-flex">
                            <i class="pi pi-angle-right"></i>
                        </button>
                    </div>

                    <div v-else class="flex items-center justify-center text-sm border-b border-gray-200 p-1.5 mb-3 text-slate-700">
                        {{ monthTexts[month.month - 1] }} {{ month.year }}
                    </div>


                    <div class="grid grid-cols-7 w-full gap-2">
                    <span v-for="day in dayTexts" class="font-bold text-xs text-center text-gray-600">
                        {{ day }}
                    </span>
                        <template v-for="day in month.dates" :key="day">
                            <button v-if="day.month === month.month"
                                    class="text-gray-800 p-1.5 text-xs flex items-center justify-center rounded-full hover:bg-gray-100">
                                {{ day.day }}
                            </button>
                            <span v-else class="text-gray-300 p-1.5 text-xs flex items-center justify-center">
                            {{ day.day }}
                        </span>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<style scoped>
.month-wrapper > *:not(:first-child) {
    @apply ml-3 pl-3 border-l border-gray-200;
}
</style>
