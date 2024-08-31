<script setup>
import {ref, watch} from 'vue'
import FormWrapper from './FormWrapper.vue'
import Button from 'primevue/button'
import DatePicker from 'primevue/datepicker'
import InputText from 'primevue/inputtext'
import Password from 'primevue/password'
import Select from 'primevue/select'
import Toast from 'primevue/toast'
import {useToast} from 'primevue/usetoast'
import {useAxios} from '../composables/useAxios'
import {useDebounce} from '../composables/useDebounce'

const toast = useToast()

const props = defineProps({
    routeCheckUsername: {
        type: String,
        required: true,
    },
    routeStore: {
        type: String,
        required: true,
    },
    routeRedirect: {
        type: String,
        required: true,
    },
    dateFormat: {
        type: String,
        required: true,
    },
    suggestedId: {
        type: String,
        required: true,
    },
    roles: {
        required: true,
        type: Array,
    },
    countries: {
        required: true,
        type: Array,
    },
    selectedCountry: {
        required: true,
        type: Object,
    },
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
    join_date_obj: null,
    join_date: '',
    role_id_obj: null,
    role_id: 0,
    country_obj: props.selectedCountry,
    country: props.selectedCountry.code,
})
const submitDisabled = ref(false)
const errorFields = ref([])

const checkUsername = async () => {
    if (user.value.username) {
        isLoading.value = true

        const {data, errors, getResponse} = useAxios(
            props.routeCheckUsername,
            {username: user.value.username},
            'post'
        )
        await getResponse()

        if (errors.value.raw) {
            toast.add({
                severity: 'error',
                summary: errors.value.message,
                group: 'br',
                detail: errors.value.list,
                life: 3000,
            })
        } else {
            usernameExists.value = data.value.length > 0
        }

        isLoading.value = false
    } else {
        usernameExists.value = false
    }
}

const save = async () => {
    isLoading.value = true

    const {data, errors, getResponse} = useAxios(
        props.routeStore,
        {
            ...user.value,
        },
        'post'
    )
    await getResponse()

    if (errors.value.raw) {
        toast.add({
            severity: 'error',
            summary: errors.value.message,
            group: 'br',
            detail: errors.value.list,
            life: 3000,
        })
    } else {
        submitDisabled.value = true
        errorFields.value = []
        toast.add({severity: 'success', summary: 'Success', group: 'br', detail: data.value, life: 3000})

        // setTimeout(() => window.location.replace(props.routeRedirect), 2000)
    }

    isLoading.value = false
}

const debouncedCheckUsername = useDebounce(checkUsername, 300)

watch(() => user.value.username, (newValue) => {
    user.value.username = newValue.toLowerCase()
})

watch(
    () => user.value.join_date_obj,
    (newDate) => {
        if (newDate) {
            const year = newDate.getFullYear();
            const month = String(newDate.getMonth() + 1).padStart(2, '0')
            const day = String(newDate.getDate()).padStart(2, '0')
            user.value.join_date = `${year}-${month}-${day}`
        } else {
            user.value.join_date = ''
        }
    }
)
</script>

