<script setup>
import { useAxios } from "../composables/useAxios.js";
import { ref } from "vue";
import { toast } from "vue3-toastify";
import { useStorage } from "../composables/useStorage.js";

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
        { field: 'username', header: 'Username', default: true },
        { field: 'full_name', header: 'Full Name', default: true },
        { field: 'first_name', header: 'First Name', default: false },
        { field: 'middle_names', header: 'Middle Name(s)', default: false },
        { field: 'last_name', header: 'Last Name', default: false },
        { field: 'email', header: 'Company Email', default: false },
        { field: 'employee_id', header: 'Employee ID', default: true },
        { field: 'join_date', header: 'Join Date', default: false },
        { field: 'role', header: 'Role', default: true },
        { field: 'active', header: 'Active', default: true },
    ],
    selected_columns: [],
    sort: [{ field: 'username', order: 1 }],
    pagination: {
        first: 0,
        page: 1,
        per_page: props.defaultPerPage,
        total: 0,
    }
}

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
</script>

<template>

</template>

<style scoped>

</style>
