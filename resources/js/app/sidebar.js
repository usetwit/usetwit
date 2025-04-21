import vueConfig from '../vue-config.js';
import AdminSidebar from '../components/AdminSidebar.vue';

const {app} = vueConfig();

app.component('AdminSidebar', AdminSidebar).mount('#sidebar');
