<script setup>
import { computed } from 'vue'
import Datepicker from '@/components/Form/Datepicker.vue'
import InputText from '@/components/Form/InputText.vue'
import InputGroup from '@/components/Form/InputGroup.vue'
import InputGroupAddon from '@/components/Form/InputGroupAddon.vue'
import Button from '@/components/Form/Button.vue'
import SalesOrdersAutocomplete from '@/components/SalesOrdersAutocomplete.vue'

const props = defineProps({
    routeStockBomSearch: { type: String, required: true },
    errorFields: { type: Array, required: true },
    dateSettings: { type: Object, required: true },
});

const items = defineModel()

const addItem = () => {
    items.value.push({
        long_id: '',
        price: '0.00',
        discount: 0,
        discounted_price: 0,
        batches: [1],
        due_date: null,
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

let symbol = formatter.formatToParts(0).filter(part => part.type === 'currency')[0].value

const currencyParts = (price) => {
    return formatter.formatToParts(price).filter(part => part.type === 'integer' || part.type === 'decimal' || part.type === 'fraction')
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
</script>

<template>
    <div class="overflow-x-auto pb-4">
        <table class="table-input">
            <thead>
            <tr>
                <th>
                    Item Code
                </th>
                <th>
                    Quantity
                </th>
                <th>
                    Price ({{ symbol }})
                </th>
                <th>
                    Discount (%)
                </th>
                <th>
                    Discounted Price ({{ symbol }})
                </th>
                <th>
                    Due Date
                </th>
                <th>
                    Remove
                </th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(item, index) in items" :key="item.id" class="item-row">
                <td>
                    <SalesOrdersAutocomplete :error-fields="errorFields"
                                             :index="index"
                                             :route-stock-bom-search="routeStockBomSearch"
                                             v-model="item.long_id"
                    />
                </td>
                <td>
                    <div class="flex">
                        <div>
                            <div v-for="({}, batchIndex) in item.batches"
                                 :key="batchIndex"
                                 class="flex items-start"
                                 :class="{'mt-1': batchIndex > 0}"
                            >
                                <InputText v-model="item.batches[batchIndex]"
                                           type="number"
                                           placeholder="Quantity"
                                           min=".001"
                                           max="1000000000"
                                           step=".001"
                                           required
                                           class="mr-1 rounded-md w-40"
                                           aria-required="true"
                                           :invalid="errorFields.includes('items.' + index + '.batches.' + batchIndex)"
                                />
                                <Button icon="pi pi-times"
                                        aria-label="Delete"
                                        variant="danger"
                                        size="sm"
                                        type="button"
                                        @click="removeBatch(item, batchIndex)"
                                />
                            </div>
                        </div>
                        <div class="text-center pl-1">
                            <Button label="Add Batch"
                                    icon="pi pi-plus"
                                    type="button"
                                    @click="addBatch(item)"
                                    size="sm"
                                    :badge="item.batches.length"
                            />
                            <span v-if="item.batches.length > 1" class="block text-slate-800 mt-2">
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
                                   class="w-40"
                                   pattern="[0-9]+\.[0-9][0-9][0-9]?"
                                   required
                                   :invalid="errorFields.includes('items.' + index + '.price')"
                        />
                    </InputGroup>
                </td>
                <td>
                    <InputGroup>
                        <InputText type="number"
                                   placeholder="Discount"
                                   v-model="item.discount"
                                   class="w-20"
                                   min="0"
                                   max="100"
                                   step=".01"
                                   required
                                   aria-required="true"
                                   :invalid="errorFields.includes('items.' + index + '.discount')"
                        />
                        <InputGroupAddon>
                            %
                        </InputGroupAddon>
                    </InputGroup>
                </td>
                <td>
                    <InputGroup>
                        <InputGroupAddon>
                            {{ symbol }}
                        </InputGroupAddon>
                        <InputText :value="discountedPrice(item)" readonly class="w-40" type="number"/>
                    </InputGroup>
                </td>
                <td>
                    <Datepicker v-model="item.due_date"
                                dropdown
                                class="w-40"
                                :placeholder="dateSettings.display"
                                :display-format="dateSettings.display"
                                :format="dateSettings.format"
                                :regex="dateSettings.regex"
                                :separator="dateSettings.separator"
                                :invalid="errorFields.includes('items.' + index + '.due_date')"
                    />
                </td>
                <td class="text-center align-top">
                    <Button @click="removeItem(index)"
                            variant="danger"
                            icon="pi pi-times"
                            class="mt-0.5"
                            size="sm"
                            type="button"
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
            variant="secondary"
            icon="pi pi-plus"
            label="Add Item"
            type="button"
    />
</template>

<style scoped lang="postcss">
@reference "../../css/app.css";

.item-row td {
    @apply border-b border-gray-200 pb-2 align-top pt-2 px-1;
}
</style>
