<script setup>
import { getCurrentInstance, inject, onBeforeUnmount, onMounted } from 'vue'

const props = defineProps({
    column: { type: Object },
    sortable: { type: Boolean, default: false },
})

const registerColumn = inject('registerColumn')
const deregisterColumn = inject('deregisterColumn')

const instance = getCurrentInstance()

onMounted(() => {
    registerColumn({
        field: props.column?.field,
        label: props.column?.label,
        sortable: props.sortable,
        children: instance.vnode.children,
    })
})

onBeforeUnmount(() => {
    deregisterColumn({ field: props.column?.field })
})
</script>

<template>

</template>
