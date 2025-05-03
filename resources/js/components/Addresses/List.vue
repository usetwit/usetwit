<script setup>
import {computed, ref} from 'vue';
import Button from '@/components/Form/Button.vue';
import Item from '@/components/Addresses/Item.vue';
import Modal from '@/components/Modal.vue';
import Wrapper from '@/components/Form/Wrapper.vue';
import InputText from '@/components/Form/InputText.vue';
import Select from '@/components/Form/Select.vue';
import Checkbox from '@/components/Form/Checkbox.vue';
import useAxios from '@/composables/useAxios.js';
import {toast} from 'vue3-toastify';

const props = defineProps({
    permissions: {
        type: Object,
        required: true,
        validator: p => ['create_address', 'update_address', 'delete_address'].every(k => typeof p[k] === 'boolean'),
    },
    countries: {type: Array, required: true},
    defaultCountry: {type: String, required: true},
    routes: {type: Object, required: true},
});

const loading = ref(false);
const addresses = defineModel({type: Array});

const sortedAddresses = computed(() => {
    return addresses.value
        .slice()
        .sort((a, b) => {
            if (a.is_default && !b.is_default) return -1;
            if (!a.is_default && b.is_default) return 1;

            return b.updated_at > a.updated_at;
        });
});

const showNewAddressModal = ref(false);

const newAddress = ref(
    {
        'address_line_1': '',
        'address_line_2': '',
        'address_line_3': '',
        'postcode': '',
        'country_code': '',
        'is_default': false,
    },
);

newAddress.value.country_code = props.defaultCountry;

const saveNewAddress = async () => {
    loading.value = true;

    const {data, status, getResponse} = useAxios(
        props.routes.create_address,
        newAddress.value,
    );

    await getResponse();

    if (status.value === 201) {
        addresses.value = data.value.addresses;

        newAddress.value = {
            'address_line_1': '',
            'address_line_2': '',
            'address_line_3': '',
            'postcode': '',
            'country_code': props.defaultCountry,
            'is_default': false,
        };

        toast.success(data.value.message);
    }

    loading.value = false;
};

const makeDefault = async (address) => {
    loading.value = true;

    const {data, status, getResponse} = useAxios(
        address.routes.make_default,
        {},
        'patch',
    );

    await getResponse();

    if (status.value === 200) {
        addresses.value = data.value.addresses;

        toast.success(data.value.message);
    }

    loading.value = false;
};

const deleteAddress = async (address) => {
    loading.value = true;

    const {data, status, getResponse} = useAxios(
        address.routes.delete,
        {},
        'delete',
    );

    await getResponse();

    if (status.value === 200) {
        addresses.value = data.value.addresses;

        toast.success(data.value.message);
    }

    loading.value = false;
};

</script>

<template>
    <Modal v-model="showNewAddressModal"
           v-if="showNewAddressModal"
           label="Save"
           variant="success"
           title="Add Address"
           icon="pi pi-save"
           @accepted="saveNewAddress"
    >
        <Wrapper>
            <template #text>
                <label for="address_line_1">
                    Address Line 1
                </label>
            </template>
            <template #input>
                <InputText v-model="newAddress.address_line_1"
                           class="rounded-md"
                           maxlength="255"
                           name="address_line_1"
                           id="address_line_1"
                ></InputText>
            </template>
        </Wrapper>
        <Wrapper>
            <template #text>
                <label for="address_line_2">
                    Address Line 2
                </label>
            </template>
            <template #input>
                <InputText v-model="newAddress.address_line_2"
                           class="rounded-md"
                           maxlength="255"
                           name="address_line_2"
                           id="address_line_2"
                ></InputText>
            </template>
        </Wrapper>
        <Wrapper>
            <template #text>
                <label for="address_line_3">
                    Address Line 3
                </label>
            </template>
            <template #input>
                <InputText v-model="newAddress.address_line_3"
                           class="rounded-md"
                           maxlength="255"
                           name="address_line_3"
                           id="address_line_3"
                ></InputText>
            </template>
        </Wrapper>
        <Wrapper>
            <template #text>
                <label for="postcode">
                    Postal Code
                </label>
            </template>
            <template #input>
                <InputText v-model="newAddress.postcode"
                           class="rounded-md"
                           maxlength="255"
                           name="postcode"
                           id="postcode"
                ></InputText>
            </template>
        </Wrapper>
        <Wrapper>
            <template #text>
                <strong>Country</strong>
            </template>
            <template #input>
                <Select v-model="newAddress.country_code"
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
            <template #input>
                <Checkbox id="is_default"
                          name="is_default"
                          class="mt-2"
                          v-model="newAddress.is_default"
                          label="Set as Default"
                />
            </template>
        </Wrapper>
    </Modal>

    <div class="space-y-4 p-4">
        <div v-if="permissions.create_address" class="text-right">
            <Button @click="showNewAddressModal = true"
                    icon="pi pi-plus"
            >
                New Address
            </Button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            <Item v-for="address in sortedAddresses"
                  :key="address.id"
                  :permissions="permissions"
                  :address="address"
                  @make-default="makeDefault"
                  @delete="deleteAddress"
                  :loading="loading"
            />

            <div v-if="addresses.length === 0"
                 class="col-span-full text-center text-gray-500 p-4"
            >
                No addresses found.
            </div>
        </div>
    </div>
</template>
