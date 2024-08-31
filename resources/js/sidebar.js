import vueConfig from './vue-config'
import Sidebar from './components/Sidebar.vue'

const { app } = vueConfig()

app.component('Sidebar', Sidebar)
app.mount('#sidebar')
