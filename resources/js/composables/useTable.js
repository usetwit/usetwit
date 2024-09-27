import { difference } from 'lodash'

export function useTable(activeData = null) {
    const getColumn = field => {
        return activeData.value.columns.find(col => col.field === field)
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

    const getFilteredFields = filters => {
        return Object.keys(filters).filter(key => {
            return filters[key].constraints.some(constraint => constraint.value !== null && constraint.value !== '')
        })
    }

    const getModifiedFields = (filters, filtered) => {
        filters = getFilteredFields(filters)
        console.log('filters', filters, 'filtered', filtered)

        return difference(filters, filtered)
    }

    return { getColumn, getModeFromMap, getFilteredFields, getModifiedFields }
}
