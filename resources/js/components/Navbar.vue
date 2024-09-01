<script setup>
import { useMenuStore } from "../stores/menuStore.js";
import { useStorage } from '../composables/useStorage';

const store = useMenuStore()

const makeMenuVisible = () => {
    store.isMenuVisible = true
    document.body.classList.add('overflow-hidden')
}

const { activeData: darkMode, saveToStorage } = useStorage('dark-mode', false);

const toggleDarkMode = () => {
    darkMode.value = !darkMode.value;

    if (darkMode.value) {
        document.body.classList.add('dark');
    } else {
        document.body.classList.remove('dark');
    }

    saveToStorage()
};

if (darkMode.value) {
    document.body.classList.add('dark');
}
</script>


<template>
    <div class="text-right h-20 bg-slate-800 text-white">
        <button @click="toggleDarkMode">Dark Mode</button>
        <button v-if="!store.isLargeScreen" @click="makeMenuVisible">Hamburger</button>
    </div>
</template>
