<script setup>
import { ref, useTemplateRef, watch } from "vue";
import Tab from "./Tab.vue";
import Wrapper from "./Form/Wrapper.vue";
import InputText from "./Form/InputText.vue";
import Button from "./Form/Button.vue";
import FormWrapper from "./Form/Wrapper.vue";
import Select from "./Form/Select.vue";
import Datepicker from "./Form/Datepicker.vue";
import Password from "./Form/Password.vue";
import useAxios from "../composables/useAxios.js";
import { debounce, pick } from "lodash";
import { toast } from "vue3-toastify";
import Csrf from "./Form/Csrf.vue";
import Modal from "./Modal.vue";

const props = defineProps({
    roles: { type: Array, required: true },
    permissions: { type: Object, required: true },
    routes: { type: Object, required: true },
    user: { type: Object, required: true },
    countries: { type: Array, required: true },
    dateSettings: { type: Object, required: true },
})

const usernameExists = ref(false)
const employeeIdExists = ref(false)
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
    dob: props.user.dob,

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
    left_at: props.user.left_at,
    username: props.user.username,
    employee_id: props.user.employee_id,
    role_id: props.user.roles[0]?.id,
    active: props.user.active,

    // Password
    new_password_confirmation: '',
    new_password: '',
    current_password: '',
})

const tabTexts = {
    personal_profile: '<i class="pi pi-user-edit hidden md:inline-block mr-2"></i>Personal',
    company_profile: '<i class="pi pi-building hidden md:inline-block mr-2"></i>Company',
    address: '<i class="pi pi-map hidden md:inline-block mr-2"></i>Address',
    image: '<i class="pi pi-image hidden md:inline-block mr-2"></i>Profile Image',
    protected_info: '<i class="pi pi-exclamation-circle hidden md:inline-block mr-2"></i>Admin',
}

for (const [key, text] of Object.entries(tabTexts)) {
    if (props.permissions[key]) {
        tabs.value.push({ key, text })
    }
}

tabs.value.push({
    key: 'password',
    text: `<span class="text-red-500"><i class="pi pi-key hidden md:inline-block mr-2"></i>Password</span>`,
})

activeTab.value = tabs.value[0]

const handleClick = text => {
    activeTab.value = text
}

const update = async (route, fields) => {
    isLoading.value = true

    const { errors, status, data, getResponse } = useAxios(
        route,
        pick(user.value, fields),
        'patch',
    )

    await getResponse()

    if (status.value === 200) {
        toast.success(data.value)
    } else {
        errorFields.value = errors.value.fields
    }

    isLoading.value = false
}

const updatePassword = async () => {
    await update(props.routes.password, [
        'current_password',
        'new_password',
        'new_password_confirmation',
    ])
}

const updatePersonalProfile = async () => {
    await update(props.routes.personal_profile, [
        'first_name',
        'middle_names',
        'last_name',
        'dob',
        'personal_number',
        'personal_mobile_number',
        'personal_email',
    ])
}

const updateCompanyProfile = async () => {
    update(props.routes.company_profile, [
        'email',
        'company_number',
        'company_ext',
        'company_mobile_number',
    ])
}

const updateAddress = async () => {
    await update(props.routes.address, [
        'address_line_1',
        'address_line_2',
        'address_line_3',
        'postcode',
        'country',
    ])
}

const updateProtectedInfo = async () => {
    await update(props.routes.protected_info, [
        'joined_at',
        'left_at',
        'role_id',
        'employee_id',
    ])
}

const updateUsername = async () => {
    await update(props.routes.username, ['username'])
}

const updateEmployeeId = async () => {
    await update(props.routes.employee_id, ['employee_id',])
}

const deleteModalIsVisible = ref(false)
const deleteUserForm = useTemplateRef('deleteUserForm')
const deleteUser = () => {
    deleteUserForm.submit()
}

const checkUsername = async () => {
    if (user.value.username) {
        isLoading.value = true

        const { data, errors, getResponse } = useAxios(
            props.routes.check_username,
            { username: user.value.username },
            'post',
        )
        await getResponse()

        if (!errors.value.raw) {
            usernameExists.value = data.value.length > 0
        }

        isLoading.value = false
    } else {
        usernameExists.value = false
    }
}

