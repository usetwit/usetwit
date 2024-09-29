import { cloneDeep, difference } from 'lodash'
import { useDebounce } from './useDebounce.js'
import { useStorage } from './useStorage.js'
import { watch } from 'vue'

export function useTable(storageKey, defaultData, fetchFn, dateSettings = null) {

    const { activeData, saveToStorage } = useStorage(storageKey, defaultData)

    const getColumn = field => {
        return activeData.value.columns.find(col => col.field === field)
    }

    watch(activeData, () => {
        save(false)
    }, { deep: true })

    const sort = (column, removeOtherSorts = false) => {
        const sortField = activeData.value.sort.find(col => col.field === column.field)

        if (sortField) {
            if (sortField.order === 'desc') {
                activeData.value.sort = activeData.value.sort.filter(col => col.field !== column.field)
            } else {
                sortField.order = 'desc'
            }
        } else {
            activeData.value.sort.push({ field: column.field, order: 'asc' })
        }

        if (removeOtherSorts) {
            activeData.value.sort = activeData.value.sort.filter(col => col.field === column.field)
        }
    }

    const getModeFromMap = fieldType => {
        const modeMapping = {
            number: 'equals',
            date: 'date_equals',
            boolean: 'equals',
            string: 'contains',
        }

        return modeMapping[fieldType] || 'contains'
    }

    const getFilteredFields = () => {
        const { filters } = activeData.value

        return Object.keys(filters).filter(key => {
            return filters[key].constraints.some(({ value }) => value !== null && value !== '')
        })
    }

    const clearFilters = () => {
        const { filters } = activeData.value

        Object.keys(filters).forEach(key => {
            filters[key].constraints = [{ value: null, mode: filters[key].constraints[0].mode }]
        })

        filter()
    }

    const clearFilter = (field) => {
        activeData.value.filters[field].constraints = [{ value: null, mode: activeData.value.filters[field].constraints[0].mode }]
    }

    const clearSort = (field) => {
        activeData.value.sort = activeData.value.sort.filter(item => item.field !== field)
    }

    const getModifiedFields = (filters, filtered) => {
        filters = getFilteredFields(filters)
        return difference(filters, filtered)
    }

    const reset = () => {
        activeData.value.filters = cloneDeep(defaultData.filters)
        filter()
    }

    const debouncedFetchFn = useDebounce(fetchFn)

    const save = (doFetchUsers = true) => {
        saveToStorage()

        if (doFetchUsers) {
            debouncedFetchFn()
        }
    }

    const filter = (doFetchUsers = true) => {
        activeData.value.filtered = getFilteredFields(activeData.value.filters)
        save(doFetchUsers)
    }

    filter()

    return {
        getColumn,
        getModeFromMap,
        getFilteredFields,
        getModifiedFields,
        reset,
        save,
        filter,
        clearFilters,
        clearFilter,
        clearSort,
        sort,
        activeData,
        defaultData,
    }
}
