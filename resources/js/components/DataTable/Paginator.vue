<script setup>
import { computed, nextTick, ref, watch } from 'vue'
import Select from '../Form/Select.vue'

const props = defineProps({
    settings: { type: Object, required: true },
})

const emit = defineEmits(['change'])

const model = defineModel()
const options = props.settings.options.map(num => ({ value: num }))

const from = computed(() => model.value.total === 0 ? 0 : model.value.per_page * (model.value.page - 1) + 1)
const totalPages = computed(() => model.value.total > 0 ? Math.ceil(model.value.total / model.value.per_page) : 1)

const perSide = 2
const startPage = computed(() => Math.max(1, model.value.page - perSide))
const endPage = computed(() => Math.min(totalPages.value, model.value.page + perSide))
const pages = computed(() => {
    let items = []
    for (let i = startPage.value; i <= endPage.value; i++) {
        items.push({ number: i, current: i === model.value.page })
    }

    return items
})

const changePerPage = () => {
    if (startPage.value > endPage.value) {
        model.value.page = endPage.value
    }
    emit('change')
}

const selectPage = number => {
    model.value.page = number
    emit('change')
}
</script>

<template>
    <nav aria-label="Page navigation" class="flex justify-center items-center text-gray-600 text-sm">
        <ul class="flex flex-nowrap">
            <li>
                <span v-if="model.page === 1" class="base disabled"><i class="pi pi-angle-double-left"></i></span>
                <button v-else
                        @click="selectPage(1)"
                        class="base enabled"
                >
                    <i class="pi pi-angle-double-left"></i>
                </button>
            </li>
            <li>
                <span v-if="model.page === 1" class="base disabled"><i class="pi pi-angle-left"></i></span>
                <button v-else
                        @click="selectPage(model.page - 1)"
                        class="base enabled"
                >
                    <i class="pi pi-angle-left"></i>
                </button>
            </li>
            <li v-for="page in pages" :key="page.number">
                <button v-if="!page.current"
                        type="button"
                        @click="selectPage(page.number)"
                        class="base enabled"
                >
                    {{ page.number }}
                </button>
                <span v-else class="base current">{{ page.number }}</span>
            </li>
            <li>
                <span v-if="model.page === totalPages" class="base disabled"><i class="pi pi-angle-right"></i></span>
                <button v-else
                        @click="selectPage(model.page + 1)"
                        class="base enabled"
                >
                    <i class="pi pi-angle-right"></i>
                </button>
            </li>
            <li>
                <span v-if="model.page === totalPages" class="base disabled"><i
                    class="pi pi-angle-double-right"></i></span>
                <button v-else
                        @click="selectPage(totalPages)"
                        class="base enabled"
                >
                    <i class="pi pi-angle-double-right"></i>
                </button>
            </li>
        </ul>
        <span class="ml-2">
            Showing {{ from }} to {{ Math.min(model.per_page * model.page, model.total) }} of {{ model.total }}
        </span>
        <Select v-model="model.per_page"
                :options="options"
                option-label="value"
                option-value="value"
                class="ml-2 text-sm"
                @selected="changePerPage"
        >
            <template #option="{ option }">
                <span class="text-sm">{{ option.value }}</span>
            </template>
        </Select>
    </nav>
</template>

<style scoped lang="postcss">
.base {
    @apply mx-[0.5px] text-sm inline-flex items-center justify-center rounded-full w-8 h-8 bg-white;

    &.disabled {
        @apply text-gray-300;
    }

    &.enabled {
        @apply text-gray-600 hover:bg-gray-100;
    }

    &.current {
        @apply bg-slate-700 text-white;
    }
}
</style>
