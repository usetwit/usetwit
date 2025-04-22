<script setup>
import InputText from '@/components/Form/InputText.vue';
import Select from '@/components/Form/Select.vue';
import Button from '@/components/Form/Button.vue';
import Wrapper from '@/components/Form/Wrapper.vue';
import {ref} from 'vue';
import useAxios from '@/composables/useAxios.js';
import {toast} from 'vue3-toastify';
import Checkbox from '@/components/Form/Checkbox.vue';

const props = defineProps({
    route: {type: String, required: true},
    countries: {type: Array, required: true},
});

const address = ref({
    address_line_1: '',
    address_line_2: '',
    address_line_3: '',
    postcode: '',
    country_code: '',
    default: false,
});

const isLoading = ref(false);

const create = async () => {
    isLoading.value = true;

    const {data, status, getResponse} = useAxios(
        props.route,
        {
            ...address.value,
        },
        'post',
    );
    await getResponse();

    if (status.value === 200) {
        toast.success(data.value.message);
    }

    isLoading.value = false;
};
</script>

<template>
    <form @submit.prevent="create" autocomplete="off">
        <Wrapper>
            <template #text>
                <label for="address_line_1">
                    Line 1
                </label>
            </template>

            <template #input>
                <InputText class="rounded-md w-full sm:w-60"
                           maxlength="255"
                           id="address_line_1"
                           placeholder="Line 1"
                           v-model="address.address_line_1"
                />
            </template>
        </Wrapper>

        <Wrapper>
            <template #text>
                <label for="address_line_2">
                    Line 2
                </label>
            </template>

            <template #input>
                <InputText class="rounded-md w-full sm:w-60"
                           maxlength="255"
                           id="address_line_2"
                           placeholder="Line 2"
                           v-model="address.address_line_2"
                />
            </template>
        </Wrapper>

        <Wrapper>
            <template #text>
                <label for="address_line_3">
                    Line 3
                </label>
            </template>

            <template #input>
                <InputText class="rounded-md w-full sm:w-60"
                           maxlength="255"
                           id="address_line_3"
                           placeholder="Line 3"
                           v-model="address.address_line_3"
                />
            </template>
        </Wrapper>

        <Wrapper>
            <template #text>
                <label for="postcode">
                    Postcode
                </label>
            </template>

            <template #input>
                <InputText class="rounded-md w-full sm:w-60"
                           maxlength="10"
                           id="postcode"
                           placeholder="Postcode"
                           v-model="address.postcode"
                />
            </template>
        </Wrapper>

        <Wrapper>
            <template #text>
                <label>
                    Country
                </label>
            </template>

            <template #input>
                <Select v-model="address.country_code"
                        :options="countries"
                        option-label="name"
                        option-value="code"
                        placeholder="Select a Country"
                        class="w-full"
                        show-clear
                        filter
                />
            </template>
        </Wrapper>

        <Wrapper>
            <template #text>
                <label>
                    Make Default
                </label>
            </template>

            <template #input>
                <Checkbox v-model="address.default"
                />
            </template>
        </Wrapper>

        <div class="flex">
            <Button severity="success"
                    type="submit"
                    aria-label="Save"
                    :loading="isLoading"
                    :disabled="isLoading"
                    label="Save"
                    icon="pi pi-save"
                    class="mx-auto my-4"
            />
        </div>
    </form>
</template>

<style scoped lang="postcss">

</style>
