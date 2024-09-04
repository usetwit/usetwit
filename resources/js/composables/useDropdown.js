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
                        setDropdownPositionX('auto', '0')
                    } else if (inputRect.right >= dropdownRect.width) {
                        setDropdownPositionX(`${right - dropdownRect.width}px`, 'auto');
                    } else if (viewportWidth - inputRect.left >= dropdownRect.width) {
                        setDropdownPositionX(`${left}px`, 'auto');
                    } else {
                        setDropdownPositionX('auto', '0')
                    }
                } else if (positionX === 'left') {
                    if (dropdownRect.width > viewportWidth) {
                        setDropdownPositionX('0', 'auto');
                    } else if (viewportWidth - inputRect.left >= dropdownRect.width) {
                        setDropdownPositionX(`${left}px`, 'auto');
                    } else if (inputRect.right >= dropdownRect.width) {
                        setDropdownPositionX(`${right - dropdownRect.width}px`, 'auto');
                    } else {
                        setDropdownPositionX('0', 'auto');
                    }
                } else {
                    const inputCenter = inputRect.left + inputRect.width / 2
                    const dropdownCenter = dropdownRect.width / 2

                    if(dropdownRect.width > viewportWidth) {
                        setDropdownPositionX(`${inputCenter - dropdownCenter}px`, 'auto')
                    } else if (inputCenter + dropdownCenter > viewportWidth) {
                        setDropdownPositionX('auto', '0')
                    } else if(inputCenter < dropdownCenter){
                        setDropdownPositionX('0', 'auto')
                    } else {
                        setDropdownPositionX(`${inputCenter - dropdownCenter}px`, 'auto')
                    }
                }

                if (positionY === 'bottom') {
                    if (spaceBelow >= dropdownRect.height + gap) {
                        setDropdownPositionY(`${bottom + gap}px`, 'auto')
                    } else if (spaceAbove >= dropdownRect.height + gap) {
                        setDropdownPositionY('auto', `${viewportHeight - top + gap}px`)
                    } else {
                        setDropdownPositionY(`${bottom + gap}px`, 'auto')
                    }
                } else {
                    if (spaceAbove >= dropdownRect.height + gap) {
                        setDropdownPositionY('auto', `${viewportHeight - top + gap}px`)
                    } else {
                        setDropdownPositionY(`${bottom + gap}px`, 'auto')
                    }
                }
            })
        }
    }

    const setDropdownPositionY = (topValue, bottomValue) => {
        dropdownStyle.value.top = topValue
        dropdownStyle.value.bottom = bottomValue
    }

    const setDropdownPositionX = (leftValue, rightValue) => {
        dropdownStyle.value.left = leftValue
        dropdownStyle.value.right = rightValue
    }

    watch(showDropdown, async () => {
        if (showDropdown.value) {
            await nextTick()
            updateDropdownPosition()
        }
    })

    const toggleDropdown = async () => {
        showDropdown.value = !showDropdown.value
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
        window.addEventListener('resize', handleResize);
        window.addEventListener('scroll', handleScroll);
        document.addEventListener('click', handleClickOutside);
        document.querySelector('main').addEventListener('scroll', handleScroll);
        document.querySelectorAll('main div').forEach(div => {
            if (window.getComputedStyle(div).overflowX === 'auto') {
                div.addEventListener('scroll', handleScroll);
            }
        })
    });

    onBeforeUnmount(() => {
        window.removeEventListener('resize', handleResize)
        window.removeEventListener('scroll', handleScroll)
        document.removeEventListener('click', handleClickOutside)
        document.querySelector('main').removeEventListener('scroll', handleScroll);
        document.querySelectorAll('main div').forEach(div => {
            if (window.getComputedStyle(div).overflowX === 'auto') {
                div.removeEventListener('scroll', handleScroll);
            }
        })
    })

    return {
        inputRef,
        dropdownStyle,
        showDropdown,
        setShowDropdown,
        toggleDropdown,
    }
}
