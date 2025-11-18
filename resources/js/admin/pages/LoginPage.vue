<template>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-950 via-slate-900 to-brand-900 px-4">
        <div class="w-full max-w-md rounded-3xl border border-white/10 bg-slate-900/80 p-8 shadow-2xl backdrop-blur">
            <div class="mb-8 text-center">
                <p class="text-sm uppercase tracking-[0.4em] text-brand-100/70">ProConnect</p>
                <h1 class="mt-2 text-3xl font-bold text-white">Espace Administrateur</h1>
                <p class="mt-1 text-sm text-slate-300">Connectez-vous pour superviser l’activité globale.</p>
            </div>

            <form class="space-y-5" @submit.prevent="handleSubmit">
                <div>
                    <label class="text-sm font-medium text-slate-200">Email</label>
                    <input
                        v-model="form.email"
                        type="email"
                        class="mt-2 w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white focus:border-brand-500 focus:outline-none"
                        placeholder="admin@proconnect.test"
                        required
                    />
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-200">Mot de passe</label>
                    <input
                        v-model="form.password"
                        type="password"
                        class="mt-2 w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white focus:border-brand-500 focus:outline-none"
                        placeholder="••••••••"
                        required
                    />
                </div>

                <p v-if="errorMessage" class="text-sm text-rose-300 bg-rose-500/10 border border-rose-500/30 px-4 py-2 rounded-2xl">
                    {{ errorMessage }}
                </p>

                <button
                    type="submit"
                    class="w-full rounded-2xl bg-gradient-to-r from-brand-500 to-brand-700 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-brand-500/30 transition hover:scale-[1.01]"
                    :disabled="isSubmitting"
                >
                    <span v-if="!isSubmitting">Se connecter</span>
                    <span v-else class="inline-flex items-center gap-2">Connexion en cours <span class="animate-spin">⏳</span></span>
                </button>
            </form>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const auth = useAuthStore();
const form = reactive({
    email: '',
    password: '',
});
const isSubmitting = ref(false);
const errorMessage = ref('');

const handleSubmit = async () => {
    if (isSubmitting.value) {
        return;
    }

    isSubmitting.value = true;
    errorMessage.value = '';

    try {
        await auth.login(form);
        router.push({ name: 'admin.dashboard' });
    } catch (error) {
        errorMessage.value = error.response?.data?.message ?? 'Identifiants invalides.';
    } finally {
        isSubmitting.value = false;
    }
};
</script>
