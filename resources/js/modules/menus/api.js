import api from '../../api/axios';

const BASE_URL = '/api/menus';

export const menuApi = {
    getAll: () => api.get(BASE_URL),
    getById: (id) => api.get(`${BASE_URL}/${id}`),
    create: (data) => api.post(BASE_URL, data),
    update: (id, data) => api.put(`${BASE_URL}/${id}`, data),
    delete: (id) => api.delete(`${BASE_URL}/${id}`),
};
