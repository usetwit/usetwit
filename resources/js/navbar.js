import vueConfig from './vue-config'
import Navbar from './components/Navbar.vue'

const { app } = vueConfig()

app.component('Navbar', Navbar)
app.mount('#navbar')
