import api from '../../api/axios';

const BASE_URL = '/api/weekly-menu-builds';

export const weeklyMenuBuildApi = {
    getAll: () => api.get(BASE_URL),
    getById: (id) => api.get(`${BASE_URL}/${id}`),
    create: (data) => api.post(BASE_URL, data),
    update: (id, data) => api.put(`${BASE_URL}/${id}`, data),
    delete: (id) => api.delete(`${BASE_URL}/${id}`),
    archive: (id) => api.post(`${BASE_URL}/${id}/archive`),
    publish: (id) => api.post(`${BASE_URL}/${id}/publish`),
};
