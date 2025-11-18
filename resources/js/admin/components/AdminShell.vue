<template>
    <div class="min-h-screen bg-slate-950 text-slate-100 flex">
        <aside class="hidden lg:flex lg:flex-col w-64 border-r border-white/5 bg-slate-900/40 backdrop-blur">
            <div class="px-6 pt-10 pb-6">
                <p class="text-xs uppercase tracking-widest text-brand-100/70">ProConnect</p>
                <p class="text-2xl font-semibold text-white">Administration</p>
            </div>

            <nav class="flex-1 px-4 space-y-1">
                <RouterLink
                    v-for="link in links"
                    :key="link.label"
                    :to="link.to"
                    :class="[
                        'flex items-center gap-3 rounded-xl px-4 py-3 text-sm transition',
                        isActive(link)
                            ? 'bg-brand-500/20 text-white border border-brand-500/40'
                            : 'text-slate-300 hover:bg-white/5 border border-transparent',
                    ]"
                >
                    <span class="text-lg" aria-hidden="true">{{ link.icon }}</span>
                    <span class="font-medium">{{ link.label }}</span>
                </RouterLink>
            </nav>

            <div class="p-6 border-t border-white/5">
                <p class="text-xs uppercase tracking-widest text-slate-400">Connecté</p>
                <p class="text-base font-semibold text-white">{{ auth.state.admin?.name }}</p>
                <p class="text-sm text-slate-400 truncate">{{ auth.state.admin?.email }}</p>
                <button
                    type="button"
                    class="mt-4 inline-flex items-center justify-center w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium text-slate-200 hover:bg-white/10 transition"
                    @click="handleLogout"
                >
                    Déconnexion
                </button>
            </div>
        </aside>

        <div class="flex-1 flex flex-col">
            <header class="lg:hidden sticky top-0 z-20 bg-slate-900/80 backdrop-blur border-b border-white/5">
                <div class="flex items-center justify-between px-4 py-3">
                    <div>
                        <p class="text-xs uppercase tracking-widest text-brand-100/70">ProConnect</p>
                        <p class="text-lg font-semibold text-white">Panel Admin</p>
                    </div>
                    <button
                        type="button"
                        class="rounded-full border border-white/10 px-4 py-2 text-sm font-medium text-slate-200"
                        @click="handleLogout"
                    >
                        Déconnexion
                    </button>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto px-4 py-8 lg:px-10">
                <RouterView />
            </main>
        </div>
    </div>
</template>

<script setup>
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const route = useRoute();
const router = useRouter();
const auth = useAuthStore();

const links = [
    { label: 'Tableau de bord', to: { name: 'admin.dashboard' }, icon: '📊' },
    { label: 'Utilisateurs', to: { name: 'admin.users' }, icon: '👥' },
    { label: 'Entreprises', to: { name: 'admin.companies' }, icon: '🏢' },
    { label: 'Publications', to: { name: 'admin.posts' }, icon: '📰' },
    { label: 'Administrateurs', to: { name: 'admin.admins' }, icon: '⚙️' },
];

const isActive = (link) => route.name === link.to.name;

const handleLogout = async () => {
    await auth.logout();
    router.push({ name: 'admin.login' });
};
</script>
