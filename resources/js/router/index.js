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
import DiningHallList from '../modules/diningHalls/DiningHallList.vue';
import DiningHallCreate from '../modules/diningHalls/DiningHallCreate.vue';
import DiningHallEdit from '../modules/diningHalls/DiningHallEdit.vue';
import DinerList from '../modules/diners/DinerList.vue';
import DinerCreate from '../modules/diners/DinerCreate.vue';
import DinerEdit from '../modules/diners/DinerEdit.vue';
import DinerImport from '../modules/diners/DinerImport.vue';
import PublicLanding from '../modules/publicMenu/PublicLanding.vue';
import PublicIdentify from '../modules/publicMenu/PublicIdentify.vue';
import PublicMenuList from '../modules/publicMenu/PublicMenuList.vue';
import PublicDayView from '../modules/publicMenu/PublicDayView.vue';
import ProfileView from '../modules/profile/ProfileView.vue';
import SystemGuideView from '../pages/SystemGuideView.vue';

const routes = [
    {
        path: '/login',
        name: 'login',
        component: Login,
        meta: { guest: true },
    },
    {
        path: '/c/:code',
        name: 'public.landing',
        component: PublicLanding,
        meta: { public: true },
    },
    {
        path: '/c/:code/identify',
        name: 'public.identify',
        component: PublicIdentify,
        meta: { public: true },
    },
    {
        path: '/c/:code/menus',
        name: 'public.menus',
        component: PublicMenuList,
        meta: { public: true },
    },
    {
        path: '/c/:code/day/:dayId',
        name: 'public.day',
        component: PublicDayView,
        meta: { public: true },
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
                path: 'profile',
                name: 'profile',
                component: ProfileView,
            },
            {
                path: 'guia-del-sistema',
                name: 'system-guide',
                component: SystemGuideView,
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
            {
                path: 'dining-halls',
                name: 'dining-halls.index',
                component: DiningHallList,
            },
            {
                path: 'dining-halls/create',
                name: 'dining-halls.create',
                component: DiningHallCreate,
            },
            {
                path: 'dining-halls/:id/edit',
                name: 'dining-halls.edit',
                component: DiningHallEdit,
            },
            {
                path: 'dining-halls/:id/diners',
                name: 'diners.index',
                component: DinerList,
            },
            {
                path: 'dining-halls/:id/diners/create',
                name: 'diners.create',
                component: DinerCreate,
            },
            {
                path: 'dining-halls/:id/diners/import',
                name: 'diners.import',
                component: DinerImport,
            },
            {
                path: 'dining-halls/:id/diners/:dinerId/edit',
                name: 'diners.edit',
                component: DinerEdit,
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
