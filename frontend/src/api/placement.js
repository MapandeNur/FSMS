import api from "./client";

export const getPlacements = async (params = {}) => {
    const response = await api.get("/placements", { params });
    return response.data;
};

export const getPlacement = async (id) => {
    const response = await api.get(`/placements/${id}`);
    return response.data;
};

export const createPlacement = async (placementData) => {
    const response = await api.post(
        "/placements",
        placementData
    );

    return response.data;
};

export const updatePlacement = async (id, placementData) => {
    const response = await api.put(
        `/placements/${id}`,
        placementData
    );

    return response.data;
};

export const deletePlacement = async (id) => {
    const response = await api.delete(`/placements/${id}`);
    return response.data;
};