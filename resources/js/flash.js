import { createApp } from "vue/dist/vue.esm-bundler";
import Vue3Toasity, {toast} from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import Flash from "./components/Flash.vue";

const app = createApp()
app.use(
    Vue3Toasity,
    {
        autoClose: 3000,
        position: toast.POSITION.BOTTOM_RIGHT
    }
)
app.component('Flash', Flash)
app.mount('#flash')
