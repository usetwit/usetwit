<script setup>
import { useMenuStore } from '../stores/menuStore.js'
import useStorage from '../composables/useStorage'

const store = useMenuStore()

const props = defineProps({
    logoutRoute: { type: String, required: true },
    icon: { type: String, required: true },
})

const makeMenuVisible = () => {
    store.isMenuVisible = true
    document.body.classList.add('overflow-hidden')
}

const { activeData: darkMode, set } = useStorage('dark-mode', false)

const toggleDarkMode = () => {
    darkMode.value = !darkMode.value

    if (darkMode.value) {
        document.body.classList.add('dark')
    } else {
        document.body.classList.remove('dark')
    }

    set()
}

if (darkMode.value) {
    document.body.classList.add('dark')
}

const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
</script>


<template>
    <div class="flex justify-between items-center bg-slate-800 text-white px-4 py-3">
        <div class="flex items-center align-middle">
            <button class="mr-4" v-if="!store.isLargeScreen" @click="makeMenuVisible" :title="store.isMenuVisible ? 'Hide Menu' : 'Show Menu'">
                <i class="pi pi-bars"></i>
            </button>
            <a href="#" class="flex items-center text-lg font-semibold text-slate-300">
                <img class="w-8 h-8 mr-1" :src="icon" alt="useTwit">
                useTwit
            </a>
        </div>
        <div class="flex items-center">
            <button @click="toggleDarkMode" :title="darkMode ? 'Light Mode' : 'Dark Mode'">
                <i v-if="darkMode" class="pi pi-sun"></i>
                <i v-else class="pi pi-moon"></i>
            </button>
            <form :action="logoutRoute" method="post">
                <input type="hidden" name="_token" :value="csrf">
                <button type="submit"><i class="pi pi-sign-out"></i></button>
            </form>
        </div>
    </div>
</template>

<style scoped lang="postcss">
    button {
        @apply p-2 ml-1;
    }
</style>
