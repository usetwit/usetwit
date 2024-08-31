import './app'
import UsersIndex from './components/UsersIndex.vue'
import vueConfig from './vue-config'

const {app} = vueConfig()

app.component('UsersIndex', UsersIndex)
    .mount('#users-index')
