<script setup>
import SalesOrderItems from './SalesOrderItems.vue'
import {useToast} from 'primevue/usetoast'
import Toast from 'primevue/toast'
import Button from 'primevue/button'
import {ref} from 'vue'
import {useAxios} from '../composables/useAxios'

const toast = useToast()

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
    dateFormat: {
        type: String,
        required: true,
    },
})

const items = ref([{
    item_name: '',
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

    const {data, errors, getResponse} = useAxios(props.routeStore, {items: items.value})
    await getResponse()

    if (errors.value.raw) {
        errorFields.value = errors.value.fields
        toast.add({
            severity: 'error',
            summary: errors.value.message,
            group: 'br',
            detail: errors.value.list,
            life: 3000,
        })
    } else {
        submitDisabled.value = true
        errorFields.value = []
        toast.add({severity: 'success', summary: 'Success', group: 'br', detail: data.value.data, life: 3000})

        setTimeout(() => window.location.replace(props.routeRedirect), 2000)
    }

    isLoading.value = false
}
</script>


<template>
    <Toast position="bottom-right" group="br"/>

    <form @submit.prevent="save">

        <h3>Customer</h3>
        <input name="customer_ref" value="RIVAUK">

        <h3>Items</h3>

        <div class="p-4">
            <sales-order-items
                :route-stock-bom-search="props.routeStockBomSearch"
                v-model:items="items"
                :error-fields="errorFields"
                :date-format="dateFormat"
            ></sales-order-items>
        </div>

        <div class="flex p-4">
            <Button severity="success"
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

</template>
