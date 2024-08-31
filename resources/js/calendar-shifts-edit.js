import './app';
import CalendarShifts from './components/CalendarShifts.vue'
import vueConfig from './vue-config'

Date.prototype.addDays = function (days) {
    const date = new Date(this.valueOf())
    date.setUTCDate(date.getUTCDate() + days)
    return date
}

const {app} = vueConfig()

app.component('CalendarShifts', CalendarShifts)
    .mount('#calendar-shifts')
