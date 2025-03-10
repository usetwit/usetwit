<script setup>
import {ref, computed, onMounted, onBeforeUnmount} from 'vue';

const props = defineProps({
    routes: {type: Object, required: true},
    operations: {type: Array, required: true},
});

// Use a ref for operations (Vue auto-unwraps refs in templates)
const ops = ref([...props.operations]);

// Computed property that returns true if any operation is active.
const isAnyActive = computed(() => ops.value.some(op => op.active));

// For dragging: store the currently dragged operation and the offset
const draggingOperation = ref(null);
const dragOffset = ref({x: 0, y: 0});
const isDragging = ref(false);
const initialMousePosition = ref({x: 0, y: 0});

// When a user mouses down on an operation, prepare for a possible drag.
function onMouseDown(e, operation) {
    draggingOperation.value = operation;
    dragOffset.value = {
        x: e.clientX - operation.x,
        y: e.clientY - operation.y,
    };
    initialMousePosition.value = {x: e.clientX, y: e.clientY};
    isDragging.value = false;
}

// Update the position if dragging, and set the flag if movement is significant.
function onMouseMove(e) {
    if (!draggingOperation.value) return;

    const dx = e.clientX - initialMousePosition.value.x;
    const dy = e.clientY - initialMousePosition.value.y;
    // If the mouse moves more than 4px, we consider it a drag.
    if (!isDragging.value && (Math.abs(dx) > 0 || Math.abs(dy) > 0)) {
        isDragging.value = true;
    }

    // Update the operation's position visually.
    draggingOperation.value.x = e.clientX - dragOffset.value.x;
    draggingOperation.value.y = e.clientY - dragOffset.value.y;
}

function onMouseUp() {
    if (draggingOperation.value && !isDragging.value) {
        if (draggingOperation.value.active) {
            // If the station is already active, toggle it off.
            draggingOperation.value.active = false;
        } else {
            // Otherwise, set all stations to inactive...
            ops.value.forEach(op => op.active = false);
            // ...and then mark this station as active.
            draggingOperation.value.active = true;
        }
    }

    draggingOperation.value = null;
    isDragging.value = false;
}

onMounted(() => {
    window.addEventListener('mousemove', onMouseMove);
    window.addEventListener('mouseup', onMouseUp);
});
onBeforeUnmount(() => {
    window.removeEventListener('mousemove', onMouseMove);
    window.removeEventListener('mouseup', onMouseUp);
});

const colorVariants = {
    blue: { default: 'bg-blue-300', active: 'bg-blue-400' },
    red: { default: 'bg-red-300', active: 'bg-red-400' },
    green: { default: 'bg-green-300', active: 'bg-green-400' },
    yellow: { default: 'bg-yellow-300', active: 'bg-yellow-400' },
    purple: { default: 'bg-purple-300', active: 'bg-purple-400' },
    orange: { default: 'bg-orange-300', active: 'bg-orange-400' },
    pink: { default: 'bg-pink-300', active: 'bg-pink-400' },
    teal: { default: 'bg-teal-300', active: 'bg-teal-400' },
};
</script>

<template>
    <div id="content">
        <div class="p-2 bg-gray-100">
            <button
                class="mr-1 text-green-700 inline-flex items-center bg-green-200 px-2 py-1 rounded-md hover:bg-green-300 disabled:bg-gray-300 disabled:text-gray-500"
                :disabled="!isAnyActive"
            >
                <i class="pi pi-link mr-2"></i> Link
            </button>
            <button>
                <i class="pi pi-delete-left"></i> Unlink
            </button>
            <button>
                <i class="pi pi-trash"></i> Delete Operation
            </button>
        </div>
        <div class="min-h-96 overflow-scroll relative" id="network">
            <div
                v-for="operation in ops"
                :key="operation.id"
                class="operation-box absolute overflow-hidden text-center border-[2px] text-gray-800"
                :class="[
                    colorVariants[operation.color][operation.active ? 'active' : 'default'],
                    {
                        'border-red-500': operation.active,
                        'border-gray-800': !operation.active,
                        'cursor-move': isDragging,
                        'cursor-pointer': !isDragging,
                    }]"
                :style="{
                    left: operation.x + 'px',
                    top: operation.y + 'px',
                }"
                @mousedown="onMouseDown($event, operation)"
            >
                {{ operation.name }}<br>
                <br>
                {{ operation.calendar_name }}
            </div>
        </div>
        <!-- For demonstration, show if any station is active -->
        <div v-if="isAnyActive">
            <strong>An operation is active!</strong>
        </div>
    </div>
</template>

<style scoped>
#network {
    background: radial-gradient(circle, #ebebeb 1px, transparent 1px);
    background-size: 5px 5px;
    position: relative;
}

.operation-box {
    width: 60px;
    height: 60px;
    font-size: 0.6rem;
    display: flex;
    align-items: center;
    justify-content: center;
    user-select: none;
    transition: left 0.1s ease, top 0.1s ease;
}
</style>
