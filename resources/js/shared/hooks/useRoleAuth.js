import { useContext } from "react";
import { AuthContext as CrmAuthContext } from "../../crm/context/AuthContext";
import { AuthContext as AdminAuthContext } from "../../admin/context/AuthContext";

export function useRoleAuth() {
    const auth = useContext(CrmAuthContext) || useContext(AdminAuthContext);

    if (!auth) {
        throw new Error("useRoleAuth must be used within AuthProvider");
    }

    const roleNames = auth.user?.role_names || [];

    return {
        user: auth.user,
        loading: auth.loading,
        isCollaborator: roleNames.includes("collaborator"),
        isPackaging: auth.user?.is_packaging ?? roleNames.includes("packaging"),
        isAccounting: auth.user?.is_accounting ?? roleNames.includes("accounting"),
        isStaff: auth.user?.is_staff ?? (roleNames.includes("admin") || roleNames.includes("staff")),
        isAdmin: roleNames.includes("admin"),
        affiliate: auth.user?.affiliate || null,
    };
}
