import { nextTick, onBeforeUnmount, onMounted, ref } from 'vue'

export function useDropdownPosition(positionX = 'left', positionY = 'bottom', setMinWidth = null, maxHeight = 0, gap = 2) {
    const inputRef = ref(null)
    const dropdownRef = ref(null)
    const buttonRef = ref(null)
    const showDropdown = ref(false)
    const dropdownStyle = ref({
        left: 'auto',
        right: 'auto',
        top: 'auto',
        bottom: 'auto',
        minWidth: 'auto',
        maxHeight: maxHeight > 0 ? `${maxHeight}px` : 'auto',
    })

    const updateDropdownPosition = () => {
        if (inputRef.value instanceof HTMLElement && dropdownRef.value instanceof HTMLElement) {
            const inputRect = inputRef.value.getBoundingClientRect()
            const dropdownRect = dropdownRef.value.getBoundingClientRect()

            const top = inputRect.top + window.scrollY
            const bottom = inputRect.bottom + window.scrollY
            const left = inputRect.left + window.scrollX
            const right = inputRect.right + window.scrollX

            const viewportHeight = window.innerHeight
            const spaceBelow = viewportHeight - inputRect.bottom
            const spaceAbove = inputRect.top

            if (setMinWidth === true) {
                dropdownStyle.value.minWidth = `${inputRect.width}px`
            } else if (typeof setMinWidth === 'number' && setMinWidth > 0) {
                dropdownStyle.value.minWidth = `${setMinWidth}px`
            } else {
                dropdownStyle.value.minWidth = 'auto'
            }

            dropdownStyle.value.left = `${left}px`

            if (positionY === 'bottom') {
                if (spaceBelow >= dropdownRect.height + gap) {
                    setDropdownPosition(`${bottom + gap}px`, 'auto')
                } else if (spaceAbove >= dropdownRect.height + gap) {
                    setDropdownPosition('auto', `${viewportHeight - top + gap}px`)
                } else {
                    setDropdownPosition(`${bottom + gap}px`, 'auto')
                }
            } else if (positionY === 'top') {
                if (spaceAbove >= dropdownRect.height + gap) {
                    setDropdownPosition('auto', `${viewportHeight - top + gap}px`)
                } else if (spaceBelow >= dropdownRect.height + gap) {
                    setDropdownPosition(`${bottom + gap}px`, 'auto')
                } else {
                    setDropdownPosition('auto', `${viewportHeight - top + gap}px`)
                }
            }
        }
    }

    const setDropdownPosition = (topValue, bottomValue) => {
        dropdownStyle.value.top = topValue
        dropdownStyle.value.bottom = bottomValue
    }

    const toggleDropdown = async () => {
        showDropdown.value = !showDropdown.value

        if (showDropdown.value) {
            await nextTick()
            updateDropdownPosition()
        }
    }

    const setShowDropdown = (value) => {
        showDropdown.value = value
    }

    const handleResizeOrScroll = () => {
        if (showDropdown.value) {
            updateDropdownPosition()
        }
    }

    const handleClickOutside = (event) => {
        if (
            !dropdownRef.value?.contains(event.target) &&
            !inputRef.value?.contains(event.target) &&
            !buttonRef.value?.contains(event.target)
        ) {
            setShowDropdown(false)
        }
    }

    onMounted(() => {
        window.addEventListener('resize', handleResizeOrScroll)
        window.addEventListener('scroll', handleResizeOrScroll)
        document.addEventListener('click', handleClickOutside)
    })

    onBeforeUnmount(() => {
        window.removeEventListener('resize', handleResizeOrScroll)
        window.removeEventListener('scroll', handleResizeOrScroll)
        document.removeEventListener('click', handleClickOutside)
    })

    return {
        inputRef,
        dropdownRef,
        buttonRef,
        dropdownStyle,
        showDropdown,
        toggleDropdown,
    }
}
