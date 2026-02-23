import api from '../../api/axios';

const BASE = '/api/profile';

export const profileApi = {
    get: () => api.get(BASE),
    update: (data) => api.put(`${BASE}/update`, data),
    updatePassword: (data) => api.put(`${BASE}/password`, data),
    logout: () => api.post(`${BASE}/logout`),
};
