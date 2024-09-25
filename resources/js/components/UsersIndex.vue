<script setup>
import { ref } from 'vue'
import { useAxios } from '../composables/useAxios.js'
import { useStorage } from '../composables/useStorage.js'
import { useDebounce } from '../composables/useDebounce.js'
import ColumnSelect from './DataTable/ColumnSelect.vue'
import DataTable from './DataTable/DataTable.vue'
import Column from './DataTable/Column.vue'
import { useTable } from "../composables/useTable.js";

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
        global: { constraints: [{ value: null, mode: 'contains' }] },
        username: { operator: 'and', constraints: [{ value: null, mode: 'contains' }] },
        first_name: { operator: 'and', constraints: [{ value: null, mode: 'starts_with' }] },
        last_name: { operator: 'and', constraints: [{ value: null, mode: 'starts_with' }] },
        middle_names: { operator: 'and', constraints: [{ value: null, mode: 'starts_with' }] },
        full_name: { operator: 'and', constraints: [{ value: null, mode: 'contains' }] },
        employee_id: { operator: 'and', constraints: [{ value: null, mode: 'contains' }] },
        email: { operator: 'and', constraints: [{ value: null, mode: 'contains' }] },
        join_date: { operator: 'and', constraints: [{ value: null, mode: 'date_equals' }] },
        role: { operator: 'or', constraints: [{ value: null, mode: 'equals' }] },
        active: { operator: 'or', constraints: [{ value: true, mode: 'equals' }] },
    },
    columns: [
        { field: 'username', label: 'Username', visible: true },
        { field: 'full_name', label: 'Full Name', visible: true },
        { field: 'first_name', label: 'First Name', visible: true },
        { field: 'last_name', label: 'Last Name', visible: true },
    ],
    sort: [{ field: 'username', order: 'asc' }],
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

const save = (doFetchUsers = true) => {
    saveToStorage()

    if (doFetchUsers) {
        fetchUsers()
    }
}

const debouncedSave = useDebounce(save)

const { getColumn } = useTable(activeData)
</script>

<template>
    <ColumnSelect v-model="activeData.columns"/>

    <DataTable :rows="users" v-model="activeData">
        <Column sticky>
            <template #body="{ row }">
                <a :href="row.edit_user_route"
                   class="bg-amber-500 p-1.5 rounded text-white inline-flex"
                   title="Edit"
                >
                    <i class="pi pi-pen-to-square"></i>
                </a>
            </template>
        </Column>
        <Column :column="getColumn('username')" v-if="getColumn('username').visible" sortable>
            <template #body="{ row }">
                {{ row.username }}
            </template>
            <template #filter="{ row }">
                Filter
            </template>
        </Column>
        <Column :column="getColumn('first_name')" v-if="getColumn('first_name').visible" sortable></Column>
        <Column :column="getColumn('last_name')" v-if="getColumn('last_name').visible" sortable>
            <template #body="{ row }">
                {{ row.last_name }}
            </template>
        </Column>
        <Column :column="getColumn('full_name')" v-if="getColumn('full_name').visible" sortable>
            <template #body="{ row }">
                {{ row.full_name }}
            </template>
        </Column>
    </DataTable>
</template>
