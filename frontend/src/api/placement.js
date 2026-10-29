import api from "./client";

export async function getPlacements(params = {}) {
const response = await api.get("/placements", { params });
return response.data;
}

export async function getPlacement(id) {
const response = await api.get("/placements/${id}");
return response.data;
}

export async function createPlacement(data) {
const response = await api.post("/placements", data);
return response.data;
}

export async function updatePlacement(id, data) {
const response = await api.put("/placements/${id}", data);
return response.data;
}

export async function deletePlacement(id) {
const response = await api.delete("/placements/${id}");
return response.data;
}