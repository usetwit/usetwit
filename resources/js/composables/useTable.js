import { cloneDeep, debounce, difference } from 'lodash'
import { watch } from 'vue'

export function useTable(defaultData, fetchFn, storageInstance) {

    const { activeData, saveToStorage } = storageInstance

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
        activeData.value.pagination.page = 1
        const { filters } = activeData.value

        Object.keys(filters).forEach(key => {
            filters[key].constraints = [{ value: null, mode: filters[key].constraints[0].mode }]
        })

        filter()
    }

    const clearFilter = (field) => {
        activeData.value.pagination.page = 1
        activeData.value.filters[field].constraints = [{ value: null, mode: activeData.value.filters[field].constraints[0].mode }]
    }

    const clearSort = (field) => {
        activeData.value.sort = activeData.value.sort.filter(item => item.field !== field)
    }

    const getModifiedFields = (filters, filtered) => {
        filters = getFilteredFields()
        return difference(filters, filtered)
    }

    const reset = () => {
        activeData.value.pagination.page = 1
        activeData.value.filters = cloneDeep(defaultData.filters)

        const filteredFields = getFilteredFields()

        activeData.value.columns.forEach(column => {
            column.visible = column.visible ? true : filteredFields.includes(column.field)
        })

        filter()
    }

    const debouncedFetchFn = debounce(fetchFn, 300, { leading: true, trailing: true })

    const save = (fetch = true) => {
        saveToStorage()

        if (fetch) {
            debouncedFetchFn()
        }
    }

    const filter = (doFetch = true) => {
        activeData.value.filtered = getFilteredFields(activeData.value.filters)
        save(doFetch)
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
    }
}
