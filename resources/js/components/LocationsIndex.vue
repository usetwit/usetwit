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
    routeGetLocations: { type: String, required: true },
})

const rows = ref([])
const isLoading = ref(false)
const defaultData = {
    filters: {
        global: { constraints: [{ value: null, mode: 'contains' }] },
        id: { operator: 'or', constraints: [{ value: null, mode: 'contains' }] },
        name: { operator: 'or', constraints: [{ value: null, mode: 'contains' }] },
        created_at: { operator: 'or', constraints: [{ value: null, mode: 'date_equals' }] },
        updated_at: { operator: 'or', constraints: [{ value: null, mode: 'date_equals' }] },
    },
    filtered: [],
    columns: [
        { field: 'id', label: 'ID', visible: true, order: 1 },
        { field: 'name', label: 'Name', visible: true, order: 2 },
        { field: 'created_at', label: 'Created Date', visible: false, order: 3 },
        { field: 'updated_at', label: 'Updated Date', visible: false, order: 4 },
    ],
    sort: [{ field: 'name', order: 'asc' }],
    pagination: {
        page: 1,
        per_page: props.paginationSettings.per_page.default,
        total: 0,
    }
}

const storageInstance = useStorage('locations-index', defaultData)
const { activeData } = storageInstance

const fetchLocations = async () => {
    isLoading.value = true

    const { data, errors, getResponse } = useAxios(props.routeGetLocations, {
        filters: activeData.value.filters,
        page: activeData.value.pagination.page,
        per_page: activeData.value.pagination.per_page,
        sort: activeData.value.sort,
        visible: activeData.value.columns.filter(col => col.visible).map(col => col.field)
    })

    await getResponse()

    if (!errors.value.raw) {
        rows.value = data.value.locations
        activeData.value.pagination.total = data.value.total
    } else {
        rows.value = []
    }

    isLoading.value = false
}

const tableInstance = useTable(defaultData, fetchLocations, storageInstance)

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
                <a :href="row.edit_location_route"
                   class="bg-amber-500 p-1.5 rounded-sm text-white inline-flex"
                   title="Edit"
                >
                    <i class="pi pi-pen-to-square"></i>
                </a>
            </template>
        </Column>
        <Column :column="getColumn('name')" v-if="isVisible('name')" sortable type="string">
            <template #body="{ row }">
                <a :href="row.edit_location_route" title="Edit" v-html="r('name', row.name)"></a>
            </template>
        </Column>
        <Column :column="getColumn('id')" v-if="isVisible('id')" sortable type="string">
            <template #body="{ row }">
                <a :href="row.edit_location_route" title="Edit" v-html="r('id', row.id)"></a>
            </template>
        </Column>
        <Column :column="getColumn('full_name')" v-if="isVisible('full_name')" sortable type="string">
            <template #body="{ row }">
                <a :href="row.edit_location_route" title="Edit" v-html="r('full_name', row.full_name)"></a>
            </template>
        </Column>
        <Column :column="getColumn('first_name')" v-if="isVisible('first_name')" sortable type="string">
            <template #body="{ row }">
                <a :href="row.edit_location_route" title="Edit" v-html="r('first_name', row.first_name)"></a>
            </template>
        </Column>
        <Column :column="getColumn('middle_names')" v-if="isVisible('middle_names')" sortable type="string">
            <template #body="{ row }">
                <a :href="row.edit_location_route" title="Edit" v-html="r('middle_names', row.middle_names)"></a>
            </template>
        </Column>
        <Column :column="getColumn('last_name')" v-if="isVisible('last_name')" sortable type="string">
            <template #body="{ row }">
                <a :href="row.edit_location_route" title="Edit" v-html="r('last_name', row.last_name)"></a>
            </template>
        </Column>
        <Column :column="getColumn('email')" v-if="isVisible('email')" sortable type="string">
            <template #body="{ row }">
                <a :href="row.edit_location_route" title="Edit" v-html="r('email', row.email)"></a>
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
                <a :href="row.edit_location_route"
                   title="Edit"
                >
                    {{ formatDate(row.joined_at, dateSettings.format, dateSettings.separator) }}
                </a>
            </template>
        </Column>
        <Column :column="getColumn('created_at')" v-if="isVisible('created_at')" sortable type="date">
            <template #body="{ row }">
                <a :href="row.edit_location_route"
                   title="Edit"
                >
                    {{ formatDate(row.created_at, dateSettings.format, dateSettings.separator) }}
                </a>
            </template>
        </Column>
        <Column :column="getColumn('updated_at')" v-if="isVisible('updated_at')" sortable type="date">
            <template #body="{ row }">
                <a :href="row.edit_location_route"
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
