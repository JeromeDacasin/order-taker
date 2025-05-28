import api from "./configApi";

export const fetchSkus = async () => {
    const response = await api.get('/skus');
    return response;
}

export const createSku = async (request) => {
    const response = await api.post('/skus', request);
    return response;
}

export const fetchSpecificSku = async (id) => {
    const response = await api.get('/skus', id);
    return response;
}

export const updateSpecificSku = async (id, request) => {
    const payload = {
        name: request.name,
        code: request.code,
        unit_price: request.unit_price,
        is_active: request.is_active
    }
    const response = await api.put(`/skus/${id}`, payload);
    return response;
}

export const deleteSpecificSku = async (id) => {
    const response = await api.delete(`/skus/${id}`);
    return response;
}