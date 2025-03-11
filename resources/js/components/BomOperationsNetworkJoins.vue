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
        y: 'middle',
    };

    if (s.value.left === op.value.left) {
        pos.x = 'middle';
    } else if (s.value.left > op.value.right) {
        pos.x = 'right';
    } else if (s.value.right < op.value.left) {
        pos.x = 'left';
    } else if (s.value.left < op.value.left && s.value.right > op.value.left) {
        pos.x = 'leftMid';
    } else if (s.value.left > op.value.left && s.value.left < op.value.right) {
        pos.x = 'rightMid';
    } else if (s.value.left === op.value.right) {
        pos.x = 'rightEdge';
    } else if (s.value.right === op.value.left) {
        pos.x = 'leftEdge';
    }

    if (s.value.top > op.value.top) {
        pos.y = 'below';
    } else if (s.value.top < op.value.top) {
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
    },
);
</script>

<template>
    <div v-if="pos.x === 'right' && pos.y === 'middle'"
         class="h-line end-right"
         :style="{
               left: op.right + 'px',
               top: (op.top + width / 2) + 'px',
               width: (s.left - op.right) + 'px',
         }"
    ></div>
    <template v-if="pos.x === 'right' && pos.y === 'below'">
        <div class="h-line"
             :style="{
                left: op.right + 'px',
                top: (op.top + width / 2) + 'px',
                width: ((s.left - op.right) / 2) + 'px',
             }"></div>
        <div class="v-line"
             :style="{
                left: (op.right + (s.left - op.right) / 2) + 'px',
                top: (op.top + width / 2) + 'px',
                height: ((s.top + width / 2) - (op.top + width / 2)) + 'px',
             }"></div>
        <div class="h-line end-right"
             :style="{
                left: (op.right + (s.left - op.right) / 2) + 'px',
                top: (s.top + width / 2) + 'px',
                width: ((s.left - op.right) / 2) + 'px',
             }"></div>
    </template>
    <template v-if="pos.x === 'right' && pos.y === 'above'">
        <div class="h-line"
             :style="{
                left: op.right + 'px',
                top: (op.top + width / 2) + 'px',
                width: ((s.left - op.right) / 2) + 'px',
             }"></div>
        <div class="v-line"
             :style="{
                left: (op.right + (s.left - op.right) / 2) + 'px',
                top: (s.top + width / 2) + 'px',
                height: ((op.top + width / 2) - (s.top + width / 2) + 3) + 'px',
             }"></div>
        <div class="h-line end-right"
             :style="{
                left: (op.right + (s.left - op.right) / 2) + 'px',
                top: (s.top + width / 2) + 'px',
                width: ((s.left - op.right) / 2) + 'px',
             }"></div>
    </template>
    <div v-if="pos.x === 'left' && pos.y === 'middle'"
         class="h-line end-left"
         :style="{
               left: s.right + 'px',
               top: (op.top + width / 2) + 'px',
               width: (op.left - s.right) + 'px',
         }"
    ></div>
    <template v-if="pos.x === 'left' && pos.y === 'below'">
        <div class="h-line end-left"
             :style="{
                left: s.right + 'px',
                top: (s.top + width / 2) + 'px',
                width: ((op.left - s.right) / 2) + 'px',
             }"></div>
        <div class="v-line"
             :style="{
                left: (op.right + (s.left - op.right) / 2) + 'px',
                top: (op.top + width / 2) + 'px',
                height: ((s.top + width / 2) - (op.top + width / 2) + 3) + 'px',
             }"></div>
        <div class="h-line"
             :style="{
                left: (s.right + (op.left - s.right) / 2) + 'px',
                top: (op.top + width / 2) + 'px',
                width: ((op.left - s.right) / 2) + 'px',
             }"></div>
    </template>
    <template v-if="pos.x === 'left' && pos.y === 'above'">
        <div class="h-line end-left"
             :style="{
                left: s.right + 'px',
                top: (s.top + width / 2) + 'px',
                width: ((op.left - s.right) / 2) + 'px',
             }"></div>
        <div class="v-line"
             :style="{
                left: (op.right + (s.left - op.right) / 2) + 'px',
                top: (s.top + width / 2) + 'px',
                height: ((op.top + width / 2) - (s.top + width / 2) + 3) + 'px',
             }"></div>
        <div class="h-line"
             :style="{
                left: (s.right + (op.left - s.right) / 2) + 'px',
                top: (op.top + width / 2) + 'px',
                width: ((op.left - s.right) / 2) + 'px',
             }"></div>
    </template>
    <div v-if="pos.x === 'middle' && pos.y === 'below'"
         class="v-line end-down"
         :style="{
               left: (op.left + width / 2) + 'px',
               top: op.bottom + 'px',
               height: (s.top - op.bottom) + 'px',
         }"
    ></div>
    <template v-if="pos.x === 'leftMid' && pos.y === 'below'">
        <div class="v-line"
             :style="{
                left: (op.left + width / 2) + 'px',
                top: op.bottom + 'px',
                height: ((s.top - op.bottom) / 2) + 'px',
             }"></div>
        <div class="h-line"
             :style="{
                left: (s.left + width / 2) + 'px',
                top: (op.bottom + (s.top - op.bottom) / 2) + 'px',
                width: (op.left - s.left + 3) + 'px',
             }"></div>
        <div class="v-line end-down"
             :style="{
                left: (s.left + width / 2) + 'px',
                top: (op.bottom + (s.top - op.bottom) / 2) + 'px',
                height: ((s.top - op.bottom) / 2) + 'px',
             }"></div>
    </template>
    <template v-if="pos.x === 'rightMid' && pos.y === 'below'">
        <div>leftMid</div>
        <div class="v-line"
             :style="{
                left: (op.left + width / 2) + 'px',
                top: op.bottom + 'px',
                height: ((s.top - op.bottom) / 2) + 'px',
             }"></div>
        <div class="h-line"
             :style="{
                left: (op.left + width / 2) + 'px',
                top: (op.bottom + (s.top - op.bottom) / 2) + 'px',
                width: (s.right - op.right + 3) + 'px',
             }"></div>
        <div class="v-line end-down"
             :style="{
                left: (s.left + width / 2) + 'px',
                top: (op.bottom + (s.top - op.bottom) / 2) + 'px',
                height: ((s.top - op.bottom) / 2) + 'px',
             }"></div>
    </template>
    <div v-if="pos.x === 'middle' && pos.y === 'above'"
         class="v-line end-up"
         :style="{
               left: (op.left + width / 2) + 'px',
               top: s.bottom + 'px',
               height: (op.top - s.bottom) + 'px',
         }"
    ></div>
    <template v-if="pos.x === 'leftMid' && pos.y === 'above'">
        <div class="v-line end-up"
             :style="{
                left: (s.left + width / 2) + 'px',
                top: s.bottom + 'px',
                height: ((op.top - s.bottom) / 2) + 'px',
             }"></div>
        <div class="h-line"
             :style="{
                left: (s.left + width / 2) + 'px',
                top: (op.bottom + (s.top - op.bottom) / 2) + 'px',
                width: (op.left - s.left + 3) + 'px',
             }"></div>
        <div class="v-line"
             :style="{
                left: (op.right - width / 2) + 'px',
                top: (s.bottom + (op.top - s.bottom) / 2) + 'px',
                height: ((op.top - s.bottom) / 2) + 'px',
             }"></div>
    </template>
    <template v-if="pos.x === 'rightMid' && pos.y === 'above'">
        <div class="v-line end-up"
             :style="{
                left: (s.left + width / 2) + 'px',
                top: s.bottom + 'px',
                height: ((op.top - s.bottom) / 2) + 'px',
             }"></div>
        <div class="h-line"
             :style="{
                left: (op.left + width / 2) + 'px',
                top: (s.bottom + (op.top - s.bottom) / 2) + 'px',
                width: (s.left - op.left + 3) + 'px',
             }"></div>
        <div class="v-line"
             :style="{
                left: (op.right - width / 2) + 'px',
                top: (s.bottom + (op.top - s.bottom) / 2) + 'px',
                height: ((op.top - s.bottom) / 2) + 'px',
             }"></div>
    </template>
</template>

<style scoped lang="postcss">
.h-line {
    position: absolute;
    background-color: black;
    height: 3px;
}

.v-line {
    position: absolute;
    background-color: black;
    width: 3px;
}

.end-right::after {
    content: "";
    position: absolute;
    right: 5px;
    top: 50%;
    transform: translateX(100%) translateY(-50%);
    border-style: solid;
    border-width: 5px 0 5px 5px;
    border-color: transparent transparent transparent black;
}

.end-left::after {
    content: "";
    position: absolute;
    left: 5px;
    top: 50%;
    transform: translateX(-100%) translateY(-50%);
    border-style: solid;
    border-width: 5px 5px 5px 0;
    border-color: transparent black transparent transparent;
}

.end-up::after {
    content: "";
    position: absolute;
    top: 5px;
    left: 50%;
    transform: translateX(-50%) translateY(-100%);
    border-style: solid;
    border-width: 0 5px 5px 5px;
    border-color: transparent transparent black transparent;
}

.end-down::after {
    content: "";
    position: absolute;
    bottom: 5px;
    left: 50%;
    transform: translateX(-50%) translateY(100%);
    border-style: solid;
    border-width: 5px 5px 0 5px;
    border-color: black transparent transparent transparent;
}
</style>
