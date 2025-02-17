import LocationsCreate from '@/components/LocationsCreate.vue'
import vueConfig from '@/vue-config.js'

const { app } = vueConfig()

app.component('LocationsCreate', LocationsCreate)
    .mount('#app')
