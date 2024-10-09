<script setup>
const props = defineProps({
    text: { type: String, required: true },
    active: { type: Boolean, default: false },
})
</script>

<template>
    <li class="p-2 ml-1 select-none text-sm lg:text-base -mb-[1px]"
        @click="$emit('clicked', text)"
        :class="{'active': active, 'inactive': !active}"
        v-html="text"
    ></li>
</template>

<style scoped lang="postcss">
.active {
    @apply border-x border-b border-t-4 border-x-gray-200 border-t-sky-600 border-b-white text-sky-600 bg-white;
}

.inactive {
    @apply border-x border-b border-t-4 border-x-gray-200 border-t-gray-200 border-b-gray-200 text-gray-700 hover:border-t-orange-500 hover:bg-white hover:border-b-white cursor-pointer bg-gray-50;
}
</style>
