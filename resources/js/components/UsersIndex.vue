<script setup>
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Badge from 'primevue/badge'
import Tag from 'primevue/tag'
import Button from 'primevue/button'
import Chip from 'primevue/chip'
import Select from 'primevue/select'
import Checkbox from 'primevue/checkbox'
import DatePicker from 'primevue/datepicker'
import IconField from 'primevue/iconfield'
import InputIcon from 'primevue/inputicon'
import InputText from 'primevue/inputtext'
import MultiSelect from 'primevue/multiselect'
import Toast from 'primevue/toast'
import {FilterMatchMode, FilterOperator} from '@primevue/core/api'
import {ref} from 'vue'
import {useAxios} from '../composables/useAxios'
import {useToast} from 'primevue/usetoast'
import {useDebounce} from '../composables/useDebounce'
import {useStorage} from '../composables/useStorage'
import {useFilter} from '../composables/useFilter'

const toast = useToast()

const props = defineProps({
    defaultPerPage: {
        required: true,
        type: Number,
    },
    perPageOptions: {
        required: true,
        type: Array,
    },
    routeGetUsers: {
        required: true,
        type: String,
    },
});

const users = ref([])
const roles = ref([])
const isLoading = ref(false)
const defaultData = {
    filters: {
        global: {
            constraints: [{value: null, matchMode: FilterMatchMode.CONTAINS}],
        },
        username: {
            operator: FilterOperator.AND,
            constraints: [{value: null, matchMode: FilterMatchMode.CONTAINS}],
        },
        first_name: {
            operator: FilterOperator.AND,
            constraints: [{value: null, matchMode: FilterMatchMode.STARTS_WITH}],
        },
        last_name: {
            operator: FilterOperator.AND,
            constraints: [{value: null, matchMode: FilterMatchMode.STARTS_WITH}],
        },
        middle_names: {
            operator: FilterOperator.AND,
            constraints: [{value: null, matchMode: FilterMatchMode.STARTS_WITH}],
        },
        full_name: {
            operator: FilterOperator.AND,
            constraints: [{value: null, matchMode: FilterMatchMode.CONTAINS}],
        },
        employee_id: {
            operator: FilterOperator.AND,
            constraints: [{value: null, matchMode: FilterMatchMode.CONTAINS}],
        },
        email: {
            operator: FilterOperator.AND,
            constraints: [{value: null, matchMode: FilterMatchMode.CONTAINS}],
        },
        join_date: {
            operator: FilterOperator.AND,
            constraints: [{value: null, matchMode: FilterMatchMode.DATE_IS}],
        },
        role: {
            operator: FilterOperator.OR,
            constraints: [{value: null, matchMode: FilterMatchMode.EQUALS}],
        },
        active: {
            operator: FilterOperator.OR,
            constraints: [{value: true, matchMode: FilterMatchMode.EQUALS}],
        },
    },
    columns: [
        {field: 'username', header: 'Username', default: true},
        {field: 'full_name', header: 'Full Name', default: true},
        {field: 'first_name', header: 'First Name', default: false},
        {field: 'middle_names', header: 'Middle Name(s)', default: false},
        {field: 'last_name', header: 'Last Name', default: false},
        {field: 'email', header: 'Company Email', default: false},
        {field: 'employee_id', header: 'Employee ID', default: true},
        {field: 'join_date', header: 'Join Date', default: false},
        {field: 'role', header: 'Role', default: true},
        {field: 'active', header: 'Active', default: true},
    ],
    selected_columns: [],
    sort: [{field: 'username', order: 1}],
    pagination: {
        first: 0,
        page: 1,
        per_page: props.defaultPerPage,
        total: 0,
    }
}

defaultData.selected_columns = defaultData.columns.filter(col => col.default === true)

const save = (doFetchUsers = true) => {
    saveToStorage()

    if (doFetchUsers) {
        fetchUsers()
    }
}
const debouncedSave = useDebounce(save)

const {activeData, saveToStorage} = useStorage('users-index', defaultData)
const {
    removeFilter,
    changeDate,
    getHeading,
    clearFilters,
    resetFilters,
    updateSelectedColumns,
    filteredColumns,
    isFilteredClass,
    updatePage,
    updateSort,
} = useFilter(activeData, defaultData, debouncedSave)

