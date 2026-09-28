import React, {
  createContext,
  useState,
  useContext,
  useEffect,
} from "react";

import api from "../api/axios";

const AuthContext = createContext(null);

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null);

  const [token, setToken] = useState(
    localStorage.getItem("token")
  );

  const [loading, setLoading] = useState(true);

  // =========================
  // GET USER ROLE
  // =========================
  const getUserRole = (userData) => {
    if (!userData) {
      return null;
    }

    /*
     * Backend returns:
     *
     * roles: [
     *   {
     *     id: 1,
     *     name: "admin"
     *   }
     * ]
     */

    if (
      Array.isArray(userData.roles) &&
      userData.roles.length > 0
    ) {
      return userData.roles[0]?.name || null;
    }

    /*
     * Fallback in case backend later
     * returns a single role field.
     */

    return userData.role?.name || userData.role || null;
  };

  // =========================
  // CHECK SAVED LOGIN
  // =========================
  useEffect(() => {
    try {
      const storedToken =
        localStorage.getItem("token");

      const storedUser =
        localStorage.getItem("user");

      if (storedToken) {
        setToken(storedToken);
      }

      if (storedUser) {
        setUser(JSON.parse(storedUser));
      }
    } catch (error) {
      console.error(
        "Error loading authentication:",
        error
      );

      localStorage.removeItem("token");
      localStorage.removeItem("user");

      setToken(null);
      setUser(null);
    } finally {
      setLoading(false);
    }
  }, []);

  // =========================
  // LOGIN
  // =========================
  const login = async (email, password) => {
    try {
      const response = await api.post(
        "/v1/auth/login",
        {
          email,
          password,
        }
      );

      console.log(
        "LOGIN RESPONSE:",
        response.data
      );

      const responseData =
        response.data?.data ||
        response.data;

      const newToken =
        responseData?.token ||
        responseData?.access_token;

      const userData =
        responseData?.user ||
        responseData?.data?.user;

      // =========================
      // CHECK TOKEN
      // =========================
      if (!newToken) {
        console.error(
          "Login response does not contain a token:",
          response.data
        );

        throw new Error(
          "Authentication token was not returned by the server."
        );
      }

      // =========================
      // SAVE TOKEN
      // =========================
      localStorage.setItem(
        "token",
        newToken
      );

      setToken(newToken);

      // =========================
      // SAVE USER
      // =========================
      if (userData) {
        localStorage.setItem(
          "user",
          JSON.stringify(userData)
        );

        setUser(userData);

        console.log(
          "USER ROLE:",
          getUserRole(userData)
        );
      } else {
        localStorage.removeItem("user");
        setUser(null);
      }

      console.log(
        "LOGIN SUCCESSFUL"
      );

      return userData;
    } catch (error) {
      console.error(
        "LOGIN FAILED:",
        error
      );

      throw error;
    }
  };

  // =========================
  // LOGOUT
  // =========================
  const logout = async () => {
    try {
      if (token) {
        await api.post(
          "/v1/auth/logout"
        );
      }
    } catch (error) {
      console.error(
        "Logout API error:",
        error
      );
    } finally {
      localStorage.removeItem("token");
      localStorage.removeItem("user");

      setToken(null);
      setUser(null);
    }
  };

  // =========================
  // ROLE
  // =========================
  const userRole = getUserRole(user);

  // =========================
  // AUTH VALUE
  // =========================
  const value = {
    user,
    token,
    login,
    logout,

    userRole,

    isAdmin:
      userRole === "admin",

    isSupervisor:
      userRole === "supervisor",

    isHrOfficer:
      userRole === "hr_officer",

    isStudent:
      userRole === "student",

    isAuthenticated:
      Boolean(token),

    loading,
  };

  return (
    <AuthContext.Provider value={value}>
      {children}
    </AuthContext.Provider>
  );
};

// =========================
// USE AUTH HOOK
// =========================
export const useAuth = () => {
  const context =
    useContext(AuthContext);

  if (!context) {
    throw new Error(
      "useAuth must be used within AuthProvider"
    );
  }

  return context;
};