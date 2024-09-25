<script setup>
import { getCurrentInstance, inject, onBeforeUnmount, onMounted } from 'vue'

const props = defineProps({
    column: { type: Object },
    sortable: { type: Boolean, default: false },
    sticky: { type: Boolean, default: false },
})

const registerColumn = inject('registerColumn')
const deregisterColumn = inject('deregisterColumn')

const instance = getCurrentInstance()
const column = {
    field: props.column?.field,
    label: props.column?.label,
    sticky: props.sticky,
    sortable: props.sortable,
    body: instance.vnode.children?.body,
    filter: instance.vnode.children?.filter,
}

onMounted(() => {
    registerColumn(column)
})

onBeforeUnmount(() => {
    deregisterColumn(column)
})
</script>

<template>

</template>
