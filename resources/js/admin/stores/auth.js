import axios from 'axios';
import { reactive, computed } from 'vue';

const state = reactive({
    token: localStorage.getItem('admin_token'),
    admin: null,
    loadingProfile: false,
});

const setAxiosAuthorization = (token) => {
    if (token) {
        axios.defaults.headers.common.Authorization = `Bearer ${token}`;
    } else {
        delete axios.defaults.headers.common.Authorization;
    }
};

if (state.token) {
    setAxiosAuthorization(state.token);
}

export function useAuthStore() {
    const isAuthenticated = computed(() => Boolean(state.token));

    const login = async (credentials) => {
        const { data } = await axios.post('/api/admin/login', credentials);
        state.token = data.token;
        state.admin = data.admin;
        localStorage.setItem('admin_token', data.token);
        setAxiosAuthorization(data.token);

        return data.admin;
    };

    const logout = async () => {
        if (state.token) {
            await axios.post('/api/admin/logout').catch(() => null);
        }

        state.token = null;
        state.admin = null;
        localStorage.removeItem('admin_token');
        setAxiosAuthorization(null);
    };

    const fetchProfile = async () => {
        if (! state.token) {
            return null;
        }

        state.loadingProfile = true;

        try {
            const { data } = await axios.get('/api/admin/me');
            state.admin = data.admin;

            return data.admin;
        } finally {
            state.loadingProfile = false;
        }
    };

    return {
        state,
        isAuthenticated,
        login,
        logout,
        fetchProfile,
    };
}
