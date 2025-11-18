<template>
    <section class="grid gap-8 lg:grid-cols-2">
        <div class="space-y-4">
            <div>
                <p class="text-sm text-slate-400">Equipe en charge de la plateforme</p>
                <h1 class="text-2xl font-semibold text-white">Administrateurs</h1>
            </div>

            <div class="rounded-3xl border border-white/10 bg-slate-900/40 p-5 space-y-4">
                <article
                    v-for="admin in admins"
                    :key="admin.id"
                    class="rounded-2xl border border-white/5 bg-white/5 px-4 py-3"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-white">{{ admin.name }}</p>
                            <p class="text-xs text-slate-400">{{ admin.email }}</p>
                        </div>
                        <p class="text-xs text-slate-500">{{ formatDate(admin.created_at) }}</p>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">{{ admin.address }}</p>
                </article>

                <p v-if="!loading && admins.length === 0" class="text-sm text-slate-500">Aucun administrateur pour le moment.</p>
                <p v-if="loading" class="text-sm text-slate-400">Chargement...</p>
            </div>
        </div>

        <div class="rounded-3xl border border-white/10 bg-gradient-to-br from-brand-900/70 to-slate-900/80 p-6">
            <h2 class="text-xl font-semibold text-white">Créer un administrateur</h2>
            <p class="text-sm text-slate-300">Donnez accès à un nouveau membre en toute sécurité.</p>

            <form class="mt-6 space-y-4" @submit.prevent="handleSubmit">
                <div>
                    <label class="text-xs uppercase tracking-widest text-slate-400">Nom complet</label>
                    <input
                        v-model="form.name"
                        type="text"
                        class="mt-1 w-full rounded-2xl border border-white/20 bg-white/10 px-4 py-2.5 text-sm text-white focus:border-white focus:outline-none"
                        required
                    />
                </div>

                <div>
                    <label class="text-xs uppercase tracking-widest text-slate-400">Email</label>
                    <input
                        v-model="form.email"
                        type="email"
                        class="mt-1 w-full rounded-2xl border border-white/20 bg-white/10 px-4 py-2.5 text-sm text-white focus:border-white focus:outline-none"
                        required
                    />
                </div>

                <div>
                    <label class="text-xs uppercase tracking-widest text-slate-400">Adresse</label>
                    <input
                        v-model="form.address"
                        type="text"
                        class="mt-1 w-full rounded-2xl border border-white/20 bg-white/10 px-4 py-2.5 text-sm text-white focus:border-white focus:outline-none"
                        required
                    />
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="text-xs uppercase tracking-widest text-slate-400">Mot de passe</label>
                        <input
                            v-model="form.password"
                            type="password"
                            class="mt-1 w-full rounded-2xl border border-white/20 bg-white/10 px-4 py-2.5 text-sm text-white focus:border-white focus:outline-none"
                            required
                        />
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-widest text-slate-400">Confirmation</label>
                        <input
                            v-model="form.password_confirmation"
                            type="password"
                            class="mt-1 w-full rounded-2xl border border-white/20 bg-white/10 px-4 py-2.5 text-sm text-white focus:border-white focus:outline-none"
                            required
                        />
                    </div>
                </div>

                <div>
                    <label class="text-xs uppercase tracking-widest text-slate-400">Type de compte</label>
                    <select
                        v-model="form.account_type"
                        class="mt-1 w-full rounded-2xl border border-white/20 bg-white/10 px-4 py-2.5 text-sm text-white focus:border-white focus:outline-none"
                    >
                        <option value="pro">Professionnel</option>
                        <option value="private">Particulier</option>
                    </select>
                </div>

                <p v-if="formMessage" :class="['text-sm rounded-2xl px-4 py-2', formError ? 'bg-rose-500/10 border border-rose-500/30 text-rose-200' : 'bg-emerald-500/10 border border-emerald-500/30 text-emerald-200']">
                    {{ formMessage }}
                </p>

                <button
                    type="submit"
                    class="w-full rounded-2xl bg-white/90 px-4 py-3 text-center text-sm font-semibold text-brand-900 transition hover:bg-white"
                    :disabled="submitting"
                >
                    <span v-if="!submitting">Créer l’accès</span>
                    <span v-else>Création...</span>
                </button>
            </form>
        </div>
    </section>
</template>

<script setup>
import axios from 'axios';
import { onMounted, reactive, ref } from 'vue';

const admins = ref([]);
const loading = ref(false);
const submitting = ref(false);
const formMessage = ref('');
const formError = ref(false);
const form = reactive({
    name: '',
    email: '',
    address: '',
    account_type: 'pro',
    password: '',
    password_confirmation: '',
});

const fetchAdmins = async () => {
    loading.value = true;

    try {
        const { data } = await axios.get('/api/admin/admins');
        admins.value = data.data;
    } finally {
        loading.value = false;
    }
};

const handleSubmit = async () => {
    if (submitting.value) {
        return;
    }

    submitting.value = true;
    formMessage.value = '';

    try {
        await axios.post('/api/admin/admins', form);
        formMessage.value = 'Administrateur créé avec succès.';
        formError.value = false;
        Object.assign(form, {
            name: '',
            email: '',
            address: '',
            account_type: 'pro',
            password: '',
            password_confirmation: '',
        });
        await fetchAdmins();
    } catch (error) {
        formError.value = true;
        formMessage.value = error.response?.data?.message ?? 'Erreur lors de la création.';
    } finally {
        submitting.value = false;
    }
};

const formatDate = (value) => {
    if (!value) {
        return '—';
    }

    return new Intl.DateTimeFormat('fr-FR', { dateStyle: 'long' }).format(new Date(value));
};

onMounted(fetchAdmins);
</script>
