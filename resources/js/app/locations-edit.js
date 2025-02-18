import LocationsEdit from '@/components/LocationsEdit.vue'
import vueConfig from '@/vue-config.js'

const { app } = vueConfig()

app.component('LocationsEdit', LocationsEdit)
    .mount('#app')
