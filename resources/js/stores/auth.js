import { defineStore } from 'pinia';
import api from '../api/axios';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        authenticated: false,
        userFetchAttempted: false,
    }),
    getters: {
        userName: (state) => state.user?.name ?? '',
        userRole: (state) => state.user?.roles?.[0]?.name ?? 'Usuario',
    },
    actions: {
        setUser(user) {
            this.user = user;
            this.authenticated = !!user;
            this.userFetchAttempted = true;
        },

        async login(email, password, remember = false) {
            await api.get('/sanctum/csrf-cookie');
            const { data } = await api.post('/login', { email, password, remember });
            this.setUser(data.user);
            return data;
        },

        async logout() {
            await api.post('/logout');
            this.user = null;
            this.authenticated = false;
            this.userFetchAttempted = true; // Ya no hay sesión
        },

        async fetchUser() {
            if (this.authenticated && this.user) {
                return this.user;
            }
            try {
                await api.get('/sanctum/csrf-cookie');
                const { data } = await api.get('/api/user');
                this.setUser(data);
                return data;
            } catch {
                this.user = null;
                this.authenticated = false;
                this.userFetchAttempted = true;
                throw new Error('Unauthenticated');
            }
        },
    },
});
