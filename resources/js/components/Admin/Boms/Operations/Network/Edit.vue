<script setup>
import {ref, onMounted, onBeforeUnmount, computed} from 'vue';
import useAxios from '@/composables/useAxios.js';
import Joins from '@/components/Admin/Boms/Operations/Network/Joins.vue';
import {toast} from 'vue3-toastify';
import BomOperationsNetworkColorPicker from '@/components/BomOperationsNetworkColorPicker.vue';
import Modal from '@/components/Modal.vue';

const props = defineProps({
    routes: {type: Object, required: true},
    operations: {type: Array, required: true},
});

const operations = ref([...props.operations]);
const draggingOperation = ref(null);
const dragOffset = ref({x: 0, y: 0});
const isDragging = ref(false);
const initialMousePosition = ref({x: 0, y: 0});
const activeJoin = ref(null);
const linkMode = ref(false);
const unlinkMode = ref(false);

function onMouseDown(e, operation) {
    activeJoin.value = false;

    if (unlinkMode.value) {
        if (!activeOperation.value) {
            toast.error('No active operation.');
            unlinkMode.value = false;
            return;
        }

        if (activeOperation.value.id === operation.id) {
            toast.error('Cannot unlink operation from itself.');
            activeOperation.value.active = false;
            unlinkMode.value = false;
            return;
        }

        if (!activeOperation.value.successors.includes(operation.id)) {
            toast.error('Operation is not a successor.');
            activeOperation.value.active = false;
            unlinkMode.value = false;
            return;
        }

        activeOperation.value.successors = activeOperation.value.successors.filter(id => id !== operation.id);
        toast.success('Operation unlinked.');
        activeOperation.value.active = false;
        unlinkMode.value = false;
        return;
    } else if (linkMode.value) {
        if (!activeOperation.value) {
            toast.error('No active operation.');
            linkMode.value = false;
            return;
        }

        if (activeOperation.value.id === operation.id) {
            toast.error('Cannot link operation to itself.');
            activeOperation.value.active = false;
            linkMode.value = false;
            return;
        }

        if (activeOperation.value.successors.includes(operation.id)) {
            toast.error('Operation already a successor.');
            activeOperation.value.active = false;
            linkMode.value = false;
            return;
        }

        const tempSuccessors = {};
        operations.value.forEach(op => {
            tempSuccessors[op.id] = [...op.successors];
        });

        tempSuccessors[activeOperation.value.id].push(operation.id);

        if (!validateGraph(tempSuccessors)) {
            toast.error('Operation already in cycle.');
            activeOperation.value.active = false;
            linkMode.value = false;
            return;
        }

        activeOperation.value.successors.push(operation.id);
        activeOperation.value.active = false;
        linkMode.value = false;
        return;
    }

    draggingOperation.value = operation;
    dragOffset.value = {
        x: e.clientX - operation.x,
        y: e.clientY - operation.y,
    };
    initialMousePosition.value = {x: e.clientX, y: e.clientY};
    isDragging.value = false;
}

function onMouseMove(e) {
    if (!draggingOperation.value) return;

    const dx = e.clientX - initialMousePosition.value.x;
    const dy = e.clientY - initialMousePosition.value.y;

    if (!isDragging.value && (Math.abs(dx) > 2 || Math.abs(dy) > 2)) {
        isDragging.value = true;
    }

    draggingOperation.value.x = Math.max(0, Math.round((e.clientX - dragOffset.value.x) / 10) * 10);
    draggingOperation.value.y = Math.max(0, Math.round((e.clientY - dragOffset.value.y) / 10) * 10);
}

function onMouseUp() {
    if (draggingOperation.value && !isDragging.value) {
        if (draggingOperation.value.active) {
            draggingOperation.value.active = false;
        } else {
            operations.value.forEach(op => op.active = false);
            draggingOperation.value.active = true;
        }
    }

    draggingOperation.value = null;
    isDragging.value = false;
}

const save = async () => {
    isLoading.value = true;

    const {getResponse, status, data} = useAxios(props.routes.update, {
        operations: operations.value,
    }, 'patch');

    await getResponse();

    if (status.value === 200 && data.value.status === 'updated') {
        toast.success(data.value.message);
    } else if (status.value === 200 && data.value.status === 'network_changed') {
        modalIsVisible.value = true;
    }

    isLoading.value = false;
};

onMounted(() => {
    window.addEventListener('mousemove', onMouseMove);
    window.addEventListener('mouseup', onMouseUp);
});

onBeforeUnmount(() => {
    window.removeEventListener('mousemove', onMouseMove);
    window.removeEventListener('mouseup', onMouseUp);
});

const colorVariants = {
    blue: {default: 'bg-blue-300', active: 'bg-blue-400'},
    red: {default: 'bg-red-300', active: 'bg-red-400'},
    green: {default: 'bg-green-300', active: 'bg-green-400'},
    yellow: {default: 'bg-yellow-300', active: 'bg-yellow-400'},
    purple: {default: 'bg-purple-300', active: 'bg-purple-400'},
    orange: {default: 'bg-orange-300', active: 'bg-orange-400'},
    pink: {default: 'bg-pink-300', active: 'bg-pink-400'},
    teal: {default: 'bg-teal-300', active: 'bg-teal-400'},
};

