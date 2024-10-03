import vueConfig from '@/vue-config.js'
import Flash from '@/components/Flash.vue'

const { app } = vueConfig()

app.component('Flash', Flash)
    .mount('#flash')