const fetchUsers = async () => {
    isLoading.value = true

    const {data, errors, getResponse} = useAxios(props.routeGetUsers, {
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

        toast.add({
            severity: 'error',
            summary: errors.value.message,
            group: 'br',
            detail: errors.value.list,
            life: 3000
        })
    }

    isLoading.value = false
}
fetchUsers()
</script>

<template>
    <Toast position="bottom-right" group="br"/>

    <DataTable :value="users"
               :totalRecords="activeData.pagination.total"
               :first="activeData.pagination.first"
               :rows="activeData.pagination.per_page"
               @page="updatePage"
               @sort="updateSort"
               v-model:filters="activeData.filters"
               :rowsPerPageOptions="props.perPageOptions"
               lazy
               paginator
               :loading="isLoading"
               scrollable
               sortMode="multiple"
               :multiSortMeta="activeData.sort"
               removableSort
               filterDisplay="menu"
               paginatorPosition="both"
               paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
               currentPageReportTemplate="{first} to {last} of {totalRecords}"
    >
        <template #header>
            <span>Select Fields</span>
            <div class="overflow-auto mb-4">
                <MultiSelect :modelValue="activeData.selected_columns"
                             :options="activeData.columns"
                             optionLabel="header"
                             @update:modelValue="updateSelectedColumns"
                             filter
                             display="chip"
                             placeholder="Select Columns"
                             class="w-full"/>
            </div>
            <div class="flex items-center justify-between mb-4 mt-8">
                <div>
                    <span>Filters</span>
                    <div class="mb-2">
                        <Chip v-for="field in filteredColumns" :key="field"
                              :label="getHeading(field)" @remove="removeFilter(field)"
                              removable/>
                    </div>
                    <div class="flex items-center">
                        <Button type="button" severity="contrast" icon="pi pi-refresh" label="Reset Filters"
                                @click="resetFilters" :badge="filteredColumns.length.toString()"
                                size="small"/>
                        <Button type="button" icon="pi pi-filter-slash" label="Clear Filters" outlined
                                @click="clearFilters" class="ml-2" :badge="filteredColumns.length.toString()"
                                size="small"/>
                    </div>
                </div>
                <div class="flex items-center">
                    <IconField>
                        <InputIcon>
                            <i class="pi pi-search"/>
                        </InputIcon>
                        <InputText v-model="activeData.filters['global'].value"
                                   placeholder="Keyword Search"
                                   class="w-60"/>
                    </IconField>
                    <Badge
                        class="hover:cursor-pointer mx-2"
                        size="small"
                        severity="contrast"
                        v-tooltip.left="'Hold ctrl to sort on multiple columns'"
                    >
                        <i class="pi pi-info"></i>
                    </Badge>
                </div>
            </div>
        </template>
        <Column header="" frozen>
            <template #body="{data}">
                <a :href="data.edit_user_route" class="bg-yellow-500 py-1 px-1.5 rounded text-white" title="Edit"><i
                    class="pi pi-pen-to-square"></i></a>
            </template>
        </Column>
        <Column field="username"
                header="Username"
                sortable
                v-if="activeData.selected_columns.some(col => col.field === 'username')"
                :headerClass="isFilteredClass('username')"
        >
            <template #body="{data}">
                <a :href="data.edit_user_route">{{ data.username }}</a>
            </template>
            <template #filter="{filterModel}">
                <InputText v-model="filterModel.value"
                           type="text"
                           placeholder="Search"
                           class="w-40"
                />
            </template>
        </Column>
        <Column field="full_name"
                header="Full Name"
                sortable
                v-if="activeData.selected_columns.some(col => col.field === 'full_name')"
                :headerClass="isFilteredClass('full_name')"
        >
            <template #body="{data}">
                <a :href="data.edit_user_route">{{ data.full_name }}</a>
            </template>
            <template #filter="{filterModel}">
                <InputText v-model="filterModel.value"
                           type="text"
                           placeholder="Search"
                           class="w-40"
                />
            </template>
        </Column>
        <Column field="first_name"
                header="First Name"
                sortable
                v-if="activeData.selected_columns.some(col => col.field === 'first_name')"
                :headerClass="isFilteredClass('first_name')"
        >
            <template #body="{data}">
                <a :href="data.edit_user_route">{{ data.first_name }}</a>
            </template>
            <template #filter="{filterModel}">
                <InputText v-model="filterModel.value"
                           type="text"
                           placeholder="Search"
                           class="w-40"
                />
            </template>
        </Column>
        <Column field="middle_names"
                header="Middle Name(s)"
                sortable
                v-if="activeData.selected_columns.some(col => col.field === 'middle_names')"
                :headerClass="isFilteredClass('middle_names')"
        >
            <template #body="{data}">
                <a :href="data.edit_user_route">{{ data.middle_names }}</a>
            </template>
            <template #filter="{filterModel}">
                <InputText v-model="filterModel.value"
                           type="text"
                           placeholder="Search"
                           class="w-40"
                />
            </template>
        </Column>
        <Column field="last_name"
                header="Last Name"
                sortable
                v-if="activeData.selected_columns.some(col => col.field === 'last_name')"
                :headerClass="isFilteredClass('last_name')"
        >
            <template #body="{data}">
                <a :href="data.edit_user_route">{{ data.last_name }}</a>
            </template>
            <template #filter="{filterModel}">
                <InputText v-model="filterModel.value"
                           type="text"
                           placeholder="Search"
                           class="w-40"
                />
            </template>
        </Column>
        <Column field="employee_id"
                header="Employee ID"
                sortable
                v-if="activeData.selected_columns.some(col => col.field === 'employee_id')"
                :headerClass="isFilteredClass('employee_id')"
        >
            <template #body="{data}">
                <a :href="data.edit_user_route">{{ data.employee_id }}</a>
            </template>
            <template #filter="{filterModel}">
                <InputText v-model="filterModel.value"
                           type="text"
                           placeholder="Search"
                           class="w-40"
                />
            </template>
        </Column>
        <Column field="email"
                header="Company Email"
                sortable
                v-if="activeData.selected_columns.some(col => col.field === 'email')"
                :headerClass="isFilteredClass('email')"
        >
            <template #body="{data}">
                <a :href="data.edit_user_route">{{ data.email }}</a>
            </template>
            <template #filter="{filterModel}">
                <InputText v-model="filterModel.value"
                           type="text"
                           placeholder="Search"
                           class="w-40"
                />
            </template>
        </Column>
        <Column field="join_date"
                header="Join Date"
                sortable
                dataType="date"
                v-if="activeData.selected_columns.some(col => col.field === 'join_date')"
                :headerClass="isFilteredClass('join_date')"
        >
            <template #body="{data}">
                <a :href="data.edit_user_route">{{ data.join_date }}</a>
            </template>
            <template #filter="{filterModel}">
                <DatePicker v-model="filterModel.value"
                            placeholder="yyyy-mm-dd"
                            showIcon
                            :showOnFocus="false"
                />
            </template>
        </Column>
        <Column field="role"
                header="Role"
                sortable
                v-if="activeData.selected_columns.some(col => col.field === 'role')"
                :headerClass="isFilteredClass('role')"
        >
            <template #body="{data}">
                <Tag :value="data.role" severity="contrast"/>
            </template>
            <template #filter="{filterModel}">
                <Select v-model="filterModel.value"
                        :options="roles"
                        placeholder="Select Role"
                >
                    <template #option="slotProps">
                        <Tag :value="slotProps.option" severity="contrast"/>
                    </template>
                </Select>
            </template>
        </Column>
        <Column field="active"
                header="Active"
                dataType="boolean"
                sortable
                v-if="activeData.selected_columns.some(col => col.field === 'active')"
                :headerClass="isFilteredClass('active')"
        >
            <template #body="{data}">
                <i class="pi"
                   :class="{ 'pi-check-circle text-green-500': data.active, 'pi-times-circle text-red-400': !data.active }"></i>
            </template>
            <template #filter="{filterModel}">
                <Checkbox v-model="filterModel.value" :indeterminate="filterModel.value === null" binary/>
            </template>
        </Column>

        <template #footer> In total there are {{ activeData.pagination.total }} users.</template>
    </DataTable>
</template>

<style scoped>
::v-deep(.filtered) button {
    @apply text-yellow-500;
}
</style>
