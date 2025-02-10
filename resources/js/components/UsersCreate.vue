<script setup>
import { ref, watch } from 'vue'
import Wrapper from '@/components/Form/Wrapper.vue'
import InputText from '@/components/Form/InputText.vue'
import Datepicker from "@/components/Form/Datepicker.vue"
import Select from '@/components/Form/Select.vue'
import Button from '@/components/Form/Button.vue'
import Password from '@/components/Form/Password.vue'
import useAxios from '@/composables/useAxios.js'
import { debounce } from 'lodash'
import { toast } from 'vue3-toastify'

const props = defineProps({
    routeCheckUsername: { type: String, required: true },
    routeStore: { type: String, required: true },
    routeRedirect: { type: String, required: true },
    dateSettings: { type: Object, required: true },
    suggestedId: { type: String, required: true },
    roles: { type: Array, required: true },
    countries: { type: Array, required: true },
    selectedCountry: { type: String, required: true },
})

const usernameExists = ref(false)
const isLoading = ref(false)
const user = ref({
    username: '',
    employee_id: props.suggestedId,
    password: '',
    password_confirmation: '',
    first_name: '',
    middle_names: '',
    last_name: '',
    address_line_1: '',
    address_line_2: '',
    address_line_3: '',
    postcode: '',
    company_number: '',
    company_ext: '',
    home_number: '',
    mobile_number: '',
    emergency_name: '',
    emergency_number: '',
    email: '',
    home_email: '',
    joined_at: '',
    role_id: 0,
    country: props.selectedCountry,
})
const submitDisabled = ref(false)
const errorFields = ref([])

