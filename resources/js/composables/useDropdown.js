import { nextTick, onBeforeUnmount, onMounted, ref, useTemplateRef, watch } from 'vue'

export function useDropdown(positionX = 'left', positionY = 'bottom', setMinWidth = null, maxHeight = 0, gap = 2) {
    const inputRef = ref(null)
    const dropdownRef = useTemplateRef('dropdownRef')
    const buttonRef = useTemplateRef('buttonRef')
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
            requestAnimationFrame(() => {
                const inputRect = inputRef.value.getBoundingClientRect()
                const dropdownRect = dropdownRef.value.getBoundingClientRect()

                // what you can see
                const viewportHeight = window.innerHeight
                const viewportWidth = window.innerWidth

                const top = inputRect.top + window.scrollY
                const bottom = inputRect.bottom + window.scrollY
                const left = inputRect.left + window.scrollX
                const right = inputRect.right + window.scrollX

                const spaceBelow = viewportHeight - inputRect.bottom
                const spaceAbove = inputRect.top

                if (setMinWidth === true) {
                    dropdownStyle.value.minWidth = `${inputRect.width}px`
                } else if (typeof setMinWidth === 'number' || typeof setMinWidth === 'string' && setMinWidth > 0) {
                    dropdownStyle.value.minWidth = `${setMinWidth}px`
                } else {
                    dropdownStyle.value.minWidth = 'auto'
                }

                if (positionX === 'right') {
                    if (dropdownRect.width > viewportWidth) {
                        dropdownStyle.value.left = '0px'
                    } else if (inputRect.right >= dropdownRect.width) {
                        dropdownStyle.value.left = `${right - dropdownRect.width}px`
                    } else if (viewportWidth - inputRect.left >= dropdownRect.width) {
                        dropdownStyle.value.left = `${left}px`
                    } else {
                        dropdownStyle.value.left = `${right - dropdownRect.width - dropdownRect.left}px`
                    }

                } else if (positionX === 'left') {
                    dropdownStyle.value.left = `${left}px`
                    dropdownStyle.value.right = 'auto'
                }

                if (positionY === 'bottom') {
                    if (spaceBelow >= dropdownRect.height + gap) {
                        setDropdownPositionY(`${bottom + gap}px`, 'auto')
                    } else if (spaceAbove >= dropdownRect.height + gap) {
                        setDropdownPositionY('auto', `${viewportHeight - top + gap}px`)
                    } else {
                        setDropdownPositionY(`${bottom + gap}px`, 'auto')
                    }
                } else if (positionY === 'top') {
                    if (spaceAbove >= dropdownRect.height + gap) {
                        setDropdownPositionY('auto', `${viewportHeight - top + gap}px`)
                    } else if (spaceBelow >= dropdownRect.height + gap) {
                        setDropdownPositionY(`${bottom + gap}px`, 'auto')
                    } else {
                        setDropdownPositionY('auto', `${viewportHeight - top + gap}px`)
                    }
                }
            })
        }
    }

    watch(showDropdown, async () => {
        if (showDropdown.value) {
            await nextTick()
            updateDropdownPosition()
        }
    })

    const setDropdownPositionY = (topValue, bottomValue) => {
        dropdownStyle.value.top = topValue
        dropdownStyle.value.bottom = bottomValue
    }

    const setDropdownPositionX = (leftValue, rightValue) => {
        dropdownStyle.value.left = leftValue
        dropdownStyle.value.right = rightValue
    }

    const toggleDropdown = async () => {
        showDropdown.value = !showDropdown.value

        if (showDropdown.value) {
            await nextTick()
            updateDropdownPosition()
        }
    }

    const setShowDropdown = async (value) => {
        showDropdown.value = value

        if (showDropdown.value) {
            await nextTick()
            updateDropdownPosition()
        }
    }

    const handleScroll = () => {
        if (showDropdown.value) {
            updateDropdownPosition()
        }
    }

    const handleResize = () => {
        showDropdown.value = false
    }

    const handleClickOutside = (event) => {
        if (
            !dropdownRef.value?.contains(event.target) &&
            !inputRef.value?.contains(event.target) &&
            !buttonRef.value?.contains(event.target)
        ) {
            showDropdown.value = false
        }
    }

    onMounted(() => {
        window.addEventListener('resize', handleResize)
        window.addEventListener('scroll', handleScroll)
        document.addEventListener('click', handleClickOutside)
    })

    onBeforeUnmount(() => {
        window.removeEventListener('resize', handleResize)
        window.removeEventListener('scroll', handleScroll)
        document.removeEventListener('click', handleClickOutside)
    })

    return {
        inputRef,
        dropdownStyle,
        showDropdown,
        setShowDropdown,
        toggleDropdown,
    }
}
