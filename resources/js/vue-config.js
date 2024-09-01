import { createApp } from 'vue/dist/vue.esm-bundler'
import { createPinia } from 'pinia'

const pinia = createPinia()

export default function vueConfig() {
    const app = createApp()

    app.use(pinia)

    return {app}
}
