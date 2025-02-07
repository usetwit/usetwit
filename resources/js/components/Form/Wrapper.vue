<script setup>
const props = defineProps({
    required: { type: Boolean, default: false },
})
</script>

<template>
    <div class="grid grid-cols-2 border-b border-gray-200 p-4 gap-1 sm:gap-2">
        <div class="text-slate-700 pt-1.5">
            <slot name="text"></slot>

            <sup v-if="required" class="text-red-500">required</sup>

            <div v-if="$slots.help" class="text-xs sm:text-sm text-gray-500">
                <slot name="help"/>
            </div>
        </div>
        <div>
            <slot name="input"></slot>
        </div>
    </div>
</template>

<style scoped lang="postcss">
@reference "../../../css/app.css";

:deep(label) {
    @apply font-bold text-sm lg:text-base;
}
</style>
