import '../app.js';
import UsersIndex from '../components/UsersIndex.vue'
import vueConfig from '../vue-config.js'

const { app } = vueConfig()

app.component('UsersIndex', UsersIndex)
    .mount('#app')
