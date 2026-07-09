import React, { useMemo } from "react";
import { useLocation } from "react-router";
import { useAuth } from "../../context/AuthContext";
import { canAccessCrmRoute, getPageTitleFromPath } from "../../../shared/lib/navigation";
import NotificationDropdown from "./NotificationDropdown";
import UserDropdown from "./UserDropdown";
import { SidebarToggleButton } from "../../../shared/components/SidebarToggleButton";

function PortalSwitchButton() {
    return (
        <a
            href="/admin"
            className="inline-flex items-center rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700 transition hover:bg-blue-100"
        >
            Quản trị hệ thống
        </a>
    );
}

export default function CrmHeader({ onToggleSidebar, mobileMenuOpen, sidebarCollapsed, showNavigation = true }) {
    const { user, isAdmin, hasCrmRole, roleNames } = useAuth();
    const location = useLocation();
    const searchParams = useMemo(() => new URLSearchParams(location.search), [location.search]);
    const routeAllowed = hasCrmRole && canAccessCrmRoute(location.pathname, searchParams, roleNames);

    const pageTitle = !hasCrmRole
        ? "Thông báo tài khoản"
        : !routeAllowed
          ? "Không có quyền truy cập"
          : getPageTitleFromPath(location.pathname, searchParams);

    return (
        <header className="crm-header">
            <div className="flex flex-col gap-3 px-4 py-3 lg:flex-row lg:items-center lg:justify-between lg:px-6">
                <div className="flex items-center gap-3">
                    {showNavigation && (
                        <SidebarToggleButton
                            mobileMenuOpen={mobileMenuOpen}
                            sidebarCollapsed={sidebarCollapsed}
                            onToggle={onToggleSidebar}
                        />
                    )}

                    <div>
                        <p className="text-xs font-medium uppercase tracking-wide text-blue-600">Himawari CRM</p>
                        <h1 className="text-lg font-bold text-slate-900 md:text-xl">{pageTitle}</h1>
                    </div>
                </div>

                {showNavigation && (
                    <div className="flex items-center justify-end gap-2 sm:gap-3">
                        {isAdmin && <PortalSwitchButton />}
                        <div className="hidden rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-600 md:block">
                            <span className="font-medium text-slate-800">円</span>
                            <span className="mx-2 text-slate-300">|</span>
                            <span>Bán xe đạp Nhật Bản</span>
                        </div>
                        <NotificationDropdown />
                        <UserDropdown user={user} />
                    </div>
                )}
            </div>
        </header>
    );
}
