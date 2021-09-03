/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

import 'bootstrap'

// any CSS you import will output into a single css file (app.scss in this case)
import './styles/app.scss'

import {createApp} from "vue";

const app = createApp({
    template: '<h1>Hello !</h1>'
}).mount('#app');
