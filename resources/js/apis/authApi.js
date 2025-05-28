import api from "./configApi";

export const login = async (email, password) => {
    try {
        const response = await api.post('/login', { email, password });
        const token = response.data.token;

        if (token) {
            localStorage.setItem('authToken', token);
            api.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        }

        return response;
    } catch (error) {
        throw error;
    }
}