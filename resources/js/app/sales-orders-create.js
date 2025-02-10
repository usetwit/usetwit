import SalesOrdersCreate from '@/components/SalesOrdersCreate.vue'
import vueConfig from '@/vue-config.js'

const { app } = vueConfig()

app.component('SalesOrdersCreate', SalesOrdersCreate)
    .mount('#app')
