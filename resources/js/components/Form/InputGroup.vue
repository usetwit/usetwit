<template>
    <div class="input-group inline-flex">
        <slot></slot>
    </div>
</template>

<style scoped>
.input-group > :deep(div:first-child) {
    @apply rounded-l-md border-l border-y border-gray-400;
}

.input-group > :deep(input:first-child) {
    @apply rounded-l-md;
}

.input-group > :deep(input:last-child) {
    @apply rounded-r-md;
}

.input-group > :deep(div:last-child) {
    @apply rounded-r-md border-r border-y border-gray-400;
}
</style>
