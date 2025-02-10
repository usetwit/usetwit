import CalendarShiftsEdit from '@/components/CalendarShiftsEdit.vue'
import vueConfig from '@/vue-config.js'

Date.prototype.addDays = function (days) {
    const date = new Date(this.valueOf())
    date.setUTCDate(date.getUTCDate() + days)
    return date
}

const { app } = vueConfig()

app.component('CalendarShiftsEdit', CalendarShiftsEdit)
    .mount('#app')
