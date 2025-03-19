import BomEdit from '@/components/Admin/Bom/BomEdit.vue';
import vueConfig from '@/vue-config.js';

const {app} = vueConfig();

app.component('BomEdit', BomEdit)
    .mount('#app');
