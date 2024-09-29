<script setup>
import { provide, ref } from 'vue'
import { useAxios } from '../composables/useAxios.js'
import DataTable from './DataTable/DataTable.vue'
import Column from './DataTable/Column.vue'
import { useTable } from '../composables/useTable.js'
import { formatDate } from '../app/helpers.js'

const props = defineProps({
    paginationSettings: { type: Object, required: true },
    dateSettings: { type: Object },
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
        joined_at: { operator: 'and', constraints: [{ value: null, mode: 'date_equals' }] },
        role: { operator: 'or', constraints: [{ value: null, mode: 'equals' }] },
        active: { constraints: [{ value: true, mode: 'equals' }] },
    },
    filtered: [],
    columns: [
        { field: 'username', label: 'Username', visible: true, order: 1 },
        { field: 'full_name', label: 'Full Name', visible: true, order: 2 },
        { field: 'first_name', label: 'First Name', visible: true, order: 3 },
        { field: 'middle_names', label: 'Middle Name(s)', visible: false, order: 4 },
        { field: 'last_name', label: 'Last Name', visible: true, order: 5 },
        { field: 'joined_at', label: 'Join Date', visible: true, order: 6 },
        { field: 'active', label: 'Active', visible: true, order: 7 },
    ],
    sort: [{ field: 'username', order: 'asc' }],
    pagination: {
        page: 1,
        per_page: props.paginationSettings.per_page.default,
        total: 0,
    }
}

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

const tableInstance = useTable('users-index', defaultData, fetchUsers)

const { getColumn, activeData } = tableInstance

provide('tableInstance', tableInstance)
</script>

<template>
    <DataTable :rows="users"
               v-model="activeData"
               :is-loading="isLoading"
               :pagination-settings="paginationSettings"
               :date-settings="dateSettings"
    >
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
                <a :href="row.edit_user_route" title="Edit">{{ row.username }}</a>
            </template>
        </Column>
        <Column :column="getColumn('first_name')" v-if="getColumn('first_name').visible" sortable type="string">
            <template #body="{ row }">
                <a :href="row.edit_user_route" title="Edit">{{ row.first_name }}</a>
            </template>
        </Column>
        <Column :column="getColumn('middle_names')" v-if="getColumn('middle_names').visible" sortable type="string">
            <template #body="{ row }">
                <a :href="row.edit_user_route" title="Edit">{{ row.middle_names }}</a>
            </template>
        </Column>
        <Column :column="getColumn('last_name')" v-if="getColumn('last_name').visible" sortable type="string">
            <template #body="{ row }">
                <a :href="row.edit_user_route" title="Edit">{{ row.last_name }}</a>
            </template>
        </Column>
        <Column :column="getColumn('full_name')" v-if="getColumn('full_name').visible" sortable type="string">
            <template #body="{ row }">
                <a :href="row.edit_user_route" title="Edit">{{ row.full_name }}</a>
            </template>
        </Column>
        <Column :column="getColumn('joined_at')" v-if="getColumn('joined_at').visible" sortable type="date">
            <template #body="{ row }">
                <a :href="row.edit_user_route"
                   title="Edit"
                >
                    {{ formatDate(row.joined_at, dateSettings.format, dateSettings.separator) }}
                </a>
            </template>
        </Column>
        <Column :column="getColumn('active')" v-if="getColumn('active').visible" sortable type="boolean"
                class="text-center">
            <template #body="{ row }">
                <span :class="{'text-green-500': row.active, 'text-red-500': !row.active}">
                    <i v-if="row.active" class="pi pi-check-circle" title="Active"></i>
                    <i v-else class="pi pi-times-circle" title="Inactive"></i>
                </span>
            </template>
        </Column>
    </DataTable>
</template>
