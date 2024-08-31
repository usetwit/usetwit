import {computed, watch} from 'vue'
import cloneDeep from 'lodash/cloneDeep'

export function useFilter(activeData, defaultData, saveFn) {
    activeData.value.selected_columns = activeData.value.selected_columns.sort((a, b) => a.header.localeCompare(b.header))
    activeData.value.columns = activeData.value.columns.sort((a, b) => a.header.localeCompare(b.header))

    const updatePage = (event) => {
        activeData.value.pagination.first = event.first
        activeData.value.pagination.per_page = event.rows
        activeData.value.pagination.page = event.page + 1
        saveFn()
    }

    const updateSort = (event) => {
        activeData.value.sort = event.multiSortMeta
        saveFn()
    }

    watch(() => activeData.value.filters, saveFn, {deep: true})

    const filteredColumns = computed(() =>
        Object.keys(activeData.value.filters).filter(key =>
            activeData.value.filters[key].constraints.some(
                constraint => constraint.value !== null
            )
        )
    )

    const getHeading = (field) => {
            return activeData.value.columns.find(col => col.field === field).header
    }

    const removeFilter = (field) => {
        activeData.value.filters[field].constraints[0].value = null;
        activeData.value.filters[field].constraints = [activeData.value.filters[field].constraints[0]];
    }

    const resetFilters = () => {
        activeData.value.filters = cloneDeep(defaultData.filters)
    }

    const clearFilters = () => {
        Object.keys(activeData.value.filters).forEach((key) => {
            activeData.value.filters[key].constraints[0].value = null;
            activeData.value.filters[key].constraints = [activeData.value.filters[key].constraints[0]];
        })
    }

    const updateSelectedColumns = (val) => {
        const sortLength = activeData.value.sort.length

        activeData.value.selected_columns = activeData.value.columns.filter(col =>
            val.some(v => v.field === col.field)
        )

        activeData.value.sort = activeData.value.sort.filter(sortItem =>
            activeData.value.selected_columns.some(col => col.field === sortItem.field)
        )

        Object.keys(activeData.value.filters).forEach(key => {
            const filter = activeData.value.filters[key]
            if (!activeData.value.selected_columns.some(col => col.field === key) && filter.constraints.length > 0) {
                filter.constraints = [{...filter.constraints[0], value: null}]
            }
        })

        return sortLength !== activeData.value.sort.length
    }

    const isFilteredClass = (field) => {
        return filteredColumns.value.includes(field) ? 'filtered' : ''
    }

    const changeDate = (filterModel) => {
        const year = filterModel.value.getFullYear()
        const month = (filterModel.value.getMonth() + 1).toString().padStart(2, '0')
        const day = filterModel.value.getDate().toString().padStart(2, '0')

        filterModel.value = `${year}-${month}-${day}`
    }

    return {
        removeFilter,
        changeDate,
        getHeading,
        filteredColumns,
        clearFilters,
        resetFilters,
        updateSelectedColumns,
        isFilteredClass,
        updateSort,
        updatePage
    }
}
