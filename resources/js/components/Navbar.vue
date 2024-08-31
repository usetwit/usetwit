<script setup>
import {useMenuStore} from "../stores/menuStore.js";

const store = useMenuStore()

const makeMenuVisible = () => {
    store.isMenuVisible = true
    document.body.classList.add('overflow-hidden')
}
</script>


<template>
    <div class="text-right h-20 bg-slate-800 text-white">
        <button v-if="!store.isLargeScreen" @click="makeMenuVisible">Hamburger</button>
    </div>
</template>