const checkEmployeeId = async () => {
    if (user.value.employee_id) {
        isLoading.value = true

        const { data, errors, getResponse } = useAxios(
            props.routes.check_employee_id,
            { employee_id: user.value.employee_id },
            'post',
        )
        await getResponse()

        if (!errors.value.raw) {
            employeeIdExists.value = data.value.length > 0
        }

        isLoading.value = false
    } else {
        employeeIdExists.value = false
    }
}

const debouncedCheckUsername = debounce(checkUsername, 300, { leading: true, trailing: true })
const debouncedEmployeeId = debounce(checkEmployeeId, 300, { leading: true, trailing: true })

watch(() => user.value.username, (newValue) => {
    user.value.username = newValue.toLowerCase()
})
</script>

<template>
    <ul v-if="tabs.length" class="flex mx-0 lg:mx-4 overflow-x-auto -mb-[1px]">
        <Tab v-for="tab in tabs"
             :tab="tab"
             :key="tab.field"
             :active="tab.key === activeTab.key"
             @clicked="handleClick"
             :important="tab.key === 'password'"
        />
    </ul>

    <div id="content">
        <div v-if="activeTab.key === 'personal_profile'">
            <form @submit.prevent="updatePersonalProfile" autocomplete="off">
                <Wrapper required>
                    <template #text>
                        <label for="first_name">
                            First Name
                        </label>
                    </template>

                    <template #input>
                        <InputText class="rounded-md w-full sm:w-60"
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
                        <InputText class="rounded-md w-full sm:w-60"
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
                        <InputText class="rounded-md w-full sm:w-60"
                                   id="last_name"
                                   maxlength="85"
                                   placeholder="Last Name"
                                   v-model="user.last_name"
                        />
                    </template>
                </Wrapper>

                <Wrapper>
                    <template #text>
                        <label for="dob">
                            Date of Birth
                        </label>
                    </template>

                    <template #input>
                        <Datepicker v-model="user.dob"
                                    dropdown
                                    class="w-full sm:w-60"
                                    id="dob"
                                    :invalid="errorFields.includes('dob')"
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
                        <label for="personal_number">
                            Personal Landline
                        </label>
                    </template>

                    <template #help>
                        Numbers, spaces, brackets and (+,-) symbols only
                    </template>

                    <template #input>
                        <InputText class="rounded-md w-full sm:w-60"
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
                        <InputText class="rounded-md w-full sm:w-60"
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
                        <InputText class="rounded-md w-full sm:w-60"
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
        <div v-if="activeTab.key === 'company_profile'">
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
                        <InputText class="rounded-md w-full sm:w-60"
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
                        <InputText class="rounded-md w-full sm:w-60"
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
                        <InputText class="rounded-md w-full sm:w-60"
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
                        <InputText class="rounded-md w-full sm:w-60"
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
        <div v-if="activeTab.key === 'address'">
            <form @submit.prevent="updateAddress" autocomplete="off">
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
                        <InputText class="rounded-md w-full sm:w-60"
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
                        <InputText class="rounded-md w-full sm:w-60"
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
                        <InputText class="rounded-md w-full sm:w-60"
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
        <div v-if="activeTab.key === 'protected_info'">
            <form @submit.prevent="updateUsername" autocomplete="off" v-if="permissions.username">

                <h3 class="mt-2">Edit Username</h3>

                <Wrapper required>
                    <template #text>
                        <label for="username">
                            Username
                        </label>
                    </template>

                    <template #input>
                        <InputText class="rounded-md w-full sm:w-60"
                                   maxlength="255"
                                   id="username"
                                   @input="debouncedCheckUsername"
                                   placeholder="Username"
                                   v-model="user.username"
                                   pattern="^[a-z0-9]{1,255}$"
                                   :invalid="errorFields.includes('username')"
                        />
                        <div v-if="usernameExists" class="text-red-600 pt-1 text-sm">Username is already in use</div>
                    </template>
                </Wrapper>

                <div class="flex mb-4">
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

            <form @submit.prevent="updateEmployeeId" autocomplete="off" v-if="permissions.employee_id">

                <h3>Edit Employee ID</h3>

                <Wrapper>
                    <template #text>
                        <label for="employee_id">
                            Employee ID
                        </label>
                    </template>

                    <template #input>
                        <InputText class="rounded-md w-full sm:w-60"
                                   maxlength="255"
                                   @input="debouncedEmployeeId"
                                   id="employee_id"
                                   placeholder="Employee ID"
                                   v-model="user.employee_id"
                                   :invalid="errorFields.includes('employee_id')"
                        />
                        <div v-if="employeeIdExists" class="text-red-600 pt-1 text-sm">Employee ID is already in use
                        </div>
                    </template>
                </Wrapper>

                <div class="flex mb-4">
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

            <form @submit.prevent="updateProtectedInfo" autocomplete="off" v-if="permissions.protected_info">

                <h3>Edit Protected Info</h3>

                <Wrapper required>
                    <template #text>
                        <label>
                            Role
                        </label>
                    </template>

                    <template #input>
                        <Select v-model="user.role_id"
                                :options="roles"
                                option-label="name"
                                option-value="id"
                                placeholder="Select a Role"
                                :invalid="errorFields.includes('role_id')"
                                class="w-full sm:w-60"
                                filter
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
                                    class="w-full sm:w-60"
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
                        <label for="left_at">
                            Date Left
                        </label>
                    </template>

                    <template #input>
                        <Datepicker v-model="user.left_at"
                                    dropdown
                                    class="w-full sm:w-60"
                                    id="left_at"
                                    :invalid="errorFields.includes('left_at')"
                                    :placeholder="dateSettings.display"
                                    :display-format="dateSettings.display"
                                    :format="dateSettings.format"
                                    :regex="dateSettings.regex"
                                    :separator="dateSettings.separator"
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

            <form v-if="user.active && permissions.delete" :action="routes.delete" autocomplete="off" method="post"
                  ref="deleteUserForm">
                <input type="hidden" name="_method" value="delete"/>
                <Csrf/>

                <div class="text-xl border-t border-red-200 text-red-700 p-4 bg-red-200 uppercase flex items-center">
                    <span class="text-red-500"><i class="pi pi-exclamation-triangle mr-2"></i>Delete User</span>
                </div>

                <Wrapper class="bg-red-50">
                    <template #text>
                        <label>
                            <span class="text-red-500">Delete User?</span>
                        </label>
                    </template>

                    <template #input>
                        <Button type="button"
                                variant="danger"
                                label="Delete User"
                                @click="deleteModalIsVisible = true"
                        />
                        <Modal v-if="deleteModalIsVisible"
                               v-model="deleteModalIsVisible"
                               @accepted="deleteUserForm.submit()"
                               title="Are you sure?"
                               label="Delete"
                               icon="pi pi-times"
                               variant="danger"
                        >
                            Please confirm that you would like to delete this user.
                        </Modal>
                    </template>
                </Wrapper>
            </form>

            <form v-if="!user.active && permissions.restore" :action="routes.restore" autocomplete="off" method="post">
                <input type="hidden" name="_method" value="patch"/>
                <Csrf/>

                <h3>Restore User</h3>

                <Wrapper>
                    <template #text>
                        <label>
                            Restore User?
                        </label>
                    </template>

                    <template #input>
                        <Button type="submit" label="Restore User"/>
                    </template>
                </Wrapper>
            </form>
        </div>
        <div v-if="activeTab.key === 'password'">
            <form @submit.prevent="updatePassword" autocomplete="off">
                <Wrapper v-if="!permissions.override_password" required>
                    <template #text>
                        <label for="current_password">
                            Current Password
                        </label>
                    </template>

                    <template #input>
                        <InputText class="rounded-md w-full sm:w-60"
                                   maxlength="255"
                                   type="password"
                                   required
                                   placeholder="••••••••"
                                   id="current_password"
                                   :invalid="errorFields.includes('current_password')"
                                   v-model="user.current_password"
                        />
                    </template>
                </Wrapper>

                <Wrapper required>
                    <template #text>
                        <label for="new_password">
                            New Password
                        </label>
                    </template>

                    <template #input>
                        <Password class="w-full sm:w-60"
                                  maxlength="255"
                                  id="new_password"
                                  required
                                  placeholder="••••••••"
                                  v-model="user.new_password"
                                  :invalid="errorFields.includes('new_password')"
                        />
                    </template>
                </Wrapper>

                <Wrapper required>
                    <template #text>
                        <label for="new_password_confirmation">
                            New Password Confirmation
                        </label>
                    </template>

                    <template #input>
                        <InputText class="rounded-md w-full sm:w-60"
                                   maxlength="255"
                                   type="password"
                                   placeholder="••••••••"
                                   id="new_password_confirmation"
                                   v-model="user.new_password_confirmation"
                                   :invalid="errorFields.includes('new_password_confirmation')"
                                   required
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
