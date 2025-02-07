<script setup>
const props = defineProps({
    tab: { type: Object, required: true },
    active: { type: Boolean, default: false },
    important: { type: Boolean, default: false },
})

const emit = defineEmits(['clicked'])

const handleClick = () => {
    window.location.hash = props.tab.key
    emit('clicked', props.tab)
}
</script>

<template>
    <li class="p-2 ml-1 text-sm lg:text-base z-25 flex items-center select-none"
        @click="handleClick"
        :class="{'active': active, 'inactive': !active, 'border-t-red-500': active && important, 'border-t-sky-600': active && !important}"
        v-html="tab.text"
    ></li>
</template>

<style scoped lang="postcss">
.active {
    @apply border-x border-b border-t-4 border-x-gray-200 border-b-white text-sky-600 bg-white;
}

.inactive {
    @apply border-x border-b border-t-4 border-x-gray-200 border-t-gray-200 border-b-gray-200 text-gray-700 hover:border-t-teal-500 hover:bg-white hover:border-b-white cursor-pointer bg-gray-50;
}
</style>
