import api from '../../api/axios';

const STORAGE_KEY = 'foodmigu_public_diner';

export function getStoredDiner(code) {
    try {
        const raw = localStorage.getItem(`${STORAGE_KEY}_${code}`);
        return raw ? JSON.parse(raw) : null;
    } catch {
        return null;
    }
}

export function setStoredDiner(code, diner) {
    localStorage.setItem(`${STORAGE_KEY}_${code}`, JSON.stringify(diner));
}

export function clearStoredDiner(code) {
    localStorage.removeItem(`${STORAGE_KEY}_${code}`);
}

export const publicMenuApi = {
    hall: (code) => api.get(`/api/public/${code}`),
    identify: (code, data) => api.post(`/api/public/${code}/identify`, data),
    menus: (code) => api.get(`/api/public/${code}/menus`),
    dayDetail: (dayId) => api.get(`/api/public/day/${dayId}`),
    select: (dayId, data) => api.post(`/api/public/day/${dayId}/select`, data),
    mySelections: (dayId, dinerId) =>
        api.get(`/api/public/day/${dayId}/selections`, { params: { diner_id: dinerId } }),
};
