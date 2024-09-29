<script setup>
import useStorage from '../composables/useStorage'

const props = defineProps({
    version: { type: String, required: true },
})

const { set, get, clear } = useStorage('usetwit-version', props.version, true)

const storedVersion = get()

if (storedVersion === undefined) {
    set()
} else if (storedVersion !== props.version) {
    clear()
    set()
}

</script>

<template>

</template>
