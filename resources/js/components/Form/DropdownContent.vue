<script setup>

</script>

<template>
    <Teleport to="body">
        <div v-if="showDropdown"
             ref="dropdownRef"
             class="rounded absolute z-40 bg-white shadow border-gray-400 border p-5"
             :style="dropdownStyle"
        >
            <slot></slot>
        </div>
    </Teleport>
</template>

<style scoped>

</style>
