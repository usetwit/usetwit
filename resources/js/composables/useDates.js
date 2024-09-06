import { computed, ref } from 'vue'

export function useDates(initialDate = new Date(), numberOfMonths = 1) {

    const year = ref(initialDate.getFullYear())
    const month = ref(initialDate.getMonth())
    const monthTexts = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
    const dayTexts = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']

    const today = day => {
        const today = new Date()
        return today.getFullYear() === day.getFullYear() && today.getMonth() === day.getMonth() && today.getDate() === day.getDate()
    }

    const generateMonths = () => {
        const monthsList = [];
        let monthIndex = month.value;
        let yearIndex = year.value;

        for (let i = 0; i < numberOfMonths; i++) {
            monthsList.push({ month: monthIndex, year: yearIndex });

            monthIndex++;
            if (monthIndex > 11) {
                monthIndex = 0;
                yearIndex++;
            }
        }

        return monthsList;
    };

    const months = computed(() => generateMonths());

    const monday = (date) => {
        const day = date.getDay()
        const diff = date.getDate() - day + (day === 0 ? -6 : 1)

        return new Date(date.getFullYear(), date.getMonth(), diff)
    }

    const sunday = (date) => {
        const day = date.getDay()
        const diff = 7 - day
        return new Date(date.getFullYear(), date.getMonth(), date.getDate() + diff)
    }

    const preDates = (date) => {
        const dates = []
        const firstDayOfMonth = new Date(date.getFullYear(), date.getMonth(), 1)
        let startDate = monday(firstDayOfMonth)

        while (startDate < firstDayOfMonth) {
            dates.push(startDate)
            startDate = startDate.addDays(1)
        }

        return dates
    }

    const lastDayOfMonth = (year, month) => new Date(year, month + 1, 0)

    const postDates = (lastDayOfMonth) => {
        const dates = []
        let nextDay = lastDayOfMonth.addDays(1)
        let sundayDate = sunday(lastDayOfMonth)

        while (nextDay <= sundayDate) {
            dates.push(new Date(nextDay))
            nextDay = nextDay.addDays(1)
        }

        return dates
    }

    const makeMonth = (year, month, withPre = false, withPost = false) => {
        const dates = []
        const firstDay = new Date(year, month, 1)
        const lastDay = lastDayOfMonth(year, month)

        if (withPre) {
            dates.push(...preDates(firstDay))
        }

        let currentDay = new Date(firstDay)

        while (currentDay <= lastDay) {
            dates.push(new Date(currentDay))
            currentDay = currentDay.addDays(1)
        }

        if (withPost) {
            dates.push(...postDates(lastDay))

            if (dates.length === 35) {
                const lastDate = dates[dates.length - 1].addDays(1)
                dates.push(lastDate)
                dates.push(...postDates(lastDate))
            }
        }

        return dates
    }

    const changeMonth = (direction) => {
        if (direction === 'increase') {
            month.value = (month.value + 1 === 12) ? 0 : month.value + 1
            year.value += (month.value === 0) ? 1 : 0
        } else if (direction === 'decrease') {
            month.value = (month.value - 1 === -1) ? 11 : month.value - 1
            year.value -= (month.value === 11) ? 1 : 0
        }
    }

    return { today, month, year, months, changeMonth, makeMonth, preDates, monthTexts, dayTexts }
}
