<template>
    <section class="space-y-6">
        <header class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-slate-400">Flux des contenus partagés</p>
                <h1 class="text-2xl font-semibold text-white">Publications</h1>
            </div>
            <div class="relative max-w-sm w-full">
                <input
                    v-model="search"
                    type="search"
                    placeholder="Titre ou contenu..."
                    class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white placeholder:text-slate-500 focus:border-brand-500 focus:outline-none"
                />
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400">⌕</span>
            </div>
        </header>

        <div class="space-y-4">
            <article
                v-for="post in posts"
                :key="post.id"
                class="rounded-3xl border border-white/10 bg-slate-900/40 p-5 hover:bg-white/5 transition"
            >
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h2 class="text-xl font-semibold text-white">{{ post.title }}</h2>
                    <p class="text-xs text-slate-400">{{ formatDate(post.created_at) }}</p>
                </div>
                <p class="mt-3 text-sm text-slate-300 whitespace-pre-line">{{ post.content }}</p>
                <div class="mt-4 flex flex-wrap items-center gap-4 text-xs uppercase tracking-widest">
                    <span class="rounded-full bg-brand-500/15 px-3 py-1 text-brand-100">Auteur : {{ post.author?.name ?? 'N/A' }}</span>
                    <span class="rounded-full bg-white/10 px-3 py-1 text-slate-200">Entreprise : {{ post.company?.name ?? 'Aucune' }}</span>
                </div>
            </article>

            <p v-if="!loading && posts.length === 0" class="rounded-3xl border border-dashed border-white/15 px-6 py-10 text-center text-slate-500">
                Aucune publication ne correspond à la recherche.
            </p>
            <p v-if="loading" class="text-sm text-slate-400">Chargement...</p>
        </div>
    </section>
</template>

<script setup>
import axios from 'axios';
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';

const posts = ref([]);
const loading = ref(false);
const search = ref('');
let debounceId;

const fetchPosts = async () => {
    loading.value = true;

    try {
        const { data } = await axios.get('/api/admin/posts', {
            params: {
                search: search.value || undefined,
            },
        });

        posts.value = data.data;
    } finally {
        loading.value = false;
    }
};

watch(
    () => search.value,
    () => {
        clearTimeout(debounceId);
        debounceId = setTimeout(fetchPosts, 400);
    }
);

onMounted(fetchPosts);
onBeforeUnmount(() => clearTimeout(debounceId));

const formatDate = (value) => {
    if (!value) {
        return '—';
    }

    return new Intl.DateTimeFormat('fr-FR', { dateStyle: 'medium', timeStyle: 'short' }).format(new Date(value));
};
</script>
