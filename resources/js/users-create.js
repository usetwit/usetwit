import './app'
import UsersCreate from './components/UsersCreate.vue'
import vueConfig from './vue-config'

const {app} = vueConfig()

app.component('UsersCreate', UsersCreate)
    .mount('#users-create')
