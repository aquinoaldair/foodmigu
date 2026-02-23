import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import Login from '../pages/Login.vue';
import Dashboard from '../pages/Dashboard.vue';

const routes = [
    {
        path: '/',
        redirect: { name: 'dashboard' },
    },
    {
        path: '/login',
        name: 'login',
        component: Login,
        meta: { guest: true },
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: Dashboard,
        meta: { requiresAuth: true },
    },
];

const router = createRouter({
    history: createWebHistory('/app'),
    routes,
});

router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();

    if (to.meta.requiresAuth) {
        if (!authStore.authenticated && !authStore.user) {
            try {
                await authStore.fetchUser();
            } catch {
                next({ name: 'login' });
                return;
            }
        }
        if (!authStore.authenticated) {
            next({ name: 'login' });
            return;
        }
    }

    if (to.meta.guest && authStore.authenticated) {
        next({ name: 'dashboard' });
        return;
    }

    if (to.meta.guest && !authStore.authenticated && !authStore.user && !authStore.userFetchAttempted) {
        try {
            await authStore.fetchUser();
            if (authStore.authenticated) {
                next({ name: 'dashboard' });
                return;
            }
        } catch {
            // Not authenticated, allow access to guest page
        }
    }

    next();
});

export default router;
