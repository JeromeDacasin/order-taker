import api from "./configApi";

export const fetchPurchaseOrders = async (page) => {
    console.log(page)
    const response = await api.get('/orders', {
        params: {
            page
        }
    });
    return response;
}

export const createPurchaseOrder = async (request) => {
    const response = await api.post('/orders', request);
    return response;
}

export const fetchSpecificPurchaseOrder = async (id) => {
    const response = await api.get(`/orders/${id}`);
    return response;
}

export const updateSpecificPurchaseOrder = async (request) => {
    const response = await api.put('/orders', request);
    return response;
}

export const deleteSpecificPurchaseOrder = async (request) => {
    const response = await api.delete('/orders', request);
    return response;
}