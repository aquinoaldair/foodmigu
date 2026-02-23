import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import Login from '../pages/Login.vue';
import AdminLayout from '../layouts/AdminLayout.vue';
import Dashboard from '../pages/Dashboard.vue';
import MenuCategoryList from '../modules/menuCategories/MenuCategoryList.vue';
import MenuCategoryCreate from '../modules/menuCategories/MenuCategoryCreate.vue';
import MenuCategoryEdit from '../modules/menuCategories/MenuCategoryEdit.vue';
import MenuList from '../modules/menus/MenuList.vue';
import MenuCreate from '../modules/menus/MenuCreate.vue';
import MenuEdit from '../modules/menus/MenuEdit.vue';
import WeeklyMenuBuildList from '../modules/weeklyMenuBuilds/WeeklyMenuBuildList.vue';
import WeeklyMenuBuildCreate from '../modules/weeklyMenuBuilds/WeeklyMenuBuildCreate.vue';
import WeeklyMenuBuildEdit from '../modules/weeklyMenuBuilds/WeeklyMenuBuildEdit.vue';

const routes = [
    {
        path: '/login',
        name: 'login',
        component: Login,
        meta: { guest: true },
    },
    {
        path: '/',
        component: AdminLayout,
        meta: { requiresAuth: true },
        children: [
            {
                path: '',
                redirect: { name: 'dashboard' },
            },
            {
                path: 'dashboard',
                name: 'dashboard',
                component: Dashboard,
            },
            {
                path: 'menu-categories',
                name: 'menu-categories.index',
                component: MenuCategoryList,
            },
            {
                path: 'menu-categories/create',
                name: 'menu-categories.create',
                component: MenuCategoryCreate,
            },
            {
                path: 'menu-categories/:id/edit',
                name: 'menu-categories.edit',
                component: MenuCategoryEdit,
            },
            {
                path: 'menus',
                name: 'menus.index',
                component: MenuList,
            },
            {
                path: 'menus/create',
                name: 'menus.create',
                component: MenuCreate,
            },
            {
                path: 'menus/:id/edit',
                name: 'menus.edit',
                component: MenuEdit,
            },
            {
                path: 'weekly-menu-builds',
                name: 'weekly-menu-builds.index',
                component: WeeklyMenuBuildList,
            },
            {
                path: 'weekly-menu-builds/create',
                name: 'weekly-menu-builds.create',
                component: WeeklyMenuBuildCreate,
            },
            {
                path: 'weekly-menu-builds/:id/edit',
                name: 'weekly-menu-builds.edit',
                component: WeeklyMenuBuildEdit,
            },
        ],
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
