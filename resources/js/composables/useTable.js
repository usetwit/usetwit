import { ref } from 'vue'

export function useTable(activeData) {
    const getColumn = field => {
        return activeData.value.columns.find(col => col.field === field)
    }

    return { getColumn }
}
