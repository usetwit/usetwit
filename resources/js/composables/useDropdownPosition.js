import { onBeforeUnmount, onMounted, ref } from 'vue'

export function useDropdownPosition(positionX = 'left', positionY = 'bottom', maxHeight = -1) {
    const inputRef = ref(null)
    const dropdownStyle = ref()
    const showDropdown = ref(false)

    const updateDropdownPosition = () => {
        if (inputRef.value instanceof HTMLElement) {
            console.log(inputRef.value.getBoundingClientRect())
        }
    }

    const toggleDropdown = () => {
        showDropdown.value = !showDropdown.value

        if (showDropdown.value) {
            updateDropdownPosition()
        }
    }

    const handleResize = () => {
        if (showDropdown.value) {
            updateDropdownPosition()
        }
    }

    const handleScroll = () => {
        if (showDropdown.value) {
            updateDropdownPosition()
        }
    }

    onMounted(() => {
        window.addEventListener('resize', handleResize)
        window.addEventListener('scroll', handleScroll)
    })

    onBeforeUnmount(() => {
        window.removeEventListener('resize', handleResize)
        window.removeEventListener('scroll', handleScroll)
    })

    return {
        inputRef,
        dropdownStyle,
        showDropdown,
        toggleDropdown,
    }

    return {
        inputRef,
        dropdownStyle,
        showDropdown,
        toggleDropdown,
    }
}
