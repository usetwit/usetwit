<script setup>
import { provide, ref } from 'vue'
import useAxios from '@/composables/useAxios.js'
import DataTable from '@/components/DataTable/DataTable.vue'
import Column from '@/components/DataTable/Column.vue'
import Button from '@/components/Form/Button.vue'
import useTable from '@/composables/useTable.js'
import { formatDate, applyFilterRegex } from '@/app/helpers.js'
import useStorage from '@/composables/useStorage.js'
import { startCase } from 'lodash'

const props = defineProps({
    paginationSettings: { type: Object, required: true },
    dateSettings: { type: Object, required: true },
    routeGetUsers: { type: String, required: true },
})

const rows = ref([])
const isLoading = ref(false)
const defaultData = {
    filters: {
        global: { constraints: [{ value: null, mode: 'contains' }] },
        id: { operator: 'or', constraints: [{ value: null, mode: 'contains' }] },
        username: { operator: 'or', constraints: [{ value: null, mode: 'contains' }] },
        first_name: { operator: 'or', constraints: [{ value: null, mode: 'contains' }] },
        last_name: { operator: 'or', constraints: [{ value: null, mode: 'contains' }] },
        middle_names: { operator: 'or', constraints: [{ value: null, mode: 'contains' }] },
        full_name: { operator: 'or', constraints: [{ value: null, mode: 'contains' }] },
        employee_id: { operator: 'or', constraints: [{ value: null, mode: 'contains' }] },
        email: { operator: 'or', constraints: [{ value: null, mode: 'contains' }] },
        role_name: { operator: 'or', constraints: [{ value: null, mode: 'contains' }] },
        joined_at: { operator: 'or', constraints: [{ value: null, mode: 'date_equals' }] },
        created_at: { operator: 'or', constraints: [{ value: null, mode: 'date_equals' }] },
        updated_at: { operator: 'or', constraints: [{ value: null, mode: 'date_equals' }] },
        active: { constraints: [{ value: true, mode: 'equals' }] },
    },
    filtered: [],
    columns: [
        { field: 'id', label: 'ID', visible: true, order: 1 },
        { field: 'username', label: 'Username', visible: true, order: 2 },
        { field: 'full_name', label: 'Full Name', visible: true, order: 3 },
        { field: 'first_name', label: 'First Name', visible: false, order: 4 },
        { field: 'middle_names', label: 'Middle Name(s)', visible: false, order: 5 },
        { field: 'last_name', label: 'Last Name', visible: false, order: 6 },
        { field: 'email', label: 'Email', visible: false, order: 7 },
        { field: 'role_name', label: 'Role', visible: true, order: 8 },
        { field: 'joined_at', label: 'Join Date', visible: true, order: 9 },
        { field: 'created_at', label: 'Created Date', visible: false, order: 8 },
        { field: 'updated_at', label: 'Updated Date', visible: false, order: 9 },
        { field: 'active', label: 'Active', visible: true, order: 10 },
    ],
    sort: [{ field: 'username', order: 'asc' }],
    pagination: {
        page: 1,
        per_page: props.paginationSettings.per_page.default,
        total: 0,
    }
}

const storageInstance = useStorage('users-index', defaultData)
const { activeData } = storageInstance

const fetchUsers = async () => {
    isLoading.value = true

    const { data, errors, getResponse } = useAxios(props.routeGetUsers, {
        filters: activeData.value.filters,
        page: activeData.value.pagination.page,
        per_page: activeData.value.pagination.per_page,
        sort: activeData.value.sort,
        visible: activeData.value.columns.filter(col => col.visible).map(col => col.field)
    })

    await getResponse()

    if (!errors.value.raw) {
        rows.value = data.value.users
        activeData.value.pagination.total = data.value.total
    } else {
        rows.value = []
    }

    isLoading.value = false
}

const tableInstance = useTable(defaultData, fetchUsers, storageInstance)

const { getColumn, isVisible, getSearchGlobalValue, getSearchValues } = tableInstance

const r = (field, string) => applyFilterRegex(string, getSearchGlobalValue(), getSearchValues(field))

provide('tableInstance', tableInstance)
</script>

