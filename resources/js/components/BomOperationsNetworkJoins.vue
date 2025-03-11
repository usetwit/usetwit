<script setup>
import {computed, ref, watch} from 'vue';

const props = defineProps({
    operation: {type: Object, required: true},
    successor: {type: Object, required: true},
});

const width = 60;

const op = ref({
    left: props.operation.x,
    right: props.operation.x + width,
    top: props.operation.y,
    bottom: props.operation.y + width,
});

const s = ref({
    left: props.successor.x,
    right: props.successor.x + width,
    top: props.successor.y,
    bottom: props.successor.y + width,
});

const pos = computed(() => {
    let pos = {
        x: 'touching',
        y: 'touching',
    };

    if (s.value.left === op.value.left) {
        pos.x = 'middle';
    } else if (s.value.left > op.value.right) {
        pos.x = 'right';
    } else if (s.value.right < op.value.left) {
        pos.x = 'left';
    }

    if (s.value.top === op.value.top) {
        pos.y = 'middle';
    } else if (s.value.top > op.value.bottom) {
        pos.y = 'below';
    } else if (s.value.bottom < op.value.top) {
        pos.y = 'above';
    }

    return pos;
});

watch(() => ({
        x: props.operation.x,
        y: props.operation.y,
        successorX: props.successor.x,
        successorY: props.successor.y,
    }),
    (newVal) => {
        op.value.left = newVal.x;
        op.value.right = newVal.x + width;
        op.value.top = newVal.y;
        op.value.bottom = newVal.y + width;
        s.value.left = newVal.successorX;
        s.value.right = newVal.successorX + width;
        s.value.top = newVal.successorY;
        s.value.bottom = newVal.successorY + width;
        // console.log(`Operation ${props.operation.id} or its successor moved. Redraw lines.`);
    },
);
</script>

<template>
    <span>{{ pos }} - </span>
</template>

<style scoped lang="postcss">
.h-line {
    position: absolute;
    background-color: black;
    height: 3px;
}
</style>
