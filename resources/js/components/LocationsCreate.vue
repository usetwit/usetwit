<script setup>
import { ref, watch } from 'vue'
import Wrapper from '@/components/Form/Wrapper.vue'
import InputText from '@/components/Form/InputText.vue'
import Datepicker from "@/components/Form/Datepicker.vue"
import Select from '@/components/Form/Select.vue'
import Button from '@/components/Form/Button.vue'
import Password from '@/components/Form/Password.vue'
import useAxios from '@/composables/useAxios.js'
import { debounce } from 'lodash'
import { toast } from 'vue3-toastify'

const props = defineProps({
    routeCCreate: { type: String, required: true },
})

const locationExists = ref(false)
const isLoading = ref(false)
const location = ref({
    name: '',
})
const submitDisabled = ref(false)
const errorFields = ref([])

const checkLocation = async () => {
    if (location.value.name) {
        isLoading.value = true

        const { data, errors, getResponse } = useAxios(
            props.routeCheckLocation,
            { name: location.value.name },
            'post'
        )
        await getResponse()

        if (!errors.value.raw) {
            locationExists.value = data.value.length > 0
        }

        isLoading.value = false
    } else {
        locationExists.value = false
    }
}

const save = async () => {
    isLoading.value = true

    const { data, status, errors, getResponse } = useAxios(
        props.routeStore,
        {
            ...location.value,
        },
        'post'
    )
    await getResponse()

    if (status.value === 200) {
        submitDisabled.value = true
        errorFields.value = []

        toast.success(data.value.message)

        setTimeout(() => window.location.replace(data.value.redirect), 2000)
    } else if (errors.value.raw) {
        errorFields.value = errors.value.fields
    }

    isLoading.value = false
}

const debouncedCheckLocation = debounce(checkLocation, 300, { leading: true, trailing: true })

watch(() => location.value.location, (newValue) => {
    location.value.location = newValue.toLowerCase()
})
</script>

<template>
    <div id="content">
        <form @submit.prevent="save" autocomplete="off">

            <Wrapper>
                <template #text>
                    <label for="name">
                        Location Name
                    </label>
                </template>

                <template #input>
                    <InputText class="rounded-md w-full sm:w-60"
                               maxlength="255"
                               id="name"
                               name="name"
                               type="text"
                               placeholder="Name"
                    />
                </template>
            </Wrapper>

            <Wrapper>
                <template #text>
                    <label for="description">
                        Description
                    </label>
                </template>

                <template #input>
                    <textarea class="rounded-md w-full sm:w-60 border p-2"
                               maxlength="255"
                               id="description"
                               placeholder="Description"
                    ></textarea>
                </template>
            </Wrapper>

        </form>
    </div>
</template>
