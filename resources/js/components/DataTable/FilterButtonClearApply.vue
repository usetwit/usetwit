<script setup>
import { useTable } from '../../composables/useTable.js'

const props = defineProps({
    type: { type: String },
})

const constraints = defineModel()
const emit = defineEmits(['clear', 'apply'])

const { getModeFromMap } = useTable()

const clear = () => {
    const mode = getModeFromMap(props.type)
    constraints.value = [{ value: null, mode }]
    emit('clear')
}

const apply = () => {
    emit('apply')
}
</script>

<template>
    <div class="flex justify-between mt-1">
        <button @click.stop="clear"
                class="text-sm rounded-md bg-white hover:bg-gray-100 text-gray-800 px-2 py-1 border border-gray-200"
        >
            Clear
        </button>
        <button @click="apply"
                class="text-sm rounded-md bg-green-500 text-gray-100 px-2 py-1 border border-green-500 hover:bg-green-400 hover:border-green-400"
        >
            Apply
        </button>
    </div>
</template>