<template>
    <DataTable v-model:rows="rows"
               v-model="activeData"
               :is-loading="isLoading"
               :pagination-settings="paginationSettings"
               :date-settings="dateSettings"
    >
        <Column sticky class="w-16" options>
            <template #body="{ row }">
                <a :href="row.edit_user_route"
                   class="bg-amber-500 p-1.5 rounded text-white inline-flex"
                   title="Edit"
                >
                    <i class="pi pi-pen-to-square"></i>
                </a>
            </template>
        </Column>
        <Column :column="getColumn('username')" v-if="isVisible('username')" sortable type="string">
            <template #body="{ row }">
                <a :href="row.edit_user_route" title="Edit" v-html="r('username', row.username)"></a>
            </template>
        </Column>
        <Column :column="getColumn('id')" v-if="isVisible('id')" sortable type="string">
            <template #body="{ row }">
                <a :href="row.edit_user_route" title="Edit" v-html="r('id', row.id)"></a>
            </template>
        </Column>
        <Column :column="getColumn('full_name')" v-if="isVisible('full_name')" sortable type="string">
            <template #body="{ row }">
                <a :href="row.edit_user_route" title="Edit" v-html="r('full_name', row.full_name)"></a>
            </template>
        </Column>
        <Column :column="getColumn('first_name')" v-if="isVisible('first_name')" sortable type="string">
            <template #body="{ row }">
                <a :href="row.edit_user_route" title="Edit" v-html="r('first_name', row.first_name)"></a>
            </template>
        </Column>
        <Column :column="getColumn('middle_names')" v-if="isVisible('middle_names')" sortable type="string">
            <template #body="{ row }">
                <a :href="row.edit_user_route" title="Edit" v-html="r('middle_names', row.middle_names)"></a>
            </template>
        </Column>
        <Column :column="getColumn('last_name')" v-if="isVisible('last_name')" sortable type="string">
            <template #body="{ row }">
                <a :href="row.edit_user_route" title="Edit" v-html="r('last_name', row.last_name)"></a>
            </template>
        </Column>
        <Column :column="getColumn('email')" v-if="isVisible('email')" sortable type="string">
            <template #body="{ row }">
                <a :href="row.edit_user_route" title="Edit" v-html="r('email', row.email)"></a>
            </template>
        </Column>
        <Column :column="getColumn('role_name')" v-if="isVisible('role_name')" sortable type="string"
                class="text-center">
            <template #body="{ row, setConstraintsCb }">
                <Button size="sm" @click="setConstraintsCb(row.role_name)" v-html="r('role_name', startCase(row.role_name))"/>
            </template>
        </Column>
        <Column :column="getColumn('joined_at')" v-if="isVisible('joined_at')" sortable type="date">
            <template #body="{ row }">
                <a :href="row.edit_user_route"
                   title="Edit"
                >
                    {{ formatDate(row.joined_at, dateSettings.format, dateSettings.separator) }}
                </a>
            </template>
        </Column>
        <Column :column="getColumn('created_at')" v-if="isVisible('created_at')" sortable type="date">
            <template #body="{ row }">
                <a :href="row.edit_user_route"
                   title="Edit"
                >
                    {{ formatDate(row.created_at, dateSettings.format, dateSettings.separator) }}
                </a>
            </template>
        </Column>
        <Column :column="getColumn('updated_at')" v-if="isVisible('updated_at')" sortable type="date">
            <template #body="{ row }">
                <a :href="row.edit_user_route"
                   title="Edit"
                >
                    {{ formatDate(row.updated_at, dateSettings.format, dateSettings.separator) }}
                </a>
            </template>
        </Column>
        <Column :column="getColumn('active')" v-if="isVisible('active')" sortable type="boolean"
                class="text-center">
            <template #body="{ row, setConstraintsCb }">
                <button :class="{'text-green-500': row.active, 'text-red-500': !row.active}"
                        @click="setConstraintsCb(row.active)"
                >
                    <i v-if="row.active" class="pi pi-check-circle" title="Active"></i>
                    <i v-else class="pi pi-times-circle" title="Inactive"></i>
                </button>
            </template>
        </Column>
    </DataTable>
</template>