<template>
    <Toast position="bottom-right" group="br"/>

    <form action="{{ routeStore }}" @submit.prevent="save" autocomplete="off">

        <h3>Details</h3>

        <FormWrapper required>
            <template #text>
                <label for="username" class="font-bold">
                    Username
                </label>
            </template>

            <template #help>
                Lowercase letters and numbers only
            </template>

            <template #input>
                <InputText maxlength="255"
                           id="username"
                           fluid
                           placeholder="Username"
                           required
                           v-model="user.username"
                           @input="debouncedCheckUsername"
                           pattern="^[a-z0-9]{1,255}$"
                           :invalid="errorFields.includes('username')"
                />
                <small v-if="usernameExists" class="text-red-600 ml-2">Username is already in use</small>
            </template>
        </FormWrapper>

        <FormWrapper required>
            <template #text>
                <label class="font-bold">
                    Role
                </label>
            </template>

            <template #input>
                <Select v-model="user.role_id_obj"
                        :options="props.roles"
                        optionLabel="name"
                        placeholder="Select a Role"
                        class="w-56"
                        filter
                        @change="(event) => user.role_id = event.value.id"
                />
            </template>
        </FormWrapper>

        <FormWrapper required>
            <template #text>
                <label for="password" class="font-bold">
                    Password
                </label>
            </template>

            <template #input>
                <Password v-model="user.password"
                          fluid
                          toggleMask
                          inputId="password"
                          placeholder="Password"
                          required
                          maxlength="255"
                />
            </template>
        </FormWrapper>

        <FormWrapper required>
            <template #text>
                <label for="password_confirmation" class="font-bold">
                    Confirm Password
                </label>
            </template>

            <template #input>
                <Password v-model="user.password_confirmation"
                          fluid
                          toggleMask
                          inputId="password_confirmation"
                          placeholder="Confirm Password"
                          required
                          maxlength="255"
                />
            </template>
        </FormWrapper>

        <FormWrapper required>
            <template #text>
                <label for="first_name" class="font-bold">
                    First Name
                </label>
            </template>

            <template #input>
                <InputText
                    maxlength="255"
                    id="first_name"
                    fluid
                    placeholder="First Name"
                    required
                    v-model="user.first_name"
                />
            </template>
        </FormWrapper>

        <FormWrapper>
            <template #text>
                <label for="middle_names" class="font-bold">
                    Middles Names
                </label>
            </template>

            <template #input>
                <InputText
                    maxlength="255"
                    id="middle_names"
                    fluid
                    placeholder="Middles Names"
                    v-model="user.middle_names"
                />
            </template>
        </FormWrapper>

        <FormWrapper>
            <template #text>
                <label for="last_name" class="font-bold">
                    Last Name
                </label>
            </template>

            <template #input>
                <InputText
                    maxlength="255"
                    id="last_name"
                    fluid
                    placeholder="Last Name"
                    v-model="user.last_name"
                />
            </template>
        </FormWrapper>

        <FormWrapper>
            <template #text>
                <label for="employee_id" class="font-bold">
                    Employee ID / Clock Number
                </label>
            </template>

            <template #input>
                <InputText
                    maxlength="255"
                    id="employee_id"
                    fluid
                    placeholder="Employee ID / Clock Number"
                    v-model="user.employee_id"
                    :invalid="errorFields.includes('employee_id')"
                />
            </template>
        </FormWrapper>

        <FormWrapper>
            <template #text>
                <label for="join_date_obj" class="font-bold">
                    Date Joined
                </label>
            </template>

            <template #input>
                <DatePicker v-model="user.join_date_obj"
                            showIcon
                            showWeek
                            fluid
                            showButtonBar
                            placeholder="Date Joined"
                            inputId="join_date_obj"
                            :showOnFocus="false"
                            :dateFormat="props.dateFormat"
                            :invalid="errorFields.includes('join_date_obj')"
                />
            </template>
        </FormWrapper>

        <h3>Address</h3>

        <FormWrapper>
            <template #text>
                <label for="address_line_1" class="font-bold">
                    Line 1
                </label>
            </template>

            <template #input>
                <InputText
                    maxlength="255"
                    id="address_line_1"
                    fluid
                    placeholder="Line 1"
                    v-model="user.address_line_1"
                />
            </template>
        </FormWrapper>

        <FormWrapper>
            <template #text>
                <label for="address_line_2" class="font-bold">
                    Line 2
                </label>
            </template>

            <template #input>
                <InputText
                    maxlength="255"
                    id="address_line_2"
                    fluid
                    placeholder="Line 2"
                    v-model="user.address_line_2"
                />
            </template>
        </FormWrapper>

        <FormWrapper>
            <template #text>
                <label for="address_line_3" class="font-bold">
                    Line 3
                </label>
            </template>

            <template #input>
                <InputText
                    maxlength="255"
                    id="address_line_3"
                    fluid
                    placeholder="Line 3"
                    v-model="user.address_line_3"
                />
            </template>
        </FormWrapper>

        <FormWrapper>
            <template #text>
                <label for="postcode" class="font-bold">
                    Postcode
                </label>
            </template>

            <template #input>
                <InputText
                    maxlength="10"
                    id="postcode"
                    fluid
                    placeholder="Postcode"
                    v-model="user.postcode"
                />
            </template>
        </FormWrapper>

        <FormWrapper>
            <template #text>
                <label class="font-bold">
                    Country
                </label>
            </template>

            <template #input>
                <Select v-model="user.country_obj"
                        :options="props.countries"
                        optionLabel="name"
                        placeholder="Select a Country"
                        class="w-full"
                        filter
                        @change="(event) => user.country = event.value.code"
                />
            </template>
        </FormWrapper>

        <h3>Contact</h3>

        <FormWrapper>
            <template #text>
                <label for="company_number" class="font-bold">
                    Company Number
                </label>
            </template>

            <template #help>
                Numbers, spaces, brackets and (+) symbol only
            </template>

            <template #input>
                <InputText
                    maxlength="255"
                    id="company_number"
                    fluid
                    type="tel"
                    placeholder="Company Number"
                    v-model="user.company_number"
                    pattern="[0-9 \+\(\)]*"
                />
            </template>
        </FormWrapper>

        <FormWrapper>
            <template #text>
                <label for="company_ext" class="font-bold">
                    Company Extension
                </label>
            </template>

            <template #help>
                Numbers and spaces only
            </template>

            <template #input>
                <InputText
                    maxlength="255"
                    id="company_ext"
                    fluid
                    placeholder="Company Extension"
                    v-model="user.company_ext"
                    pattern="[0-9 ]*"
                />
            </template>
        </FormWrapper>

        <FormWrapper>
            <template #text>
                <label for="home_number" class="font-bold">
                    Home Number
                </label>
            </template>

            <template #help>
                Numbers, spaces, brackets and (+) symbol only
            </template>

            <template #input>
                <InputText
                    maxlength="255"
                    id="home_number"
                    fluid
                    type="tel"
                    placeholder="Home Number"
                    v-model="user.home_number"
                    pattern="[0-9 \+\(\)]*"
                />
            </template>
        </FormWrapper>

        <FormWrapper>
            <template #text>
                <label for="mobile_number" class="font-bold">
                    Mobile Number
                </label>
            </template>

            <template #help>
                Numbers, spaces, brackets and (+) symbol only
            </template>

            <template #input>
                <InputText
                    maxlength="255"
                    id="mobile_number"
                    fluid
                    type="tel"
                    placeholder="Mobile Number"
                    v-model="user.mobile_number"
                    pattern="[0-9 \+\(\)]*"
                />
            </template>
        </FormWrapper>

        <FormWrapper>
            <template #text>
                <label for="email" class="font-bold">
                    Company Email
                </label>
            </template>

            <template #input>
                <InputText
                    maxlength="255"
                    id="email"
                    fluid
                    type="email"
                    placeholder="Company Email"
                    v-model="user.email"
                />
            </template>
        </FormWrapper>

        <FormWrapper>
            <template #text>
                <label for="home_email" class="font-bold">
                    Personal Email
                </label>
            </template>

            <template #input>
                <InputText
                    maxlength="255"
                    id="home_email"
                    fluid
                    type="email"
                    placeholder="Personal Email"
                    v-model="user.home_email"
                />
            </template>
        </FormWrapper>

        <h3>Emergency Contact</h3>

        <FormWrapper>
            <template #text>
                <label for="emergency_name" class="font-bold">
                    Emergency Contact Name
                </label>
            </template>

            <template #input>
                <InputText
                    maxlength="255"
                    id="emergency_name"
                    fluid
                    placeholder="Emergency Contact Name"
                    v-model="user.emergency_name"
                />
            </template>
        </FormWrapper>

        <FormWrapper>
            <template #text>
                <label for="emergency_number" class="font-bold">
                    Emergency Contact Number
                </label>
            </template>

            <template #help>
                Numbers, spaces, brackets and (+) symbol only
            </template>

            <template #input>
                <InputText
                    maxlength="255"
                    id="emergency_number"
                    fluid
                    type="tel"
                    placeholder="Emergency Contact Number"
                    v-model="user.emergency_number"
                    pattern="[0-9 \+\(\)]*"
                />
            </template>
        </FormWrapper>

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
</template>
