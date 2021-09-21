import {createApp} from 'vue';
import App from './Pages/Admin/Index.vue';
import PrimeVue from 'primevue/config';
import ToastService from 'primevue/toastservice';

import {createRouter, createWebHistory} from 'vue-router'

const app = createApp(App);

app.use(PrimeVue);
app.use(ToastService);

import admin_routes from "@/Routes/admin";

const router = createRouter({
    history: createWebHistory(),
    routes: admin_routes
});

app.use(router);
app.mount('#app');
