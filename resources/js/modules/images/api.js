import api from '../../api/axios';

const BASE_URL = '/api/images';

export const imageApi = {
    getAll: (params = {}) => api.get(BASE_URL, { params }),
    upload: (file) => {
        const formData = new FormData();
        formData.append('image', file);
        return api.post(BASE_URL, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });
    },
    delete: (id) => api.delete(`${BASE_URL}/${id}`),
};
