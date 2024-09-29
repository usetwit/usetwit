import { ref } from 'vue'
import { cloneDeep } from 'lodash'

export function useStorage(key, defaultData) {
    const storedData = JSON.parse(localStorage.getItem(key))
    const activeData = ref(storedData || cloneDeep(defaultData))

    const saveToStorage = () => {
        // localStorage.setItem(key, JSON.stringify(activeData.value))
    }

    return { activeData, saveToStorage }
}
