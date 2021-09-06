import 'bootstrap'

import {createApp} from 'vue';

import axios from 'axios'
import VueAxios from 'vue-axios'

import App from './controllers/Dashboard';

createApp(App)
    .use(VueAxios, axios)
    .mount('#app');
