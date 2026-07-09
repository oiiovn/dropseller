import React, { createContext, useContext, useEffect, useMemo, useState } from "react";
import { profileApi } from "../lib/api";

const AuthContext = createContext(null);

export { AuthContext };

export function AuthProvider({ children }) {
    const [user, setUser] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState("");

    useEffect(() => {
        profileApi
            .get("/me")
            .then((response) => {
                if (!response.data?.role_names?.includes("admin")) {
                    window.location.href = "/crm";
                    return;
                }
                setUser(response.data);
            })
            .catch(() => {
                setError("Không xác thực được quyền admin.");
                window.location.href = "/crm";
            })
            .finally(() => setLoading(false));
    }, []);

    const value = useMemo(
        () => ({
            user,
            loading,
            error,
            isAdmin: user?.role_names?.includes("admin"),
        }),
        [user, loading, error]
    );

    return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}

export function useAuth() {
    const context = useContext(AuthContext);
    if (!context) {
        throw new Error("useAuth must be used within AuthProvider");
    }
    return context;
}
