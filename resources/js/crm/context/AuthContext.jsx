import React, { createContext, useContext, useEffect, useMemo, useState } from "react";
import { api } from "../lib/api";

const AuthContext = createContext(null);

export { AuthContext };

export function AuthProvider({ children }) {
    const [user, setUser] = useState(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        api.get("/me")
            .then((response) => setUser(response.data))
            .catch(() => setUser(null))
            .finally(() => setLoading(false));
    }, []);

    const value = useMemo(() => {
        const roleNames = user?.role_names || [];
        const hasCrmRole = roleNames.length > 0;
        return {
            user,
            loading,
            hasCrmRole,
            roleNames,
            isCollaborator: roleNames.includes("collaborator"),
            isPackaging: user?.is_packaging ?? roleNames.includes("packaging"),
            isAccounting: user?.is_accounting ?? roleNames.includes("accounting"),
            isStaff: user?.is_staff ?? (roleNames.includes("admin") || roleNames.includes("staff")),
            isAdmin: roleNames.includes("admin"),
            affiliate: user?.affiliate || null,
        };
    }, [user, loading]);

    return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}

export function useAuth() {
    const context = useContext(AuthContext);
    if (!context) {
        throw new Error("useAuth must be used within AuthProvider");
    }
    return context;
}
