<script setup>
import { useAxios } from "../composables/useAxios.js";
import { ref } from "vue";
import { toast } from "vue3-toastify";
import { useStorage } from "../composables/useStorage.js";
import { useDebounce } from "../composables/useDebounce.js";
import Chips from "./Form/Chips.vue";

const props = defineProps({
    defaultPerPage: { type: Number, required: true },
    perPageOptions: { type: Array, required: true },
    routeGetUsers: { type: String, required: true },
})

const users = ref([])
const roles = ref([])
const isLoading = ref(false)
const defaultData = {
    filters: {
        global: {
            constraints: [{ value: null, mode: 'contains' }],
        },
        username: {
            operator: 'and',
            constraints: [{ value: null, mode: 'contains' }],
        },
        first_name: {
            operator: 'and',
            constraints: [{ value: null, mode: 'starts_with' }],
        },
        last_name: {
            operator: 'and',
            constraints: [{ value: null, mode: 'starts_with' }],
        },
        middle_names: {
            operator: 'and',
            constraints: [{ value: null, mode: 'starts_with' }],
        },
        full_name: {
            operator: 'and',
            constraints: [{ value: null, mode: 'contains' }],
        },
        employee_id: {
            operator: 'and',
            constraints: [{ value: null, mode: 'contains' }],
        },
        email: {
            operator: 'and',
            constraints: [{ value: null, mode: 'contains' }],
        },
        join_date: {
            operator: 'and',
            constraints: [{ value: null, mode: 'date_equals' }],
        },
        role: {
            operator: 'or',
            constraints: [{ value: null, mode: 'equals' }],
        },
        active: {
            operator: 'or',
            constraints: [{ value: true, mode: 'equals' }],
        },
    },
    columns: [
        { field: 'username', label: 'Username', visible: true },
        { field: 'full_name', label: 'Full Name', visible: true },
        { field: 'first_name', label: 'First Name', visible: false },
        { field: 'middle_names', label: 'Middle Name(s)', visible: false },
        { field: 'last_name', label: 'Last Name', visible: false },
        { field: 'email', label: 'Company Email', visible: false },
        { field: 'employee_id', label: 'Employee ID', visible: true },
        { field: 'join_date', label: 'Join Date', visible: false },
        { field: 'role', label: 'Role', visible: true },
        { field: 'active', label: 'Active', visible: true },
    ],
    sort: [{ field: 'username', order: 1 }],
    pagination: {
        first: 0,
        page: 1,
        per_page: props.defaultPerPage,
        total: 0,
    }
}

defaultData.selected_columns = defaultData.columns.filter(col => col.default === true)

const { activeData, saveToStorage } = useStorage('users-index', defaultData)

const fetchUsers = async () => {
    isLoading.value = true

    const { data, errors, getResponse } = useAxios(props.routeGetUsers, {
        filters: activeData.value.filters,
        page: activeData.value.pagination.page,
        per_page: activeData.value.pagination.per_page,
        sort: activeData.value.sort,
    })

    await getResponse()

    if (!errors.value.raw) {
        users.value = data.value.users
        roles.value = data.value.roles
        activeData.value.pagination.total = data.value.total
    } else {
        users.value = []
    }

    isLoading.value = false
}
fetchUsers()

const save = (doFetchUsers = true) => {
    saveToStorage()

    if (doFetchUsers) {
        fetchUsers()
    }
}

const debouncedSave = useDebounce(save)
</script>

<template>
    <Chips v-model="activeData.columns"/>
</template>

<style scoped>

</style>
