/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
require('admin-lte');

window.Vue = require('vue');
import moment from 'moment';
import VueRouter from 'vue-router';
import { Form, HasError, AlertError } from 'vform';

import Gate from "./Gate";
Vue.prototype.$gate = new Gate(window.user);

Vue.component('pagination', require('laravel-vue-pagination'));

Vue.component(HasError.name, HasError)
Vue.component(AlertError.name, AlertError)
window.Form = Form;
Vue.use(VueRouter);

// custom alert
import Swal from 'sweetalert2'
window.Swal = Swal;

const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    onOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
});

window.Toast = Toast;

// custom progressbar 
import VueProgressBar from 'vue-progressbar'
Vue.use(VueProgressBar, {
    color: 'rgb(143, 255, 199)',
    failedColor: 'red',
    height: '5px'
});


const routes = [{
        path: '/home',
        component: require('./components/dashboard/dashboard.vue').default
    },{
        path: '/dashboard',
        component: require('./components/dashboard/dashboard.vue').default
    },
    {
        path: '/dashboard/profile',
        component: require('./components/dashboard/users/profile.vue').default
    },
    {
        path: '/dashboard/Devoloper',
        component: require('./components/Devoloper.vue').default
    },
    {
        path: '/dashboard/users',
        component: require('./components/dashboard/users/users.vue').default
    },
    {
        path: '*',
        component: require('./components/NotFound.vue').default
    }
];

const router = new VueRouter({
    mode: 'history',
    routes // short for `routes: routes`
});

Vue.filter('UpText', function (text) {
    return text.charAt(0).toUpperCase() + text.slice(1)
});

Vue.filter('myDate', function (created) {
    //return moment(created).format('MMMM Do YYYY');
    return moment(created).fromNow();
});

window.Fire = new Vue();

Vue.component(
    'passport-clients',
    require('./components/passport/Clients.vue').default
);

Vue.component(
    'passport-authorized-clients',
    require('./components/passport/AuthorizedClients.vue').default
);

Vue.component(
    'passport-personal-access-tokens',
    require('./components/passport/PersonalAccessTokens.vue').default
);

const app = new Vue({
    el: '#app',
    router,
    data: {
        search: ''
    },
    methods: {
        searchit: _.debounce(() => {
            Fire.$emit('searching');
        }, 1000),

        printme() {
            window.print();
        }
    }
});
