import Edit from '@/components/Admin/Calendars/Shifts/Edit.vue'
import vueConfig from '@/vue-config.js'

Date.prototype.addDays = function (days) {
    const date = new Date(this.valueOf())
    date.setUTCDate(date.getUTCDate() + days)
    return date
}

const { app } = vueConfig()

app.component('Edit', Edit)
    .mount('#app')
