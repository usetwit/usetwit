import { ref } from 'vue'

export function useStorage(key, defaultValue) {
    const storedData = JSON.parse(localStorage.getItem(key))
    const activeData = ref(storedData || defaultValue)

    const saveToStorage = () => {
        localStorage.setItem(key, JSON.stringify(activeData.value))
    }

    return { activeData, saveToStorage }
}
