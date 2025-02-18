<script setup>
import useDropdown from "@/composables/useDropdown.js";

const props = defineProps({
    editor: {type: Object, required: true}
})

const items = [
    {label: "Thin", class: "font-thin"},
    {label: "Extra Light", class: "font-extralight"},
    {label: "Light", class: "font-light"},
    {label: "Normal", class: "font-normal"},
    {label: "Medium", class: "font-medium"},
    {label: "SemiBold", class: "font-semibold"},
    {label: "Bold", class: "font-bold"},
    {label: "Extra Bold", class: "font-extrabold"},
    {label: "Black", class: "font-black"},
]

const apply = (item) => {
    props.editor.chain().focus().toggleFontWeight(item.class).run()
}

const {
    inputRef,
    dropdownStyle,
    showDropdown,
    toggleDropdown,
} = useDropdown()
</script>

<template>
    <button ref="inputRef" class="wysiwyg-button" @click="toggleDropdown" type="button">Font Weight</button>

    <Teleport to="body" v-if="showDropdown">
        <div ref="dropdownRef"
             class="dropdown z-350 min-w-48"
             :style="dropdownStyle"
        >
            <ul>
                <li v-for="item in items" :key="item.class" class="w-full">
                    <button
                        :class="[item.class, 'px-4 py-2 w-full']"
                        @click="apply(item)"
                    >
                        {{ item.label }}
                    </button>
                </li>
            </ul>
        </div>
    </Teleport>
</template>

<style scoped lang="postcss">

</style>
