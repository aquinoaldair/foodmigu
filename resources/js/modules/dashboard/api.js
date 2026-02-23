import api from '../../api/axios';

export const dashboardApi = {
    weeks: () => api.get('/api/dashboard/weeks'),
    week: (id, diningHallId) =>
        api.get(`/api/dashboard/week/${id}`, { params: { dining_hall_id: diningHallId } }),
    day: (dayId, diningHallId) =>
        api.get(`/api/dashboard/day/${dayId}`, { params: { dining_hall_id: diningHallId } }),
};
