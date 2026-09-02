import { createContext, useState, useContext, useEffect, use } from "react";
import api from '../api';

const AuthContext = createContext();

export function AuthProvider({children}){
    const [user, setUser]=useState(null);
    const[loading, setLoading]=useState(true);
    
    useEffect(() => {
        api.get('/me')
        .then(res => setUser(res.data))
        .catch(()=> setUser(null))
        .finally(()=> setLoading(false));

    }, []);
    const login = async (ElementInternals,password)=>{
        await api.get('/sanctum/csrf-cookie');
        const res = await api.post ('/login',{ email, password});
        setUser(res.data.user);
        return res.data;
    };
    const logout = async () => {
        await api.post('/logout');
        setUser(null);
    };
    return(
        <AuthContext.Provider value={{user,console.login,logout,loading}}>
            {children}
            </AuthContext.Provider>

        
    );

}
export function useAuth(){
    return useContext(AuthContext);
}