<script setup>
import {computed, ref, watch} from 'vue'
import DatePicker from 'primevue/datepicker'
import InputText from 'primevue/inputtext'
import InputGroup from 'primevue/inputgroup'
import InputGroupAddon from 'primevue/inputgroupaddon'
import Button from 'primevue/button'
import AutoComplete from 'primevue/autocomplete'
import {useToast} from 'primevue/usetoast'
import {useAxios} from '../composables/useAxios'

const props = defineProps({
    routeStockBomSearch: {
        type: String,
        required: true,
    },
    errorFields: {
        type: Array,
        required: true,
    },
    dateFormat: {
        type: String,
        required: true,
    },
});

const items = defineModel('items')

const addItem = () => {
    items.value.push({
        item_name: '',
        price: '0.00',
        discount: 0,
        discounted_price: 0,
        batches: [1],
        due_date: null,
        due_date_ymd: null,
        id: items.value.length
    })
}

const removeItem = (index) => {
    items.value.splice(index, 1)

    if (items.value.length === 0) {
        addItem()
    }
}

const formatter = new Intl.NumberFormat('en-GB', {
    style: 'currency',
    currency: 'GBP',
});

let symbol = formatter.formatToParts(0)
    .filter(part => part.type === 'currency')[0].value

const currencyParts = (price) => {
    return formatter.formatToParts(price)
        .filter(part => part.type === 'integer' || part.type === 'decimal' || part.type === 'fraction')
}

const formatPrice = (price) => {
    if (isNaN(price)) {
        return '0.00'
    }

    return currencyParts(price).map(part => part.value).join('')
}

const discountedPrice = (item) => {
    let price = item.price * (1 - item.discount / 100)
    let roundedPrice = Math.round((price + Number.EPSILON) * 100) / 100

    item.discounted_price = formatPrice(roundedPrice)

    return formatPrice(roundedPrice)
}

const addBatch = (item) => {
    item.batches.push(1)
}

const removeBatch = (item, index) => {
    item.batches.splice(index, 1)

    if (item.batches.length === 0) {
        addBatch(item)
    }
}

const totals = computed(() => {
    return {
        prices: formatPrice(items.value.reduce((s, a) => s + parseFloat(a.price), 0)),
        discounted_prices: formatPrice(items.value.reduce((s, a) => s + parseFloat(a.discounted_price), 0)),
    }
})

const batchTotal = (item) => {
    let total = item.batches.reduce((s, a) => s + parseFloat(a), 0)

    if (isNaN(total)) {
        return 0
    }

    return Math.round((total + Number.EPSILON) * 1000) / 1000
}

const toast = useToast();
const searchResults = ref([])

const search = async (item) => {
    const {data, errors, getResponse} = useAxios(props.routeStockBomSearch, {name: item.item_name})
    await getResponse()

    if (errors.value.raw) {
        toast.add({
            severity: 'error',
            summary: errors.value.message,
            group: 'br',
            detail: errors.value.list,
            life: 3000
        })
    } else {
        searchResults.value = data.value
    }
}

items.value.forEach((item) => {
    watch(
        () => item.due_date,
        (newDate) => {
            if (newDate) {
                const year = newDate.getFullYear();
                const month = String(newDate.getMonth() + 1).padStart(2, '0')
                const day = String(newDate.getDate()).padStart(2, '0')
                item.due_date_ymd = `${year}-${month}-${day}`
            } else {
                item.due_date_ymd = ''
            }
        }
    )
})
</script>

