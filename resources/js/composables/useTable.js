import { cloneDeep, difference } from 'lodash'
import { useDebounce } from './useDebounce.js'
import { useStorage } from './useStorage.js'
import { watch } from 'vue'

export function useTable(storageKey = null, defaultData = null, fetchFn = null) {

    const { activeData, saveToStorage } = useStorage(storageKey, defaultData)

    const getColumn = field => {
        return activeData.value.columns.find(col => col.field === field)
    }

    watch(activeData, () => {
        save(false)
    }, { deep: true })

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
            const { constraints } = filters[key]
            return constraints.some(({ value }) => value !== null && value !== '')
        })
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
        activeData,
        defaultData,
    }
}
