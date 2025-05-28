    import api from "./configApi";

export const fetchCustomers = async (search) => {
    console.log(search)
    const response = await api.get('/customers', {
        params: {
            search
        },
    });
    return response;
}

export const createCustomer = async (request) => {
    const response = await api.post('/customers', request);
    return response;
}

export const fetchSpecificCustomer = async (id) => {
    const response = await api.get('/customers', id);
    return response;
}

export const updateSpecificCustomer = async (id, request) => {
    const payload = {
        first_name: request.first_name,
        last_name: request.last_name,
        mobile_number: request.mobile_number,
        city: request.city,
    }
    const response = await api.put(`/customers/${id}`, payload);
    return response;
}

export const deleteSpecificCustomer = async (id) => {
    const response = await api.delete(`/customers/${id}`);
    return response;
}