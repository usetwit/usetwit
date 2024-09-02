<script setup>
import AutoComplete from "./Form/AutoComplete.vue";
import { ref } from "vue";
import InputGroup from "./Form/InputGroup.vue";
import InputGroupAddon from "./Form/InputGroupAddon.vue";
import InputText from "./Form/InputText.vue";

const items = ref([
    {
        name: 'Lee', id: 1,
    },
    {
        name: 'Sandra', id: 2,
    },
])

const value = ref()
</script>

<template>
    <div>
        <InputGroup>
            <InputText/>
            <InputGroupAddon>
                <i class="pi pi-save"></i>
            </InputGroupAddon>
        </InputGroup>
    </div>
    <div class="p-4 relative">
        <AutoComplete v-model="value" :items="items"/>
    </div>
</template>
