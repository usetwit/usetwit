<script setup>
import {provide, ref} from 'vue'
import useAxios from '@/composables/useAxios.js'
import DataTable from '@/components/DataTable/DataTable.vue'
import Column from '@/components/DataTable/Column.vue'
import useTable from '@/composables/useTable.js'
import {formatDate, applyFilterRegex} from '@/app/helpers.js'
import useStorage from '@/composables/useStorage.js'
import Button from "@/components/Form/Button.vue";
import {flagEmoji} from "@/helpers/helpers.js";

const props = defineProps({
    paginationSettings: {type: Object, required: true},
    dateSettings: {type: Object, required: true},
    routeGetLocations: {type: String, required: true},
})

const rows = ref([])
const isLoading = ref(false)
const defaultData = {
    filters: {
        global: {constraints: [{value: null, mode: 'contains'}]},
        id: {operator: 'or', constraints: [{value: null, mode: 'contains'}]},
        name: {operator: 'or', constraints: [{value: null, mode: 'contains'}]},
        active: {constraints: [{value: true, mode: 'equals'}]},
        address_line_1: {operator: 'or', constraints: [{value: null, mode: 'contains'}]},
        address_line_2: {operator: 'or', constraints: [{value: null, mode: 'contains'}]},
        address_line_3: {operator: 'or', constraints: [{value: null, mode: 'contains'}]},
        postcode: {operator: 'or', constraints: [{value: null, mode: 'contains'}]},
        country_name: {operator: 'or', constraints: [{value: null, mode: 'contains'}]},
        country_code: {operator: 'or', constraints: [{value: null, mode: 'contains'}]},
        created_at: {operator: 'or', constraints: [{value: null, mode: 'date_equals'}]},
        updated_at: {operator: 'or', constraints: [{value: null, mode: 'date_equals'}]},
    },
    filtered: [],
    columns: [
        {field: 'id', label: 'ID', visible: true, order: 1},
        {field: 'name', label: 'Name', visible: true, order: 2},
        {field: 'address_line_1', label: 'Line 1', visible: true, order: 3},
        {field: 'address_line_2', label: 'Line 2', visible: true, order: 4},
        {field: 'address_line_3', label: 'Line 3', visible: true, order: 5},
        {field: 'postcode', label: 'Postal Code', visible: true, order: 6},
        {field: 'country_name', label: 'Country', visible: true, order: 7},
        {field: 'country_code', label: 'Country Code', visible: true, order: 8},
        {field: 'created_at', label: 'Created Date', visible: false, order: 9},
        {field: 'updated_at', label: 'Updated Date', visible: false, order: 10},
        {field: 'active', label: 'Active', visible: true, order: 11},
    ],
    sort: [{field: 'name', order: 'asc'}],
    pagination: {
        page: 1,
        per_page: props.paginationSettings.per_page.default,
        total: 0,
    }
}

const storageInstance = useStorage('locations-index', defaultData)
const {activeData} = storageInstance

const fetchLocations = async () => {
    isLoading.value = true

    const {data, errors, getResponse} = useAxios(props.routeGetLocations, {
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

const {getColumn, isVisible, getSearchGlobalValue, getSearchValues} = tableInstance

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
        <Column :column="getColumn('id')" v-if="isVisible('id')" sortable type="string">
            <template #body="{ row }">
                <a :href="row.edit_location_route" title="Edit" v-html="r('id', row.id)"></a>
            </template>
        </Column>
        <Column :column="getColumn('name')" v-if="isVisible('name')" sortable type="string">
            <template #body="{ row }">
                <a :href="row.edit_location_route" title="Edit" v-html="r('name', row.name)"></a>
            </template>
        </Column>
        <Column :column="getColumn('address_line_1')" v-if="isVisible('address_line_1')" sortable type="string">
            <template #body="{ row }">
                <a :href="row.edit_location_route" title="Edit" v-html="r('address_line_1', row.address_line_1)"></a>
            </template>
        </Column>
        <Column :column="getColumn('address_line_2')" v-if="isVisible('address_line_2')" sortable type="string">
            <template #body="{ row }">
                <a :href="row.edit_location_route" title="Edit" v-html="r('address_line_2', row.address_line_2)"></a>
            </template>
        </Column>
        <Column :column="getColumn('address_line_3')" v-if="isVisible('address_line_3')" sortable type="string">
            <template #body="{ row }">
                <a :href="row.edit_location_route" title="Edit" v-html="r('address_line_3', row.address_line_3)"></a>
            </template>
        </Column>
        <Column :column="getColumn('postcode')" v-if="isVisible('postcode')" sortable type="string">
            <template #body="{ row }">
                <a :href="row.edit_location_route" title="Edit" v-html="r('postcode', row.postcode)"></a>
            </template>
        </Column>
        <Column :column="getColumn('country_name')" v-if="isVisible('country_name')" sortable type="string">
            <template #body="{ row }">
                <a :href="row.edit_location_route" title="Edit" v-html="r('country_name', row.country_name)"></a>
            </template>
        </Column>
        <Column :column="getColumn('country_code')" v-if="isVisible('country_code')" sortable type="string" class="text-center">
            <template #body="{ row, setConstraintsCb }">
                <Button size="sm"
                        @click="setConstraintsCb(row.country_code)"
                >
                    <span v-html="r('country_code', row.country_code)"></span>
                    <span :title="row.country_code" class="ml-1">{{ flagEmoji(row.country_code) }}</span>
                </Button>
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
        <Column :column="getColumn('active')" v-if="isVisible('active')" sortable type="boolean" class="text-center">
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
