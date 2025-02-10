import UsersEdit from '@/components/UsersEdit.vue'
import vueConfig from '@/vue-config.js'

const { app } = vueConfig()

app.component('UsersEdit', UsersEdit)
    .mount('#app')
