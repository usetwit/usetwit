<script setup>
import FilterSelectOperator from './FilterSelectOperator.vue'
import FilterButtonClearApply from './FilterButtonClearApply.vue'
import FilterSelectMode from './FilterSelectMode.vue'
import InputText from '../Form/InputText.vue'
import FilterButtonAdd from './FilterButtonAdd.vue'
import FilterButtonRemove from './FilterButtonRemove.vue'

const props = defineProps({
    column: { type: Object, required: true },
})

const emit = defineEmits(['filter', 'apply'])

const filter = defineModel()
</script>

<template>
    <FilterSelectOperator v-if="column.type !== 'boolean'" v-model="filter.operator"/>
    <template v-for="(constraint, i) in filter.constraints">
        <FilterSelectMode v-if="column.type !== 'boolean'" :type="column.type" v-model="constraint.mode"/>
        <slot :constraint="constraint" :column="column">
            <InputText v-model="constraint.value"
                       class="text-sm mt-1 rounded-md"
                       :placeholder="'Search by ' + column.label"
            />
        </slot>
        <FilterButtonRemove v-if="filter.constraints.length > 1 && column.type !== 'boolean'"
                            v-model="filter.constraints"
                            :index="i"
        />
    </template>
    <FilterButtonAdd v-if="column.type !== 'boolean'" v-model="filter.constraints" :type="column.type"/>
    <FilterButtonClearApply v-model="filter.constraints" :type="column.type"
                            @clear="$emit('filter')" @apply="$emit('apply')"/>
</template>
