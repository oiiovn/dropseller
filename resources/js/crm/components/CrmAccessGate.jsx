import React, { useMemo } from "react";
import { useLocation } from "react-router";
import { useAuth } from "../context/AuthContext";
import { canAccessCrmRoute } from "../../shared/lib/navigation";
import NoCrmRolePage from "../pages/NoCrmRolePage";
import NoPermissionPage from "../pages/NoPermissionPage";

export default function CrmAccessGate({ children }) {
    const { loading, hasCrmRole, roleNames } = useAuth();
    const location = useLocation();
    const searchParams = useMemo(() => new URLSearchParams(location.search), [location.search]);

    if (loading) {
        return null;
    }

    if (!hasCrmRole) {
        return <NoCrmRolePage />;
    }

    if (!canAccessCrmRoute(location.pathname, searchParams, roleNames)) {
        return <NoPermissionPage />;
    }

    return children;
}
