import './app'
import SalesOrderCreate from './components/SalesOrderCreate.vue'
import vueConfig from './vue-config'

const {app} = vueConfig()

app.component('SalesOrderCreate', SalesOrderCreate)
    .mount('#sales-order-create')
