<template>
    <section class="space-y-6">
        <header class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-slate-400">Suivi des membres inscrits</p>
                <h1 class="text-2xl font-semibold text-white">Utilisateurs</h1>
            </div>
            <div class="relative max-w-sm w-full">
                <input
                    v-model="search"
                    type="search"
                    placeholder="Rechercher par nom, email ou adresse..."
                    class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white placeholder:text-slate-500 focus:border-brand-500 focus:outline-none"
                />
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400">⌕</span>
            </div>
        </header>

        <div class="overflow-hidden rounded-3xl border border-white/10 bg-slate-900/40">
            <table class="min-w-full divide-y divide-white/5 text-left text-sm">
                <thead class="uppercase text-xs tracking-widest text-slate-400">
                    <tr>
                        <th class="px-6 py-3">Nom</th>
                        <th class="px-6 py-3">Email</th>
                        <th class="px-6 py-3">Type</th>
                        <th class="px-6 py-3">Entreprises</th>
                        <th class="px-6 py-3">Publications</th>
                        <th class="px-6 py-3">Créé le</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-slate-200">
                    <tr v-for="user in users" :key="user.id" class="hover:bg-white/5">
                        <td class="px-6 py-4">
                            <p class="font-medium text-white">{{ user.name }}</p>
                            <p class="text-xs text-slate-400">{{ user.address }}</p>
                        </td>
                        <td class="px-6 py-4 text-slate-300">{{ user.email }}</td>
                        <td class="px-6 py-4">
                            <span class="rounded-full bg-brand-500/20 px-3 py-1 text-xs font-semibold text-brand-100 uppercase tracking-widest">
                                {{ user.account_type }}
                            </span>
                        </td>
                        <td class="px-6 py-4">{{ user.companies_count }}</td>
                        <td class="px-6 py-4">{{ user.posts_count }}</td>
                        <td class="px-6 py-4 text-slate-400">{{ formatDate(user.created_at) }}</td>
                    </tr>
                    <tr v-if="!loading && users.length === 0">
                        <td colspan="6" class="px-6 py-6 text-center text-slate-500">Aucun utilisateur trouvé.</td>
                    </tr>
                </tbody>
            </table>

            <p v-if="loading" class="px-6 py-4 text-sm text-slate-400">Chargement...</p>
        </div>
    </section>
</template>

<script setup>
import axios from 'axios';
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';

const users = ref([]);
const loading = ref(false);
const search = ref('');

let debounceId;

const fetchUsers = async () => {
    loading.value = true;

    try {
        const { data } = await axios.get('/api/admin/users', {
            params: {
                search: search.value || undefined,
            },
        });

        users.value = data.data;
    } finally {
        loading.value = false;
    }
};

watch(
    () => search.value,
    () => {
        clearTimeout(debounceId);
        debounceId = setTimeout(fetchUsers, 400);
    }
);

onMounted(fetchUsers);
onBeforeUnmount(() => clearTimeout(debounceId));

const formatDate = (value) => {
    if (!value) {
        return '—';
    }

    return new Intl.DateTimeFormat('fr-FR', { dateStyle: 'medium' }).format(new Date(value));
};
</script>
