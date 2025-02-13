import LocationsIndex from '@/components/LocationsIndex.vue'
import vueConfig from '@/vue-config.js'

const { app } = vueConfig()

app.component('LocationsIndex', LocationsIndex)
    .mount('#app')
