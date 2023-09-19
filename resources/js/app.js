import './bootstrap';

import 'admin-lte/plugins/bootstrap/js/bootstrap.bundle.min.js';
import 'admin-lte/dist/js/adminlte.min.js';
import { createPinia } from 'pinia';
import { createApp } from 'vue/dist/vue.esm-bundler.js';
import { createRouter, createWebHistory } from 'vue-router';
import Routes from './routes.js';
import Login from './pages/auth/Login.vue';
import { useAuthUserStore } from './stores/AuthUserStore';





const pinia = createPinia();
const app = createApp({});

const router = createRouter({
    routes: Routes,
    history: createWebHistory(),
});

// router.beforeEach(async (to, from) => {
//     const authUserStore = useAuthUserStore();
//     if (authUserStore.user.name === '' && to.name !== 'admin.login') {

//         await Promise.all([
//             authUserStore.getAuthUser(),
//         ]);
//     }
// });

app.use(pinia);

app.use(router);

app.component('Login', Login);

app.mount('#app');