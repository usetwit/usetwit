import Edit from '@/components/Admin/Boms/Versions/Edit.vue';
import vueConfig from '@/vue-config.js';

const {app} = vueConfig();

app.component('Edit', Edit)
    .mount('#app');
