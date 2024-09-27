<script setup>
import { ref } from 'vue'
import { useAxios } from '../composables/useAxios.js'
import { useStorage } from '../composables/useStorage.js'
import { useDebounce } from '../composables/useDebounce.js'
import DataTable from './DataTable/DataTable.vue'
import Column from './DataTable/Column.vue'
import { useTable } from '../composables/useTable.js'

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
        first_name: { operator: 'and', constraints: [{ value: null, mode: 'contains' }] },
        last_name: { operator: 'and', constraints: [{ value: null, mode: 'contains' }] },
        middle_names: { operator: 'and', constraints: [{ value: null, mode: 'contains' }] },
        full_name: { operator: 'and', constraints: [{ value: null, mode: 'contains' }] },
        employee_id: { operator: 'and', constraints: [{ value: null, mode: 'contains' }] },
        email: { operator: 'and', constraints: [{ value: null, mode: 'contains' }] },
        join_date: { operator: 'and', constraints: [{ value: null, mode: 'date_equals' }] },
        role: { operator: 'or', constraints: [{ value: null, mode: 'equals' }] },
        active: { constraints: [{ value: true, mode: 'equals' }] },
    },
    filtered: [],
    columns: [
        { field: 'username', label: 'Username', visible: true, order: 1 },
        { field: 'full_name', label: 'Full Name', visible: true, order: 2 },
        { field: 'first_name', label: 'First Name', visible: true, order: 3 },
        { field: 'last_name', label: 'Last Name', visible: true, order: 4 },
        { field: 'active', label: 'Active', visible: true, order: 5 },
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

const debouncedFetchUsers = useDebounce(fetchUsers)

const save = (doFetchUsers = true) => {
    saveToStorage()

    if (doFetchUsers) {
        debouncedFetchUsers()
    }
}

const { getColumn, getFilteredFields } = useTable(activeData)

const filter = (doFetchUsers = true) => {
    activeData.value.filtered = getFilteredFields(activeData.value.filters)
    save(doFetchUsers)
}

filter(false)
</script>

<template>
    {{ activeData.filtered }}
    <DataTable :rows="users" v-model="activeData" @sort="save" @filter="filter" :is-loading="isLoading">
        <Column sticky class="w-16">
            <template #body="{ row }">
                <a :href="row.edit_user_route"
                   class="bg-amber-500 p-1.5 rounded text-white inline-flex"
                   title="Edit"
                >
                    <i class="pi pi-pen-to-square"></i>
                </a>
            </template>
        </Column>
        <Column :column="getColumn('username')" v-if="getColumn('username').visible" sortable type="string">
            <template #body="{ row }">
                {{ row.username }}
            </template>
            <template #filter="{ row }">
                Filter
            </template>
        </Column>
        <Column :column="getColumn('first_name')" v-if="getColumn('first_name').visible" sortable type="string"></Column>
        <Column :column="getColumn('last_name')" v-if="getColumn('last_name').visible" sortable>
            <template #body="{ row }">
                {{ row.last_name }}
            </template>
            <template #filter="{ row }">
                Filter
            </template>
        </Column>
        <Column :column="getColumn('full_name')" v-if="getColumn('full_name').visible" sortable>
            <template #body="{ row }">
                {{ row.full_name }}
            </template>
            <template #filter="{ row }">
                Filter
            </template>
        </Column>
        <Column :column="getColumn('active')" v-if="getColumn('active').visible" sortable>
            <template #body="{ row }">
                <span :class="{'text-green-500': row.active, 'text-red-500': !row.active}">
                    <i v-if="row.active" class="pi pi-check-circle" title="Active" aria-label="Active"></i>
                    <i v-else class="pi pi-times-circle" title="Inactive" aria-label="Inactive"></i>
                </span>
            </template>
            <template #filter="{ row }">
                Filter
            </template>
        </Column>
    </DataTable>
</template>
