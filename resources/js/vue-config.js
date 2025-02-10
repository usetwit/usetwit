import { createApp } from 'vue/dist/vue.esm-bundler'
import { createPinia } from 'pinia'
import Vue3Toastify, { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'

const pinia = createPinia()

export default function vueConfig() {
    const app = createApp()

    app.use(pinia)
        .use(Vue3Toastify, {
            autoClose: 3000,
            position: toast.POSITION.BOTTOM_RIGHT,
            style: {
                fontSize: '0.75rem',
                minWidth: '320px',
                width: 'auto',
            },
        })

    return { app }
}