const checkUsername = async () => {
    if (user.value.username) {
        isLoading.value = true

        const { data, errors, getResponse } = useAxios(
            props.routeCheckUsername,
            { username: user.value.username },
            'post'
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

const save = async () => {
    isLoading.value = true

    const { data, status, errors, getResponse } = useAxios(
        props.routeStore,
        {
            ...user.value,
        },
        'post'
    )
    await getResponse()

    if (status.value === 200) {
        submitDisabled.value = true
        errorFields.value = []

        toast.success(data.value.message)

        setTimeout(() => window.location.replace(data.value.redirect), 2000)
    } else if (errors.value.raw) {
        errorFields.value = errors.value.fields
    }

    isLoading.value = false
}

const debouncedCheckUsername = debounce(checkUsername, 300, { leading: true, trailing: true })

watch(() => user.value.username, (newValue) => {
    user.value.username = newValue.toLowerCase()
})
</script>

<template>
    <div id="content">
        <form @submit.prevent="save" autocomplete="off">

            <h3>Details</h3>

            <Wrapper required>
                <template #text>
                    <label for="username">
                        Username
                    </label>
                </template>

                <template #help>
                    Lowercase letters and numbers only
                </template>

                <template #input>
                    <InputText class="rounded-md w-full sm:w-60" maxlength="255"
                               id="username"
                               placeholder="Username"
                               required
                               autocomplete="off"
                               v-model="user.username"
                               @input="debouncedCheckUsername"
                               pattern="^[a-z0-9]{1,255}$"
                               :invalid="errorFields.includes('username')"
                    />
                    <div v-if="usernameExists" class="text-red-600 pt-1 text-sm">Username is already in use</div>
                </template>
            </Wrapper>

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

            <Wrapper required>
                <template #text>
                    <label for="password">
                        Password
                    </label>
                </template>

                <template #input>
                    <Password v-model="user.password"
                              required
                              class="w-full sm:w-60"
                              id="password"
                              maxlength="255"
                    />
                </template>
            </Wrapper>

            <Wrapper required>
                <template #text>
                    <label for="password_confirmation">
                        Confirm Password
                    </label>
                </template>

                <template #input>
                    <InputText v-model="user.password_confirmation"
                               required
                               maxlength="255"
                               id="password_confirmation"
                               placeholder="••••••••"
                               type="password"
                               class="rounded-md w-full sm:w-60"
                    />
                </template>
            </Wrapper>

            <Wrapper required>
                <template #text>
                    <label for="first_name">
                        First Name
                    </label>
                </template>

                <template #input>
                    <InputText class="rounded-md w-full sm:w-60"
                               maxlength="85"
                               id="first_name"
                               placeholder="First Name"
                               required
                               v-model="user.first_name"
                    />
                </template>
            </Wrapper>

            <Wrapper>
                <template #text>
                    <label for="middle_names">
                        Middles Names
                    </label>
                </template>

                <template #input>
                    <InputText class="rounded-md w-full sm:w-60"
                               maxlength="85"
                               id="middle_names"
                               placeholder="Middles Names"
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
                               maxlength="85"
                               id="last_name"
                               placeholder="Last Name"
                               v-model="user.last_name"
                    />
                </template>
            </Wrapper>

            <Wrapper>
                <template #text>
                    <label for="employee_id">
                        Employee ID / Clock Number
                    </label>
                </template>

                <template #input>
                    <InputText class="rounded-md w-full sm:w-60"
                               maxlength="255"
                               id="employee_id"
                               placeholder="Employee ID / Clock Number"
                               v-model="user.employee_id"
                               :invalid="errorFields.includes('employee_id')"
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
                                class="w-full sm:w-60"
                                :invalid="errorFields.includes('joined_at')"
                                :placeholder="dateSettings.display"
                                :display-format="dateSettings.display"
                                :format="dateSettings.format"
                                :regex="dateSettings.regex"
                                :separator="dateSettings.separator"
                    />
                </template>
            </Wrapper>

            <h3>Address</h3>

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

            <h3>Contact</h3>

            <Wrapper>
                <template #text>
                    <label for="company_number">
                        Company Number
                    </label>
                </template>

                <template #help>
                    Numbers, spaces, brackets and (+,-) symbols only
                </template>

                <template #input>
                    <InputText class="rounded-md w-full sm:w-60"
                               maxlength="255"
                               id="company_number"
                               type="tel"
                               placeholder="Company Number"
                               v-model="user.company_number"
                               pattern="^[0-9 \+\(\)\.\-]*$"
                    />
                </template>
            </Wrapper>

            <Wrapper>
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
                               maxlength="255"
                               id="company_ext"
                               placeholder="Company Extension"
                               v-model="user.company_ext"
                               pattern="[0-9 ]*"
                    />
                </template>
            </Wrapper>

            <Wrapper>
                <template #text>
                    <label for="home_number">
                        Home Number
                    </label>
                </template>

                <template #help>
                    Numbers, spaces, brackets and (+,-) symbols only
                </template>

                <template #input>
                    <InputText class="rounded-md w-full sm:w-60"
                               maxlength="255"
                               id="home_number"
                               type="tel"
                               placeholder="Home Number"
                               v-model="user.home_number"
                               pattern="^[0-9 \+\(\)\.\-]*$"
                    />
                </template>
            </Wrapper>

            <Wrapper>
                <template #text>
                    <label for="mobile_number">
                        Mobile Number
                    </label>
                </template>

                <template #help>
                    Numbers, spaces, brackets and (+,-) symbols only
                </template>

                <template #input>
                    <InputText class="rounded-md w-full sm:w-60"
                               maxlength="255"
                               id="mobile_number"
                               type="tel"
                               placeholder="Mobile Number"
                               v-model="user.mobile_number"
                               pattern="^[0-9 \+\(\)\.\-]*$"
                    />
                </template>
            </Wrapper>

            <Wrapper>
                <template #text>
                    <label for="email">
                        Company Email
                    </label>
                </template>

                <template #input>
                    <InputText class="rounded-md w-full sm:w-60"
                               maxlength="255"
                               id="email"
                               type="email"
                               placeholder="Company Email"
                               v-model="user.email"
                    />
                </template>
            </Wrapper>

            <Wrapper>
                <template #text>
                    <label for="home_email">
                        Personal Email
                    </label>
                </template>

                <template #input>
                    <InputText class="rounded-md w-full sm:w-60"
                               maxlength="255"
                               id="home_email"
                               type="email"
                               placeholder="Personal Email"
                               v-model="user.home_email"
                    />
                </template>
            </Wrapper>

            <h3>Emergency Contact</h3>

            <Wrapper>
                <template #text>
                    <label for="emergency_name">
                        Emergency Contact Name
                    </label>
                </template>

                <template #input>
                    <InputText class="rounded-md w-full sm:w-60"
                               maxlength="255"
                               id="emergency_name"
                               placeholder="Emergency Contact Name"
                               v-model="user.emergency_name"
                    />
                </template>
            </Wrapper>

            <Wrapper>
                <template #text>
                    <label for="emergency_number">
                        Emergency Contact Number
                    </label>
                </template>

                <template #help>
                    Numbers, spaces, brackets and (+,-) symbols only
                </template>

                <template #input>
                    <InputText class="rounded-md w-full sm:w-60"
                               maxlength="255"
                               id="emergency_number"
                               placeholder="Emergency Contact Number"
                               v-model="user.emergency_number"
                               pattern="^[0-9 \+\(\)\.\-]*$"
                    />
                </template>
            </Wrapper>

            <div class="flex">
                <Button severity="success"
                        type="submit"
                        aria-label="Create User"
                        :loading="isLoading"
                        :disabled="submitDisabled"
                        label="Create User"
                        icon="pi pi-save"
                        class="mx-auto my-4"
                />
            </div>

        </form>
    </div>
</template>
