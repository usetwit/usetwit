import vueConfig from '../vue-config.js'
import Sidebar from '../components/Sidebar.vue'

const { app } = vueConfig()

app.component('Sidebar', Sidebar)
    .mount('#sidebar')
