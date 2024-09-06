import vueConfig from '../vue-config.js'
import Navbar from '../components/Navbar.vue'

const { app } = vueConfig()

app.component('Navbar', Navbar)
    .mount('#navbar')
