<script setup>
import { getCurrentInstance, inject, onBeforeUnmount, onMounted } from 'vue'

const props = defineProps({
    column: { type: Object, default: null },
    sortable: { type: Boolean, default: false },
    sticky: { type: Boolean, default: false },
    label: { type: String, default: null },
    type: { type: String, default: null },
})

defineOptions({
    inheritAttrs: false,
    render() {
        return null
    },
})

const registerColumn = inject('registerColumn')
const deregisterColumn = inject('deregisterColumn')

const instance = getCurrentInstance()
const column = {
    field: props.column?.field,
    order: props.column?.order,
    label: props.column?.label || props.label,
    sticky: props.sticky,
    sortable: props.sortable,
    body: instance.vnode.children?.body,
    filter: instance.vnode.children?.filter,
    type: props.type,
    attributes: instance.attrs,
    props: instance.props,
}

onMounted(() => {
    registerColumn(column)
})

onBeforeUnmount(() => {
    deregisterColumn(column)
})
</script>
