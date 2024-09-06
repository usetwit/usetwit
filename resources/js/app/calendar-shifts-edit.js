import '../app.js';
import CalendarShifts from './components/CalendarShifts.vue'
import vueConfig from '../vue-config.js'

Date.prototype.addDays = function (days) {
    const date = new Date(this.valueOf())
    date.setUTCDate(date.getUTCDate() + days)
    return date
}

const {app} = vueConfig()

app.component('CalendarShifts', CalendarShifts)
    .mount('#calendar-shifts')
