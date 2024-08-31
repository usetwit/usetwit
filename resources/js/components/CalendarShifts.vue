<script setup>
import {computed, ref} from 'vue'
import CalendarShiftsMonth from './CalendarShiftsMonth.vue'
import CalendarShiftsShiftInput from './CalendarShiftsShiftInput.vue'
import {useAxios} from '../composables/useAxios'
import {useToast} from 'primevue/usetoast'
import Toast from 'primevue/toast'
import Button from 'primevue/button'
import Fieldset from 'primevue/fieldset'
import CalendarShiftsSelect from './CalendarShiftsSelect.vue'

const toast = useToast()

const dateList = ref([])
const year = ref(new Date().getFullYear())
const dayTexts = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
const monthNumbers = Array.from(Array(12).keys())
const lastDateClicked = ref(null)
const isLoading = ref(false)

const props = defineProps({
    route: {
        required: true,
        type: String,
    },
    routeUpdate: {
        required: true,
        type: String,
    },
    calendarList: {
        required: true,
        type: Array,
    },
})

const updateShifts = (shifts) => {
    let activeDates = activeList.value.map(a => a.id)

    dateList.value.forEach(a => {
            if (activeDates.includes(a.id)) {
                a.nwd = shifts.nwd
                a.shift1_start = shifts.shift1_start
                a.shift1_end = shifts.shift1_end
                a.shift2_start = shifts.shift2_start
                a.shift2_end = shifts.shift2_end
                a.shift3_start = shifts.shift3_start
                a.shift3_end = shifts.shift3_end
                a.shift4_start = shifts.shift4_start
                a.shift4_end = shifts.shift4_end
                a.isModified = getIsModified(a)
            }
        }
    )

    save()
}

const clearActive = () => {
    activeList.value.forEach(a => {
        a.active = false
    })
}

const singleClick = (date) => {
    if (!(date.active && activeList.value.length === 1)) {
        clearActive()

        date.active = true
        date.lastClicked = Date.now()

        lastDateClicked.value = date
    }
}

const shiftClick = (date) => {
    if (activeList.value.length === 0) {
        singleClick(date)
    } else {
        let minDate = null
        let maxDate = null

        minDate = Math.min(date.timestamp, lastDateClicked.value.timestamp)
        maxDate = Math.max(date.timestamp, lastDateClicked.value.timestamp)

        dateList.value.forEach(a => {
            if (a.timestamp >= minDate && a.timestamp <= maxDate) {
                a.active = true

                if (lastDateClicked.value.timestamp !== a.timestamp) {
                    a.lastClicked = Date.now()
                }
            }
        })
    }
}

const ctrlClick = (date) => {
    date.active = !date.active
    date.lastClicked = Date.now()

    if (date.active === true) {
        lastDateClicked.value = date
    }
}

const changeYear = direction => {
    if (direction === 'decrease' && year.value > 2020) {
        year.value--
    } else if (direction === 'increase' && year.value < 2050) {
        year.value++
    }

    getDates()
}

const save = async () => {
    isLoading.value = true

    const {data, errors, getResponse} = useAxios(
        props.routeUpdate,
        {
            dates: dateList.value,
            year: year.value,
        },
        'patch'
    )
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
        toast.add({severity: 'success', summary: 'Changes Applied', group: 'br', detail: data.value.data, life: 3000})
    }

    isLoading.value = false
}

const getCalendarShifts = async () => {
    const {data, errors, getResponse} = useAxios(
        props.route,
        {
            year: year.value,
        },
    )
    await getResponse()

    if (errors.value.raw) {
        toast.add({
            severity: 'error',
            summary: errors.value.message,
            group: 'br',
            detail: errors.value.list,
            life: 3000
        })

        return []
    } else {
        return data.value
    }
}

const filterMonth = (month) => {
    return dateList.value.filter(a => a.month === month)
}

function getIsModified(date) {
    return date.shift1_start !== '00:00' || date.shift1_end !== '00:00'
}

const getDates = async () => {
    isLoading.value = true

    let dateArray = []
    let currentDate = new Date(Date.UTC(year.value, 0, 1));
    const endDate = new Date(Date.UTC(year.value, 11, 31));
    const calendarShifts = await getCalendarShifts()

    while (currentDate <= endDate) {
        let id = currentDate.toISOString().split('T')[0]
        let dateProperties = {
            id: id,
            timestamp: currentDate.getTime(),
            year: currentDate.getUTCFullYear(),
            month: currentDate.getUTCMonth(),
            day: currentDate.getUTCDate(),
            dayOfWeek: currentDate.getUTCDay(),
            active: false,
            lastClicked: 0,
            shift_date: id,
        }

        let merged = null

        if (typeof calendarShifts[id] !== 'undefined') {
            merged = {...calendarShifts[id], ...dateProperties}
        } else {
            let temp = {
                'shift1_start': '00:00',
                'shift1_end': '00:00',
                'shift2_start': '',
                'shift2_end': '',
                'shift3_start': '',
                'shift3_end': '',
                'shift4_start': '',
                'shift4_end': '',
                'nwd': false,
            }

            merged = {...temp, ...dateProperties}
        }

        dateArray.push({...merged, ...{isModified: getIsModified(merged)}})

        currentDate = currentDate.addDays(1)
    }

    dateList.value = dateArray
    isLoading.value = false
}

getDates()
const selectDays = (day) => {
    dateList.value.forEach(a => {
        if (a.dayOfWeek === day) {
            a.active = true

            if (lastDateClicked.value !== a.timestamp) {
                a.lastClicked = Date.now()
            }
        }
    })
}

const activeList = computed(() => {
    return dateList.value.filter(a => a.active === true).sort(function (a, b) {
        return a.lastClicked - b.lastClicked
    })
})
</script>

<template>
    <Toast position="bottom-right" group="br"/>

    <div class="p-4">

        <CalendarShiftsSelect :calendar-list="props.calendarList"/>

        <Fieldset legend="Select Days of Week" :toggleable="true">
            <Button v-for="(day, key) in dayTexts"
                    @click="selectDays(key + 1 === 7 ? 0 : key + 1)"
                    :key="day"
                    :label="day"
                    severity="contrast"
                    class="mr-1"
            />
            <Button @click="clearActive"
                    severity="warn"
                    label="Clear"
            />
        </Fieldset>

        <div class="flex items-center justify-center text-gray-900 text-3xl my-2">
            <Button @click="changeYear('decrease')"
                    :disabled="isLoading || year <= 2020"
                    :loading="isLoading"
                    icon="pi pi-chevron-left"
                    severity="contrast"
                    size="small"
                    class="leading-6"
            />
            <span class="mx-6 font-bold">{{ year }}</span>
            <Button @click="changeYear('increase')"
                    :disabled="isLoading || year >= 2050"
                    :loading="isLoading"
                    icon="pi pi-chevron-right"
                    severity="contrast"
                    size="small"
                    class="leading-6"
            />
        </div>

        <div class="flex">
            <div class="grow grid md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-4">
                <CalendarShiftsMonth
                    v-if="dateList.length"
                    v-for="monthNumber in monthNumbers"
                    :monthNumber="monthNumber"
                    :dates="filterMonth(monthNumber)"
                    :key="monthNumber"
                    :dayTexts="dayTexts"
                    @single-click="singleClick"
                    @shift-click="shiftClick"
                    @ctrl-click="ctrlClick"
                />
            </div>

            <CalendarShiftsShiftInput :date="activeList[0]"
                                      v-if="activeList.length"
                                      @updateShifts="updateShifts"
                                      v-model:isLoading="isLoading"
            />

        </div>
    </div>
</template>
