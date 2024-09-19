<script setup>
import { inject, onMounted } from 'vue';

const props = defineProps({
    field: String,
    header: String
});

const registerColumn = inject('registerColumn');

onMounted(() => {
    registerColumn({
        field: props.field,
        header: props.header
    });
});
</script>
