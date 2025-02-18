<script setup>
import {ref, useTemplateRef, watch} from 'vue'
import Tab from '@/components/Tab.vue'
import Wrapper from '@/components/Form/Wrapper.vue'
import InputText from '@/components/Form/InputText.vue'
import Button from '@/components/Form/Button.vue'
import FormWrapper from '@/components/Form/Wrapper.vue'
import Select from '@/components/Form/Select.vue'
import Datepicker from '@/components/Form/Datepicker.vue'
import Password from '@/components/Form/Password.vue'
import useAxios from '@/composables/useAxios.js'
import {debounce, pick} from 'lodash'
import {toast} from 'vue3-toastify'
import Csrf from '@/components/Form/Csrf.vue'
import Modal from '@/components/Modal.vue'
import Textarea from "@/components/Form/Textarea.vue";
import Wysiwyg from "@/components/Form/Wysiwyg.vue";

const props = defineProps({
    routes: {type: Object, required: true},
    location: {type: Object, required: true},
    countries: {type: Array, required: true},
    dateSettings: {type: Object, required: true},
    permissions: {type: Object, required: true},
})

const locationExists = ref(false)
const isLoading = ref(false)
const tabs = ref([])
const activeTab = ref(null)
const errorFields = ref([])
const location = ref({...props.location})

const tabTexts = {
    info: '<i class="pi pi-user-edit hidden md:inline-block mr-2"></i>Info',
    opening_hours: '<i class="pi pi-building hidden md:inline-block mr-2"></i>Opening Hours',
    address: '<i class="pi pi-map hidden md:inline-block mr-2"></i>Address',
}

for (const [key, text] of Object.entries(tabTexts)) {
    tabs.value.push({key, text})
}

const currentHash = window.location.hash.slice(1);
const activeTabFromHash = tabs.value.find(tab => tab.key === currentHash)
activeTab.value = activeTabFromHash || tabs.value[0]

const handleClick = text => {
    activeTab.value = text
}

const update = async (route, fields) => {
    isLoading.value = true

    const {errors, status, data, getResponse} = useAxios(
        route,
        pick(location.value, fields),
        'patch',
    )

    await getResponse()

    if (status.value === 200) {
        toast.success(data.value)
    } else {
        errorFields.value = errors.value.fields
    }

    isLoading.value = false
}

const updateInfo = async () => {
    await update(props.routes.info, [
        'name',
        'description',
    ])
}

const updateAddress = async () => {
    await update(props.routes.address, [
        'address',
    ])
}

const deleteModalIsVisible = ref(false)
const deleteLocationForm = useTemplateRef('deleteLocationForm')

const checkLocation = async () => {
    if (location.value.location) {
        isLoading.value = true

        const {data, errors, getResponse} = useAxios(
            props.routes.check_location,
            {location: location.value.location},
            'post',
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

const debouncedCheckLocation = debounce(checkLocation, 300, {leading: true, trailing: true})
</script>

<template>
    <ul v-if="tabs.length" class="flex mx-0 lg:mx-4 overflow-x-auto -mb-[1px]">
        <Tab v-for="tab in tabs"
             :tab="tab"
             :key="tab.key"
             :active="tab.key === activeTab.key"
             @clicked="handleClick"
        />
    </ul>

    <div id="content">
        <div v-if="activeTab.key === 'info'">
            <form @submit.prevent="updateInfo" autocomplete="off">
                <Wrapper required>
                    <template #text>
                        <label for="name">
                            Location Name
                        </label>
                    </template>

                    <template #input>
                        <InputText class="rounded-md w-full sm:w-60"
                                   id="name"
                                   maxlength="85"
                                   required
                                   placeholder="Location Name"
                                   v-model="location.name"
                        />
                    </template>
                </Wrapper>

                        <label for="description">
                            Description
                        </label>
                        <Wysiwyg v-model="location.description"/>

                <div class="flex">
                    <Button severity="success"
                            type="submit"
                            aria-label="Save"
                            :loading="isLoading"
                            :disabled="isLoading"
                            label="Save"
                            icon="pi pi-save"
                            class="mx-auto my-4"
                    />
                </div>
            </form>


            <form v-if="location.active && permissions.delete" :action="routes.delete" autocomplete="off" method="post"
                  ref="deleteLocationForm">
                <input type="hidden" name="_method" value="delete"/>
                <Csrf/>

                <div class="text-xl border-t border-red-200 text-red-700 p-4 bg-red-200 uppercase flex items-center">
                    <span class="text-red-500"><i class="pi pi-exclamation-triangle mr-2"></i>Delete Location</span>
                </div>

                <div class="flex bg-red-100">
                    <Button type="button"
                            variant="danger"
                            label="Delete Location"
                            @click="deleteModalIsVisible = true"
                            class="mx-auto my-4"
                            icon="pi pi-times"
                    />
                    <Modal v-if="deleteModalIsVisible"
                           v-model="deleteModalIsVisible"
                           @accepted="deleteLocationForm.submit()"
                           title="Are you sure?"
                           label="Delete"
                           icon="pi pi-times"
                           variant="danger"
                    >
                        Please confirm that you would like to delete this location.
                    </Modal>
                </div>
            </form>

            <form v-else-if="!location.active && permissions.restore" :action="routes.restore" autocomplete="off"
                  method="post">
                <input type="hidden" name="_method" value="patch"/>
                <Csrf/>

                <h3>Restore Location</h3>

                <div class="flex">
                    <Button type="submit" label="Restore Location" icon="pi pi-user-plus" class="mx-auto my-4"/>
                </div>
            </form>

        </div>
        <div v-if="activeTab.key === 'opening_hours'">
            Calendar Placeholder
        </div>
        <div v-if="activeTab.key === 'address'">
            <form @submit.prevent="updateAddress" autocomplete="off">
                Address Placeholder
            </form>
        </div>
    </div>
</template>
