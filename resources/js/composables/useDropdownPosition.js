import { ref, watch, onMounted } from 'vue';

export function useDropdownPosition(positionX = 'left', positionY = 'bottom') {
    const inputRef = ref(null);
    const dropdownStyle = ref();
    const showDropdown = ref(false);

    const updateDropdownPosition = () => {
        if (inputRef.value) {
            // console.log(inputRef.value.inputElement.getBoundingClientRect())
            console.log(inputRef.value.getBoundingClientRect())
            // console.log(inputRef.value)
        }
    };

    const toggleDropdown = () => {
        showDropdown.value = !showDropdown.value;
        updateDropdownPosition()
    };

    return {
        inputRef,
        dropdownStyle,
        showDropdown,
        toggleDropdown,
    };
}
