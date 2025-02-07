<script setup>
import SalesOrderItems from './SalesOrderItems.vue'
import Button from '@/components/Form/Button.vue'
import { ref } from 'vue'
import useAxios from '@/composables/useAxios'
import { toast } from 'vue3-toastify'

const props = defineProps({
    routeStore: {
        type: String,
        required: true,
    },
    routeStockBomSearch: {
        type: String,
        required: true,
    },
    routeRedirect: {
        type: String,
        required: true,
    },
    dateSettings: { type: Object, required: true },
})

const items = ref([{
    long_id: '',
    price: '0.00',
    discount: 0,
    discounted_price: 0,
    batches: [1],
    due_date: null,
    due_date_ymd: null,
    id: 0
}])

const isLoading = ref(false)
const submitDisabled = ref(false)
const errorFields = ref([])

const save = async () => {
    isLoading.value = true
    errorFields.value = []

    const { status, data, errors, getResponse } = useAxios(props.routeStore, { items: items.value })
    await getResponse()

    if (status.value === 200) {
        toast.success(data.value)

        setTimeout(() => window.location.replace(props.routeRedirect), 2000)
    } else {
        errorFields.value = errors.value.fields
    }

    isLoading.value = false
}
</script>


<template>
    <div id="content">
        <form @submit.prevent="save">

            <h3>Customer</h3>
            <input name="customer_ref" value="RIVAUK">

            <h3>Items</h3>

            <div class="p-4">
                <sales-order-items :route-stock-bom-search="props.routeStockBomSearch"
                                   v-model="items"
                                   :error-fields="errorFields"
                                   :date-settings="dateSettings"
                ></sales-order-items>
            </div>

            <div class="flex p-4">
                <Button variant="success"
                        type="submit"
                        aria-label="Create Sales Order"
                        :loading="isLoading"
                        :disabled="submitDisabled"
                        label="Create Sales Order"
                        icon="pi pi-file-plus"
                        class="mx-auto mt-4"
                />
            </div>

        </form>
    </div>

</template>
