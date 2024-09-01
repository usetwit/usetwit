<script setup>
import Button from './Form/Button.vue'
import { ref } from "vue";
import InputText from "./Form/InputText.vue";

const x = ref(false)
const change = () => {
    x.value = !x.value
}

</script>

<template>
    <form class="form-horizontal">
        <InputText value="Hi" class="rounded" :disabled="x"/>

        <Button type="button" icon="pi pi-save" :loading="x" label="Save" badge="9" severity="primary"></Button>
    </form>
</template>
