import UsersCreate from '@/components/UsersCreate.vue'
import vueConfig from '@/vue-config.js'

const { app } = vueConfig()

app.component('UsersCreate', UsersCreate)
    .mount('#app')
