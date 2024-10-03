import vueConfig from '@/vue-config.js'
import Home from '@/components/Home.vue'

const { app } = vueConfig()

app.component('Home', Home)
    .mount('#app')
