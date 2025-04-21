<script setup>
import Wrapper from '@/components/Form/Wrapper.vue';
import InputText from '@/components/Form/InputText.vue';
import Textarea from '@/components/Form/Textarea.vue';
import {ref} from 'vue';
import Button from '@/components/Form/Button.vue';
import useAxios from '@/composables/useAxios.js';
import {toast} from 'vue3-toastify';
import {debounce} from 'lodash';
import Select from '@/components/Form/Select.vue';

const props = defineProps({
    bom: {type: Object, required: true},
    routes: {type: Object, required: true},
    versions: {type: Array, required: true},
});

const bom = ref(props.bom);
const isLoading = ref(false);
const errorFields = ref([]);
const nameExists = ref(false);

const checkName = async () => {
    if(bom.value.name) {
        const {data, getResponse} = useAxios(
            props.routes.check_name,
            {name: bom.value.name},
            'post',
        );

        await getResponse();

        nameExists.value = data.value.exists && props.bom.name !== bom.value.name;
    }
};

const save = async () => {
    isLoading.value = true;

    const {data, status, errors, getResponse} = useAxios(
        props.routes.update,
        {
            ...bom.value,
        },
        'patch',
    );
    await getResponse();

    if (status.value === 200) {
        errorFields.value = [];

        toast.success(data.value.message);
    } else if (errors.value.raw) {
        errorFields.value = errors.value.fields;
    }

    isLoading.value = false;
};

const debouncedCheckName = debounce(checkName, 300, {leading: true, trailing: true});
</script>

<template>
    <div id="content">
        <div class="flex justify-end">
            <Select :option-label="null" :options="versions"/>
        </div>
        <form @submit.prevent="save" autocomplete="off">

            <Wrapper>
                <template #text>
                    <label for="name">Name</label>
                </template>

                <template #help>
                    Changing the name of the BOM will affect all previous uses including those in Purchase Orders and
                    Sales
                    Orders. Proceed with caution.
                </template>

                <template #input>
                    <InputText v-model="bom.name" id="name"
                               class="rounded-md w-full sm:w-60" maxlength="255"
                               placeholder="Name"
                               required
                               autocomplete="off"
                               @input="debouncedCheckName"
                               :invalid="errorFields.includes('name')"/>

                    <div v-if="nameExists" class="text-red-600 pt-1 text-sm">Name is already in use</div>
                </template>
            </Wrapper>

            <Wrapper>
                <template #text>
                    <label for="name">Description</label>
                </template>

                <template #input>
                    <Textarea placeholder="Description"
                              v-model="bom.description"
                              id="name"
                              class="rounded-md w-full sm:w-60"
                              :invalid="errorFields.includes('description')"/>
                </template>
            </Wrapper>

            <div class="flex">
                <Button severity="success"
                        type="submit"
                        aria-label="Save BOM"
                        :loading="isLoading"
                        :disabled="nameExists || isLoading || errorFields.length"
                        label="Save BOM"
                        icon="pi pi-save"
                        class="mx-auto my-4"
                />
            </div>
        </form>
    </div>
</template>

<style scoped lang="postcss">

</style>
