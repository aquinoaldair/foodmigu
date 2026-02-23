import axios from 'axios';

const api = axios.create({
    baseURL: '',
    withCredentials: true,
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        Accept: 'application/json',
        'Content-Type': 'application/json',
    },
    xsrfCookieName: 'XSRF-TOKEN',
    xsrfHeaderName: 'X-XSRF-TOKEN',
});

export const setupAxiosInterceptors = (router) => {
    api.interceptors.response.use(
        (response) => response,
        (error) => {
            if (
                error.response?.status === 401 &&
                router.currentRoute.value.name !== 'login' &&
                !router.currentRoute.value.meta?.public
            ) {
                router.push({ name: 'login' });
            }
            return Promise.reject(error);
        }
    );
};

export default api;
