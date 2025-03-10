import BomOperationsNetworkEdit from '@/components/BomOperationsNetworkEdit.vue';
import vueConfig from '@/vue-config.js';

const {app} = vueConfig();

app.component('BomOperationsNetworkEdit', BomOperationsNetworkEdit)
    .mount('#app');
