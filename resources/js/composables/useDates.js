import { computed, ref, watchEffect } from 'vue'
import { DateTime } from 'luxon'

export function useDates(initialDate = DateTime.utc(), numberOfMonths = 2) {

    const year = ref(initialDate.year)
    const month = ref(initialDate.month)
    const monthTexts = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
    const dayTexts = ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su']

    const months = computed(() => {
        const monthsList = []
        let startMonth = month.value
        let startYear = year.value

        for (let i = 0; i < numberOfMonths; i++) {
            monthsList.push({ month: startMonth, year: startYear, dates: makeMonth(startYear, startMonth) })

            startMonth++
            if (startMonth === 13) {
                startMonth = 1
                startYear++
            }
        }

        return monthsList
    })

    const makeMonth = (year, month) => {
        const dates = [];

        const firstOfMonth = DateTime.utc(year, month, 1)
        let start = firstOfMonth.startOf('week')
        let end = firstOfMonth.endOf('month').endOf('week')
        const diffInDays = end.diff(start, 'days').days;
console.log(firstOfMonth)
        if (Math.round(diffInDays) === 35) {
            end = end.plus({ days: 7 });
        }

        while (start <= end) {
            dates.push(start)
            start = start.plus({days: 1})
        }

        return dates;
    }

    const changeMonth = (direction) => {
        if (direction === 'increase') {
            month.value = (month.value + 1 === 13) ? 1 : month.value + 1
            year.value += (month.value === 1) ? 1 : 0
        } else if (direction === 'decrease') {
            month.value = (month.value - 1 === 0) ? 12 : month.value - 1
            year.value -= (month.value === 12) ? 1 : 0
        }
    }

    return { month, year, changeMonth, makeMonth, months, monthTexts, dayTexts }
}
