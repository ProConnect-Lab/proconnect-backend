import { createRouter, createWebHistory } from 'vue-router';
import AdminShell from '../components/AdminShell.vue';
import AdminsPage from '../pages/AdminsPage.vue';
import CompaniesPage from '../pages/CompaniesPage.vue';
import DashboardPage from '../pages/DashboardPage.vue';
import LoginPage from '../pages/LoginPage.vue';
import PostsPage from '../pages/PostsPage.vue';
import UsersPage from '../pages/UsersPage.vue';
import { useAuthStore } from '../stores/auth';

const router = createRouter({
    history: createWebHistory('/admin'),
    routes: [
        {
            path: '/login',
            name: 'admin.login',
            component: LoginPage,
            meta: { guest: true },
        },
        {
            path: '/',
            component: AdminShell,
            meta: { requiresAuth: true },
            children: [
                {
                    path: '',
                    name: 'admin.dashboard',
                    component: DashboardPage,
                },
                {
                    path: 'utilisateurs',
                    name: 'admin.users',
                    component: UsersPage,
                },
                {
                    path: 'entreprises',
                    name: 'admin.companies',
                    component: CompaniesPage,
                },
                {
                    path: 'publications',
                    name: 'admin.posts',
                    component: PostsPage,
                },
                {
                    path: 'administrateurs',
                    name: 'admin.admins',
                    component: AdminsPage,
                },
            ],
        },
        {
            path: '/:pathMatch(.*)*',
            redirect: '/',
        },
    ],
});

router.beforeEach(async (to, from, next) => {
    const auth = useAuthStore();

    if (auth.state.token && !auth.state.admin && !auth.state.loadingProfile) {
        try {
            await auth.fetchProfile();
        } catch {
            await auth.logout();
        }
    }

    if (to.meta.requiresAuth && !auth.state.token) {
        return next({ name: 'admin.login' });
    }

    if (to.meta.guest && auth.state.token) {
        return next({ name: 'admin.dashboard' });
    }

    return next();
});

export default router;
