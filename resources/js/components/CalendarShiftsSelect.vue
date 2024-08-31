<script setup>
import {ref} from 'vue'
import Select from 'primevue/select'

const props = defineProps({
    calendarList: {
        required: true,
        type: Array,
    }
})

const selectedCalendar = ref()
const options = ref(props.calendarList)

const redirect = () => {
    window.location.href = selectedCalendar.value.route
}
</script>

<template>
    <div class="flex justify-end">
        <Select v-model="selectedCalendar"
                :options="options"
                optionLabel="name"
                placeholder="Select a Calendar"
                class="w-56"
                filter
                @change="redirect"
        />
    </div>
</template>
