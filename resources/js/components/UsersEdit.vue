<script setup>
import { ref } from "vue";
import Tab from "./Tab.vue";
import Wrapper from "./Form/Wrapper.vue";
import InputText from "./Form/InputText.vue";
import Button from "./Form/Button.vue";
import FormWrapper from "./Form/Wrapper.vue";
import Select from "./Form/Select.vue";
import Datepicker from "./Form/Datepicker.vue";

const props = defineProps({
    roles: { type: Array, required: true },
    permissions: { type: Object, required: true },
    routes: { type: Object, required: true },
    user: { type: Object, required: true },
    countries: { type: Array, required: true },
    dateSettings: { type: Object, required: true },
})

const isLoading = ref(false)
const tabs = ref([])
const activeTab = ref(null)
const errorFields = ref([])
const user = ref({
    // Personal Profile
    first_name: props.user.first_name,
    middle_names: props.user.middle_names,
    last_name: props.user.last_name,
    personal_number: props.user.personal_number,
    personal_mobile_number: props.user.personal_mobile_number,
    personal_email: props.user.personal_email,

    // Company Profile
    company_mobile_number: props.user.company_mobile_number,
    company_number: props.user.company_number,
    email: props.user.email,
    company_ext: props.user.company_ext,

    // Address
    address_line_1: props.user.address?.address_line_1 || null,
    address_line_2: props.user.address?.address_line_2 || null,
    address_line_3: props.user.address?.address_line_3 || null,
    postcode: props.user.address?.postcode || null,
    country: props.user.address?.country || null,

    // Protected Info
    joined_at: props.user.joined_at,
    username: props.user.username,
    employee_id: props.user.employee_id,
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
                                   maxlength="255"
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
                                   maxlength="255"
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
                                   maxlength="255"
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
                                   maxlength="255"
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
                                   id="company_ext"
                                   maxlength="255"
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
                                   maxlength="255"
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
                                   maxlength="255"
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

        <div v-if="activeTab === 'Address'">
            <form @submit.prevent="updateAddress">
                <Wrapper>
                    <template #text>
                        <label for="address_line_1">
                            Line 1
                        </label>
                    </template>

                    <template #input>
                        <InputText class="rounded-md"
                                   maxlength="255"
                                   id="address_line_1"
                                   placeholder="Line 1"
                                   v-model="user.address_line_1"
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
                        <InputText class="rounded-md"
                                   maxlength="255"
                                   id="address_line_2"
                                   placeholder="Line 2"
                                   v-model="user.address_line_2"
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
                        <InputText class="rounded-md"
                                   maxlength="255"
                                   id="address_line_3"
                                   placeholder="Line 3"
                                   v-model="user.address_line_3"
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
                        <InputText class="rounded-md"
                                   maxlength="10"
                                   id="postcode"
                                   placeholder="Postcode"
                                   v-model="user.postcode"
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
                        <Select v-model="user.country"
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

        <div v-if="activeTab === 'Admin'">
            <form @submit.prevent="updateProtectedInfo">
                <Wrapper>
                    <template #text>
                        <label for="username">
                            Username
                        </label>
                    </template>

                    <template #input>
                        <InputText class="rounded-md"
                                   maxlength="255"
                                   id="username"
                                   placeholder="Username"
                                   v-model="user.username"
                        />
                    </template>
                </Wrapper>

                <Wrapper>
                    <template #text>
                        <label for="joined_at">
                            Date Joined
                        </label>
                    </template>

                    <template #input>
                        <Datepicker v-model="user.joined_at"
                                    dropdown
                                    placeholder="Date Joined"
                                    id="joined_at"
                                    :invalid="errorFields.includes('joined_at')"
                                    :placeholder="dateSettings.display"
                                    :display-format="dateSettings.display"
                                    :format="dateSettings.format"
                                    :regex="dateSettings.regex"
                                    :separator="dateSettings.separator"
                        />
                    </template>
                </Wrapper>

                <Wrapper>
                    <template #text>
                        <label for="employee_id">
                            Employee ID
                        </label>
                    </template>

                    <template #input>
                        <InputText class="rounded-md"
                                   maxlength="255"
                                   id="employee_id"
                                   placeholder="Employee ID"
                                   v-model="user.employee_id"
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
