<script setup>
import { ref } from "vue";
import Tab from "./Tab.vue";
import Wrapper from "./Form/Wrapper.vue";
import InputText from "./Form/InputText.vue";
import Button from "./Form/Button.vue";
import FormWrapper from "./Form/Wrapper.vue";

const props = defineProps({
    roleId: { type: [Number, null], required: true },
    roles: { type: Array, required: true },
    permissions: { type: Object, required: true },
    routes: { type: Object, required: true },
    images: { type: Array, required: true },
    user: { type: Object, required: true },
    address: { type: [Object, null], required: true },
})

const isLoading = ref(false)
const tabs = ref([])
const activeTab = ref(null)
const user = ref({
    username: props.user?.username,
    first_name: props.user?.first_name,
    middle_names: props.user?.middle_names,
    last_name: props.user?.last_name,
    personal_number: props.user?.personal_number,
    personal_mobile_number: props.user?.personal_mobile_number,
    personal_email: props.user?.personal_email,
    last_name: props.user?.last_name,
})

const tabTexts = {
    personal_profile: 'Personal Profile',
    company_profile: 'Company Profile',
    address: 'Address',
    image: 'Profile Image',
    protected_info: 'Admin',
}

for (const [permission, tabName] of Object.entries(tabTexts)) {
    if (props.permissions[permission]) {
        tabs.value.push(tabName);
    }
}

activeTab.value = tabs.value[0]

const handleClick = text => {
    activeTab.value = text
}
</script>

<template>
    <ul v-if="tabs.length" class="flex mx-0 lg:mx-4">
        <Tab v-for="tab in tabs" :text="tab" :key="tab" :active="tab === activeTab" @clicked="handleClick"/>
    </ul>

    <div id="content">
        <div v-if="activeTab === 'Personal Profile'">
            <form @submit.prevent="updatePersonalProfile" autocomplete="off">
                <Wrapper required>
                    <template #text>
                        <label for="first_name">
                            First Name
                        </label>
                    </template>

                    <template #input>
                        <InputText class="rounded-md"
                                   id="first_name"
                                   maxlength="85"
                                   required
                                   placeholder="First Name"
                                   v-model="user.first_name"
                        />
                    </template>
                </Wrapper>

                <Wrapper>
                    <template #text>
                        <label for="middle_names">
                            Middle Name(s)
                        </label>
                    </template>

                    <template #input>
                        <InputText class="rounded-md"
                                   id="middle_names"
                                   maxlength="85"
                                   placeholder="Middle Name(s)"
                                   v-model="user.middle_names"
                        />
                    </template>
                </Wrapper>

                <Wrapper>
                    <template #text>
                        <label for="last_name">
                            Last Name
                        </label>
                    </template>

                    <template #input>
                        <InputText class="rounded-md"
                                   id="last_name"
                                   maxlength="85"
                                   placeholder="Last Name"
                                   v-model="user.last_name"
                        />
                    </template>
                </Wrapper>

                <Wrapper>
                    <template #text>
                        <label for="personal_number">
                            Personal Landline
                        </label>
                    </template>

                    <template #help>
                        Numbers, spaces, brackets and (+,-) symbols only
                    </template>

                    <template #input>
                        <InputText class="rounded-md"
                                   id="personal_number"
                                   type="tel"
                                   placeholder="Personal Landline"
                                   v-model="user.personal_number"
                                   pattern="^[0-9 \+\(\)\.\-]*$"
                        />
                    </template>
                </Wrapper>

                <Wrapper>
                    <template #text>
                        <label for="personal_mobile_number">
                            Personal Mobile
                        </label>
                    </template>

                    <template #help>
                        Numbers, spaces, brackets and (+,-) symbols only
                    </template>

                    <template #input>
                        <InputText class="rounded-md"
                                   id="personal_mobile_number"
                                   type="tel"
                                   placeholder="Personal Mobile"
                                   v-model="user.personal_mobile_number"
                                   pattern="^[0-9 \+\(\)\.\-]*$"
                        />
                    </template>
                </Wrapper>

                <Wrapper>
                    <template #text>
                        <label for="personal_email">
                            Personal Email
                        </label>
                    </template>

                    <template #input>
                        <InputText class="rounded-md"
                                   id="personal_email"
                                   type="email"
                                   placeholder="Personal Email"
                                   v-model="user.personal_email"
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
        </div>
        <div v-if="activeTab === 'Company Profile'">
            <form @submit.prevent="updateCompanyProfile" autocomplete="off">
                <Wrapper>
                    <template #text>
                        <label for="company_number">
                            Company Landline
                        </label>
                    </template>

                    <template #help>
                        Numbers, spaces, brackets and (+,-) symbols only
                    </template>

                    <template #input>
                        <InputText class="rounded-md"
                                   id="company_number"
                                   type="tel"
                                   placeholder="Company Landline"
                                   v-model="user.company_number"
                                   pattern="^[0-9 \+\(\)\.\-]*$"
                        />
                    </template>
                </Wrapper>

                <FormWrapper>
                    <template #text>
                        <label for="company_ext">
                            Company Extension
                        </label>
                    </template>

                    <template #help>
                        Numbers and spaces only
                    </template>

                    <template #input>
                        <InputText class="rounded-md"
                                   maxlength="255"
                                   id="company_ext"
                                   placeholder="Company Extension"
                                   v-model="user.company_ext"
                                   pattern="[0-9 ]*"
                        />
                    </template>
                </FormWrapper>

                <Wrapper>
                    <template #text>
                        <label for="company_mobile_number">
                            Company Mobile
                        </label>
                    </template>

                    <template #help>
                        Numbers, spaces, brackets and (+,-) symbols only
                    </template>

                    <template #input>
                        <InputText class="rounded-md"
                                   id="company_mobile_number"
                                   type="tel"
                                   placeholder="Company Mobile"
                                   v-model="user.company_mobile_number"
                                   pattern="^[0-9 \+\(\)\.\-]*$"
                        />
                    </template>
                </Wrapper>

                <Wrapper>
                    <template #text>
                        <label for="personal_email">
                            Company Email
                        </label>
                    </template>

                    <template #input>
                        <InputText class="rounded-md"
                                   id="company_email"
                                   type="email"
                                   placeholder="Company Email"
                                   v-model="user.personal_email"
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
        </div>
    </div>
</template>

<style scoped lang="postcss">
label {
    @apply font-bold;
}
</style>
