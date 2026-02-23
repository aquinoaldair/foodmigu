import api from '../../api/axios';

const getBaseUrl = (diningHallId) => `/api/dining-halls/${diningHallId}/diners`;

export const dinerApi = {
    getAll: (diningHallId) => api.get(getBaseUrl(diningHallId)),
    create: (diningHallId, data) => api.post(getBaseUrl(diningHallId), data),
    update: (diningHallId, dinerId, data) => api.put(`${getBaseUrl(diningHallId)}/${dinerId}`, data),
    delete: (diningHallId, dinerId) => api.delete(`${getBaseUrl(diningHallId)}/${dinerId}`),
    import: (diningHallId, formData) => api.post(`/api/dining-halls/${diningHallId}/diners/import`, formData),
};