const unlink = () => {
    linkMode.value = false;

    if (unlinkMode.value) {
        unlinkMode.value = false;
        return;
    }

    if (activeOperation.value) {
        unlinkMode.value = true;
        return;
    }

    if (activeJoin.value) {
        const operation = operations.value.find(op => op.id === activeJoin.value.operationId);

        if (operation) {
            operation.successors = operation.successors.filter(id => id !== activeJoin.value.successorId);
            activeJoin.value = null;
        }
    }
};

const activeOperation = computed(() => operations.value.find(op => op.active) || null);

function hasCycleDFS(node, successors, visited, stack) {
    if (stack.has(node)) return true;
    if (visited.has(node)) return false;

    visited.add(node);
    stack.add(node);

    for (const nextOp of (successors[node] || [])) {
        if (hasCycleDFS(nextOp, successors, visited, stack)) {
            return true;
        }
    }

    stack.delete(node);
    return false;
}

function validateGraph(successors) {
    const visited = new Set();
    const stack = new Set();

    for (const op in successors) {
        if (!visited.has(op)) {
            if (hasCycleDFS(op, successors, visited, stack)) {
                return false;
            }
        }
    }

    return true;
}

const backgroundClick = (e) => {
    if (e.target === e.currentTarget) {
        linkMode.value = false;
        unlinkMode.value = false;
        activeJoin.value = null;
        activeOperation.value = null;
        operations.value.forEach(op => op.active = false);
    }
};

const handleColorSelected = (color) => {
    if (activeOperation.value) {
        activeOperation.value.color = color;
    }
};

const modalIsVisible = ref(false);
const isLoading = ref(false);

const upversion = async () => {
    isLoading.value = true;

    const {getResponse, status, data} = useAxios(props.routes.upversion, {
        operations: operations.value,
    }, 'post');

    await getResponse();

    if (status.value === 422) {
        toast.error(data.value.message);
    } else if (status.value === 201) {
        toast.success(data.value.message);

        window.location.replace(data.value.redirect);
    }

    isLoading.value = false;
};

const handleJoinClicked = () => {
    activeOperation.value = false;
    linkMode.value = false;
    unlinkMode.value = false;
    operations.value.forEach(op => op.active = false);
}
</script>

<template>
    <Modal v-if="modalIsVisible"
           v-model="modalIsVisible"
           title="Are you sure?"
           icon="pi pi-file-arrow-up"
           variant="danger"
           @accepted="upversion"
    >
        This will create a newer version of the BOM.
    </Modal>
    <div id="content">
        <div class="p-2 bg-gray-100 flex justify-between items-center">
            <div>
                <button type="button"
                        class="mr-1 inline-flex items-center px-2 py-1 rounded-md disabled:bg-gray-300 disabled:text-gray-500"
                        :class="{
                        'bg-green-200 text-green-700 hover:bg-green-300': !linkMode,
                        'bg-green-500 text-white hover:bg-green-600': linkMode
                    }"
                        :disabled="!activeOperation"
                        @click="linkMode = !linkMode; unlinkMode = false"
                >
                    <i class="pi pi-link mr-1"></i>Link
                </button>
                <button type="button"
                        class="mr-1 inline-flex items-center px-2 py-1 rounded-md disabled:bg-gray-300 disabled:text-gray-500"
                        :class="{
                        'bg-red-200 text-red-700 hover:bg-red-300': !unlinkMode,
                        'bg-red-500 text-white hover:bg-red-600': unlinkMode
                    }"
                        :disabled="!activeJoin && !activeOperation"
                        @click="unlink"
                >
                    <i class="pi pi-trash mr-1"></i>Unlink
                </button>
                <BomOperationsNetworkColorPicker :disabled="!activeOperation" @selected="handleColorSelected"/>
            </div>
            <div>
                <button type="button"
                        :disabled="isLoading"
                        @click="save"
                        class="text-green-700 inline-flex items-center bg-green-200 px-2 py-1 rounded-md hover:bg-green-300"
                >
                    <i class="pi mr-1" :class="isLoading ? 'pi-spinner pi-spin' : 'pi-save'"></i>Save
                </button>
            </div>
        </div>
        <div class="min-h-96 overflow-scroll relative" id="network" @click="backgroundClick">
            <template v-for="operation in operations" :key="operation.id">
                <Joins v-for="successor in operation.successors"
                                           :key="`${operation.id}-${successor}`"
                                           :operation="operation"
                                           :successor="operations.find(op => op.id === successor)"
                                           v-model="activeJoin"
                                           @clicked="handleJoinClicked"
                />
            </template>
            <div v-for="operation in operations"
                 :key="operation.id"
                 class="operation-box absolute overflow-hidden text-center border-[2px] text-gray-800"
                 :class="[
                    colorVariants[operation.color][operation.active ? 'active' : 'default'],
                    {
                        'border-red-500': operation.active,
                        'border-gray-800': !operation.active,
                        'cursor-move': isDragging && !linkMode && !unlinkMode,
                        'cursor-pointer': !isDragging && !linkMode && !unlinkMode,
                        'cursor-copy': linkMode && !unlinkMode && !isDragging,
                        'cursor-no-drop': !linkMode && unlinkMode && !isDragging,
                    }
                ]"
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
    </div>
</template>

<style scoped>
#network {
    background: radial-gradient(circle, #e8e8e8 1px, transparent 1px);
    background-size: 10px 10px;
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
