import vueConfig from '../vue-config.js'
import Storage from '../components/Storage.vue'

const { app } = vueConfig()

app.component('Storage', Storage)
    .mount('#storage')
