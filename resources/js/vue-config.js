import {createApp} from 'vue/dist/vue.esm-bundler'
import PrimeVue from 'primevue/config'
import ToastService from 'primevue/toastservice'
import Tooltip from 'primevue/tooltip'
import {createPinia} from 'pinia'
import Aura from './presets/aura'
import {usePassThrough} from 'primevue/passthrough'

const pinia = createPinia()

const CustomPreset = usePassThrough(
    Aura,
    {},
    {
        mergeSections: true,
        mergeProps: true,
    }
);

export default function vueConfig() {
    const app = createApp()

    app.use(PrimeVue, {
        unstyled: true,
        pt: CustomPreset,
        ripple: true,
        theme: {
            options: {
                darkModeSelector: '.dark',
                cssLayer: false,
            }
        },
    })
        .use(ToastService)
        .use(pinia)
        .directive('tooltip', Tooltip)

    return {app}
}
