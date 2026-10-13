import React, {
  createContext,
  useState,
  useContext,
  useEffect,
} from "react";

import api from "../api/client";

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

  const login = async (email, password) => {
    try {
      const response = await api.post(
        "/v1/auth/login",
        {
          email,
          password,
        }
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

      
      if (!newToken) {
        console.error(
          "Login response does not contain a token:",
          response.data
        );

        throw new Error(
          "Authentication token was not returned by the server."
        );
      }

      localStorage.setItem(
        "token",
        newToken
      );

      setToken(newToken);

   
      if (userData) {
        localStorage.setItem(
          "user",
          JSON.stringify(userData)
        );

        setUser(userData);

       
      } else {
        localStorage.removeItem("user");
        setUser(null);
      }


      return userData;
    } catch (error) {
      console.error(
        "LOGIN FAILED:",
        error
      );

      throw error;
    }
  };

  
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

 
  const userRole = getUserRole(user);

  
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
