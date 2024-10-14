<script setup>
import Button from "@/components/Form/Button.vue";

const props = defineProps({
    title: { type: String, default: null },
    variant: { type: String, default: 'primary' },
    icon: { type: String, default: null },
    label: { type: String, default: null },
})

const emit = defineEmits(['closed', 'accepted'])
const isVisible = defineModel()

const close = () => {
    isVisible.value = false
    emit('closed')
}

const accept = () => {
    isVisible.value = false
    emit('accepted')
}
</script>

<template>
    <Teleport to="body">
        <div @click.self="isVisible = false"
             class="bg-opacity-50 bg-gray-900 z-[950] w-full h-full left-0 top-0 fixed flex items-center justify-center"
        >
            <div class="rounded-lg bg-white shadow border-gray-200 border flex flex-col overflow-y-auto p-2 md:p-4 max-w-full max-h-full md:max-w-lg md:max-h-[90vh]">
                <h2 v-if="title || $slots.title" class="text-lg font-semibold mb-2 md:mb-4">
                    {{ title }}
                    <slot name="title"/>
                </h2>
                <div v-if="$slots.default" class="text-gray-700 mb-3 md:mb-6 overflow-y-auto">
                    <slot/>
                </div>
                <div class="flex justify-end">
                    <Button :variant="variant" @click="accept" class="mr-2" :icon="icon" :label="label"></Button>
                    <Button variant="secondary" @click="close" icon="pi pi-times-circle">Close</Button>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<style scoped lang="postcss">

</style>
