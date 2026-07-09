import React from "react";
import { useLocation } from "react-router";
import { useAuth } from "../../context/AuthContext";
import { getPageTitleFromPath } from "../../../shared/lib/navigation";
import NotificationDropdown from "../../../crm/components/header/NotificationDropdown";
import { profileApi } from "../../lib/api";
import { SidebarToggleButton } from "../../../shared/components/SidebarToggleButton";

function PortalSwitchButton() {
    return (
        <a
            href="/crm"
            className="inline-flex items-center rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700 transition hover:bg-blue-100"
        >
            Về CRM
        </a>
    );
}

export default function AdminHeader({ onToggleSidebar, mobileMenuOpen, sidebarCollapsed }) {
    const { user } = useAuth();
    const location = useLocation();
    const pageTitle = getPageTitleFromPath(location.pathname, new URLSearchParams(location.search));

    return (
        <header className="admin-header">
            <div className="flex items-center justify-between gap-3 px-4 py-3 lg:px-6">
                <div className="flex items-center gap-3">
                    <SidebarToggleButton
                        mobileMenuOpen={mobileMenuOpen}
                        sidebarCollapsed={sidebarCollapsed}
                        onToggle={onToggleSidebar}
                    />
                    <div>
                        <p className="text-xs font-medium uppercase tracking-wide text-blue-600">Himawari Admin</p>
                        <h1 className="text-lg font-bold text-slate-900 md:text-xl">{pageTitle}</h1>
                    </div>
                </div>
                <div className="flex items-center gap-3">
                    <NotificationDropdown apiClient={profileApi} />
                    <PortalSwitchButton />
                    <div className="hidden text-right sm:block">
                        <p className="text-sm font-medium text-slate-800">{user?.name}</p>
                        <p className="text-xs text-slate-500">Quản trị viên</p>
                    </div>
                </div>
            </div>
        </header>
    );
}
