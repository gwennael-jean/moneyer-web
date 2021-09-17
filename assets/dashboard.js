import 'bootstrap'

import {createApp} from 'vue'

import axios from 'axios'
import VueAxios from 'vue-axios'

import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'

import './styles/_reset.scss'

import store from "./store";

import App from './controllers/Dashboard'

createApp(App)
    .use(store)
    .use(ElementPlus)
    .use(VueAxios, axios)
    .mount('#app')
