<template>
    <section class="space-y-8">
        <header>
            <p class="text-sm uppercase tracking-[0.4em] text-brand-100/70">Vue d’ensemble</p>
            <h1 class="mt-2 text-3xl font-semibold text-white">Tableau de bord global</h1>
            <p class="text-slate-400 mt-1 max-w-2xl">
                Surveillez les indicateurs clés : nouveaux inscrits, entreprises actives, publications et équipe d’administration.
            </p>
        </header>

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            <article
                v-for="card in cards"
                :key="card.label"
                class="rounded-3xl border border-white/10 bg-gradient-to-br from-slate-900/80 to-slate-900/40 p-6 shadow-xl"
            >
                <p class="text-sm text-slate-400">{{ card.label }}</p>
                <p class="mt-4 text-4xl font-semibold text-white">{{ loading ? '…' : card.value }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ card.helper }}</p>
            </article>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-3xl border border-white/10 bg-white/5 p-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-white">Nouveaux utilisateurs</h2>
                    <span class="text-xs uppercase tracking-widest text-slate-400">5 derniers</span>
                </div>
                <ul class="mt-4 space-y-3">
                    <li
                        v-for="user in stats?.latest_users ?? []"
                        :key="user.id"
                        class="flex items-center justify-between rounded-2xl bg-slate-900/40 px-4 py-3"
                    >
                        <div>
                            <p class="font-medium text-white">{{ user.name }}</p>
                            <p class="text-xs text-slate-400">{{ user.email }}</p>
                        </div>
                        <p class="text-sm text-slate-300">{{ formatDate(user.created_at) }}</p>
                    </li>
                    <li v-if="!loading && (stats?.latest_users?.length ?? 0) === 0" class="text-sm text-slate-400">
                        Aucun utilisateur récent.
                    </li>
                </ul>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-white">Publications récentes</h2>
                    <span class="text-xs uppercase tracking-widest text-slate-400">5 dernières</span>
                </div>
                <ul class="mt-4 space-y-3">
                    <li
                        v-for="post in stats?.latest_posts ?? []"
                        :key="post.id"
                        class="rounded-2xl bg-slate-900/40 px-4 py-3"
                    >
                        <p class="font-medium text-white">{{ post.title }}</p>
                        <p class="text-xs text-slate-400">
                            {{ post.user?.name ?? 'Utilisateur' }} · {{ post.company?.name ?? 'Sans entreprise' }}
                        </p>
                        <p class="text-xs text-slate-500 mt-1">{{ formatDate(post.created_at) }}</p>
                    </li>
                    <li v-if="!loading && (stats?.latest_posts?.length ?? 0) === 0" class="text-sm text-slate-400">
                        Aucune publication récente.
                    </li>
                </ul>
            </div>
        </div>

        <p v-if="errorMessage" class="rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
            {{ errorMessage }}
        </p>
    </section>
</template>

<script setup>
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';

const stats = ref(null);
const loading = ref(true);
const errorMessage = ref('');

const cards = computed(() => [
    {
        label: 'Utilisateurs actifs',
        value: stats.value?.totals?.users ?? 0,
        helper: 'comptes membres',
    },
    {
        label: 'Entreprises référencées',
        value: stats.value?.totals?.companies ?? 0,
        helper: 'profils professionnels',
    },
    {
        label: 'Publications en ligne',
        value: stats.value?.totals?.posts ?? 0,
        helper: 'offres / actualités',
    },
    {
        label: 'Administrateurs',
        value: stats.value?.totals?.admins ?? 0,
        helper: 'membres de l’équipe',
    },
]);

const fetchStats = async () => {
    loading.value = true;
    errorMessage.value = '';

    try {
        const { data } = await axios.get('/api/admin/stats');
        stats.value = data;
    } catch (error) {
        errorMessage.value = error.response?.data?.message ?? 'Impossible de récupérer les statistiques.';
    } finally {
        loading.value = false;
    }
};

const formatDate = (value) => {
    if (!value) {
        return '—';
    }
    return new Intl.DateTimeFormat('fr-FR', { dateStyle: 'medium', timeStyle: 'short' }).format(new Date(value));
};

onMounted(fetchStats);
</script>
