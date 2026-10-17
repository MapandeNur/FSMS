import api from "./client";

export const login = async (credentials) => {
    const response = await api.post("/auth/login", credentials);

    const result = response.data.data;

    if (result?.token) {
        localStorage.setItem("token", result.token);
    }

    if (result?.user) {
        localStorage.setItem("user", JSON.stringify(result.user));
    }

    return result;
};

export const logout = async () => {
    try {
        await api.post("/auth/logout");
    } finally {
        localStorage.removeItem("token");
        localStorage.removeItem("user");
    }
};

export const getCurrentUser = async () => {
    const response = await api.get("/auth/me");
    return response.data.data.user;
};

export default {
    login,
    logout,
    getCurrentUser,
};