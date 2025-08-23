import Index from '@/components/Admin/Boms/Index.vue';
import vueConfig from '@/vue-config.js';

const {app} = vueConfig();

app.component('Index', Index)
    .mount('#app');
