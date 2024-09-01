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
    You are home<br><button type="button" @click="change">jjjjjjjjjjjjjjjj</button>

    <Button :loading="x" icon="pi pi-save" label="Save" severity="primary"></Button>
    <Button :loading="x" icon="pi pi-save" label="Save" severity="secondary"></Button>
    <Button :loading="x" icon="pi pi-save" label="Save" severity="warning"></Button>
    <Button :loading="x" icon="pi pi-save" label="Save" severity="success"></Button>
    <Button :loading="x" icon="pi pi-save" label="Save" severity="danger"></Button>
    <Button :loading="x" icon="pi pi-save" label="Save" border severity="primary"></Button>
    <Button :loading="x" icon="pi pi-save" label="Save" border severity="secondary"></Button>
    <Button :loading="x" icon="pi pi-save" label="Save" border severity="warning"></Button>
    <Button :loading="x" icon="pi pi-save" label="Save" border severity="success"></Button>
    <Button :loading="x" icon="pi pi-save" label="Save" border severity="danger"></Button>
    <br>
    <InputText value="Hi" :disabled="x"/>
</template>
