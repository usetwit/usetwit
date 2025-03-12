<script setup>
import {ref, computed} from 'vue';

const currentInput = ref('');
const previousInput = ref('');
const currentOperation = ref('');

const display = computed(() => {
    if (!previousInput.value && !currentInput.value) return '';
    return `${previousInput.value} ${currentOperation.value} ${currentInput.value}`;
});

const appendNumber = (number) => {
    currentInput.value += number;
};

const appendOperation = (operation) => {
    if (!currentInput.value) return;
    if (previousInput.value) {
        calculate();
    }
    currentOperation.value = operation;
    previousInput.value = currentInput.value;
    currentInput.value = '';
};

const calculate = () => {
    if (!previousInput.value || !currentInput.value) return;

    const prev = parseFloat(previousInput.value);
    const current = parseFloat(currentInput.value);
    let result;

    switch (currentOperation.value) {
        case '+':
            result = prev + current;
            break;
        case '-':
            result = prev - current;
            break;
        case '*':
            result = prev * current;
            break;
        case '/':
            if (current === 0) {
                alert('Cannot divide by zero');
                return;
            }
            result = prev / current;
            break;
        default:
            return;
    }

    currentInput.value = result.toString();
    previousInput.value = '';
    currentOperation.value = '';
};

const clearDisplay = () => {
    currentInput.value = '';
    previousInput.value = '';
    currentOperation.value = '';
};
</script>

<template>
    <div class="calculator">
        <input type="text" v-model="display" class="display" disabled/>
        <div class="buttons">
            <button class="button clear" @click="clearDisplay">C</button>
            <button class="button operator" @click="appendOperation('/')">/</button>
            <button class="button operator" @click="appendOperation('*')">*</button>
            <button class="button operator" @click="appendOperation('-')">-</button>

            <button class="button" @click="appendNumber('7')">7</button>
            <button class="button" @click="appendNumber('8')">8</button>
            <button class="button" @click="appendNumber('9')">9</button>
            <button class="button operator" @click="appendOperation('+')">+</button>

            <button class="button" @click="appendNumber('4')">4</button>
            <button class="button" @click="appendNumber('5')">5</button>
            <button class="button" @click="appendNumber('6')">6</button>
            <button class="button equal" @click="calculate">=</button>

            <button class="button" @click="appendNumber('1')">1</button>
            <button class="button" @click="appendNumber('2')">2</button>
            <button class="button" @click="appendNumber('3')">3</button>
            <button class="button" @click="appendNumber('0')">0</button>
        </div>
    </div>
</template>

<style scoped>
.calculator {
    width: 300px;
    background-color: #fff;
    border-radius: 15px;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    padding: 20px;
}

.display {
    width: 100%;
    height: 50px;
    text-align: right;
    font-size: 1.5rem;
    padding: 10px;
    margin-bottom: 20px;
    border: none;
    background-color: #ffeb3b;
    border-radius: 8px;
    color: #333;
}

.buttons {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
}

.button {
    background-color: #03a9f4;
    border: none;
    font-size: 1.5rem;
    padding: 20px;
    cursor: pointer;
    border-radius: 10px;
    transition: background-color 0.3s, transform 0.2s;
    color: white;
}

.button:hover {
    background-color: #0288d1;
    transform: scale(1.1);
}

.button:active {
    transform: scale(0.95);
}

.button.clear {
    background-color: #ff5722;
}

.button.clear:hover {
    background-color: #e64a19;
}

.button.equal {
    background-color: #8bc34a;
}

.button.equal:hover {
    background-color: #689f38;
}

.button.operator {
    background-color: #ff9800;
}

.button.operator:hover {
    background-color: #f57c00;
}
</style>
