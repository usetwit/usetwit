<script setup>
import { toast } from 'vue3-toastify'

defineOptions({
    inheritAttrs: false,
    render() {
        return null
    },
})

const props = defineProps({
    message: String,
    type: String,
})

toast(props.message, {
    type: props.type,
})
</script>
