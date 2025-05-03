<script setup>
import useDropdown from '@/composables/useDropdown.js';

const {
    inputRef,
    dropdownStyle,
    showDropdown,
    toggleDropdown,
} = useDropdown('right', 'bottom', false);

const emit = defineEmits(['selected']);

const colors = ['blue', 'green', 'orange', 'pink', 'purple', 'red', 'teal', 'yellow'];

const selectColor = (color) => {
    emit('selected', color);
    showDropdown.value = false;
};

defineOptions({
    inheritAttrs: false,
});
</script>

<template>
    <button ref="inputRef"
            class="bg-amber-200 text-amber-700 inline-flex items-center px-2 py-1 rounded-md disabled:bg-gray-300 disabled:text-gray-500"
            @click="toggleDropdown"
            v-bind="$attrs"
    >
        <i class="pi pi-palette mr-1"></i>Color
    </button>

    <Teleport to="body" v-if="showDropdown">
        <div ref="dropdownRef"
             class="min-w-[100px] dropdown z-350 bg-white shadow-lg rounded-md p-2"
             :style="dropdownStyle"
        >
            <div class="grid grid-cols-4 gap-2">
                <button v-for="color in colors" :key="color"
                        :class="`w-8 h-8 rounded-md border border-gray-400 hover:border-gray-500 bg-${color}-300`"
                        @click="selectColor(color)"
                ></button>
            </div>
        </div>
    </Teleport>
</template>