<template>
    <div class="overflow-x-auto pb-4">
        <table class="table-input">
            <thead>
            <tr>
                <th class="text-sm min-w-[250px]">
                    Item Code
                </th>
                <th class="text-sm min-w-[350px]">
                    Quantity
                </th>
                <th class="text-sm min-w-[170px]">
                    Price ({{ symbol }})
                </th>
                <th class="text-sm min-w-[140px]">
                    Discount (%)
                </th>
                <th class="text-sm min-w-[170px]">
                    Discounted Price ({{ symbol }})
                </th>
                <th class="text-sm min-w-[200px]">
                    Due Date
                </th>
                <th class="text-sm">
                    Remove
                </th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(item, index) in items" :key="item.id">
                <td>

                    <AutoComplete v-model="item.item_name"
                                  :suggestions="searchResults"
                                  @complete="search(item)"
                                  @option-select="(event) => (item.item_name = event.value.name)"
                                  optionLabel="name"
                                  optionGroupLabel="label"
                                  optionGroupChildren="items"
                                  placeholder="BOM/Stock Code"
                                  fluid
                                  :invalid="props.errorFields.includes('items.' + index + '.item_name')"
                    >
                        <template #optiongroup="slotProps">
                            <div class="text-slate-600 text-sm">
                                {{ slotProps.option.label }}
                            </div>
                        </template>
                        <template #option="slotProps">
                            <div class="text-slate-600 text-sm">
                                {{ slotProps.option.name }}
                                <span class="text-slate-400 block text-xs">
                                {{ slotProps.option.description }}
                            </span>
                            </div>
                        </template>
                    </AutoComplete>

                </td>
                <td>
                    <div class="grid grid-cols-2">
                        <div>
                            <InputGroup v-for="(batch, batchIndex) in item.batches"
                                        :key="batchIndex"
                                        :class="{'mt-1': batchIndex > 0}"
                            >
                                <InputText v-model="item.batches[batchIndex]"
                                           type="number"
                                           placeholder="Quantity"
                                           min=".001"
                                           max="1000000000"
                                           step=".001"
                                           required
                                           aria-required="true"
                                           :invalid="props.errorFields.includes('items.' + index + '.batches.' + batchIndex)"
                                />
                                <InputGroupAddon>
                                    <Button icon="pi pi-times"
                                            aria-label="Delete"
                                            severity="danger"
                                            size="small"
                                            @click="removeBatch(item, batchIndex)"
                                            class="!p-0"
                                    />
                                </InputGroupAddon>
                            </InputGroup>
                        </div>
                        <div class="text-center">
                            <Button label="Add Batch"
                                    icon="pi pi-plus"
                                    severity="contrast"
                                    @click="addBatch(item)"
                                    size="small"
                                    :badge="item.batches.length.toString()"
                            />
                            <span v-if="item.batches.length > 1" class="block text-slate-900 mt-2">
                            Total Items: {{ batchTotal(item) }}
                        </span>
                        </div>
                    </div>
                </td>
                <td>
                    <InputGroup>
                        <InputGroupAddon>
                            {{ symbol }}
                        </InputGroupAddon>
                        <InputText type="number"
                                   min="0"
                                   max="1000000000"
                                   step="0.01"
                                   placeholder="Price"
                                   @change="item.price = formatPrice(item.price)"
                                   v-model="item.price"
                                   pattern="[0-9]+\.[0-9][0-9][0-9]?"
                                   required
                                   aria-required="true"
                                   :invalid="props.errorFields.includes('items.' + index + '.price')"
                        />
                    </InputGroup>
                </td>
                <td>
                    <InputGroup>
                        <InputText type="number"
                                   placeholder="Discount"
                                   v-model="item.discount"
                                   min="0"
                                   max="100"
                                   step=".01"
                                   required
                                   aria-required="true"
                                   :invalid="props.errorFields.includes('items.' + index + '.discount')"
                        />
                        <InputGroupAddon>
                            %
                        </InputGroupAddon>
                    </InputGroup>
                </td>
                <td class="p-1 align-top">
                    <InputGroup>
                        <InputGroupAddon>
                            {{ symbol }}
                        </InputGroupAddon>
                        <InputText :value="discountedPrice(item)" readonly/>
                    </InputGroup>
                </td>
                <td class="p-1 align-top">
                    <DatePicker v-model="item.due_date"
                                showIcon
                                showWeek
                                fluid
                                showButtonBar
                                :showOnFocus="false"
                                :dateFormat="props.dateFormat"
                                :invalid="props.errorFields.includes('items.' + index + '.due_date')"
                    />
                </td>
                <td class="text-center p-1 align-top">
                    <Button @click="removeItem(index)"
                            severity="danger"
                            icon="pi pi-times"
                    />
                </td>
            </tr>
            <tr>
                <td class="p-1 text-right font-bold" colspan="2">
                    Total
                </td>
                <td class="p-1 text-center font-bold">
                    {{ symbol + totals.prices }}
                </td>
                <td>

                </td>
                <td class="p-1 text-center font-bold">
                    {{ symbol + totals.discounted_prices }}
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <Button @click="addItem"
            severity="contrast"
            icon="pi pi-plus"
            label="Add Item"
    />
</template>
