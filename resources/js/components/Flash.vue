<script setup>
import {toast} from "vue3-toastify";

const props = defineProps({
    message: String,
    type: String,
})

toast(props.message, {
    type: props.type
})

</script>

<template>

</template>
