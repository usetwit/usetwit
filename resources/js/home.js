import './app';
import Home from './components/Home.vue'
import vueConfig from './vue-config'

const {app} = vueConfig()

app.component('Home', Home)
    .mount('#home')
