import { ref, watch, onMounted } from 'vue';

export function useDropdownPosition(position = 'left') {
    const inputRef = ref(null);
    const dropdownStyle = ref();
    const showDropdown = ref(false);

    const updateDropdownPosition = () => {
        if (inputRef.value) {
            const inputRect = inputRef.value.getBoundingClientRect();
            dropdownStyle.value = {
                top: `${inputRect.bottom}px`,
                left: `${inputRect.left}px`,
                width: `${inputRect.width}px`,
            };
        }
    };

    const toggleDropdown = (shouldShow) => {
        showDropdown.value = shouldShow;
        if (shouldShow) {
            updateDropdownPosition();
        }
    };

    watch(showDropdown, (newValue) => {
        if (newValue) {
            updateDropdownPosition();
        }
    });

    onMounted(() => {
        // Optionally initialize or update position when mounted
    });

    return {
        inputRef,
        dropdownStyle,
        showDropdown,
        toggleDropdown,
    };
}
