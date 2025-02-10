<script setup>
import AutoComplete from '@/components/Form/AutoComplete.vue'
import { debounce } from 'lodash'
import useAxios from '@/composables/useAxios.js'
import { nextTick, ref } from 'vue'

const props = defineProps({
    'index': { type: Number, required: true },
    'errorFields': { type: Array, required: true },
    'routeStockBomSearch': { type: String, required: true },
})

const text = defineModel()
const searchResults = ref([])

const search = async () => {
    await nextTick()
    const { status, data, getResponse } = useAxios(props.routeStockBomSearch, { long_id: text.value })
    await getResponse()

    if (status.value === 200) {
        searchResults.value = data.value
    }
}

const debouncedSearch = debounce(search, 300, { leading: true, trailing: true })
</script>

<template>

    <AutoComplete dropdown
                  :invalid="errorFields.includes('items.' + index + '.long_id')"
                  option-label="long_id"
                  option-group-label="label"
                  option-group-items="items"
                  @completed="debouncedSearch"
                  v-model="text"
                  :items="searchResults"
    >
        <template #item="item">
            <div class="flex flex-col">
                <span>{{ item.long_id }}</span>
                <span class="text-xs text-gray-500">{{ item.description }}</span>
            </div>
        </template>
    </AutoComplete>

</template>
