import React, { useEffect, useState } from "react";
import CrmHeader from "../components/header/CrmHeader";
import { useAuth } from "../context/AuthContext";
import GroupedSidebarNav from "../../shared/components/GroupedSidebarNav";
import { CRM_NAV } from "../../shared/lib/navigation";
import { readSidebarCollapsed, toggleSidebarState } from "../../shared/components/SidebarToggleButton";
import { useTheme } from "../../shared/context/ThemeContext";

const SIDEBAR_STORAGE_KEY = "crm-sidebar-collapsed";

export default function CrmLayout({ children }) {
    const { user, loading, isCollaborator, isPackaging, isAccounting, hasCrmRole } = useAuth();
    const { cssVars } = useTheme();
    const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
    const [sidebarCollapsed, setSidebarCollapsed] = useState(() => readSidebarCollapsed(SIDEBAR_STORAGE_KEY));
    const showNavigation = hasCrmRole;

    const roleNames = Array.isArray(user?.role_names) ? user.role_names : [];

    const closeMobileMenu = () => setMobileMenuOpen(false);
    const toggleSidebar = () =>
        toggleSidebarState({
            mobileMenuOpen,
            setMobileMenuOpen,
            sidebarCollapsed,
            setSidebarCollapsed,
            storageKey: SIDEBAR_STORAGE_KEY,
        });

    useEffect(() => {
        const media = window.matchMedia("(min-width: 1024px)");
        const handleChange = () => {
            if (media.matches) {
                setMobileMenuOpen(false);
            }
        };
        media.addEventListener("change", handleChange);
        return () => media.removeEventListener("change", handleChange);
    }, []);

    const sidebarSubtitle = !hasCrmRole
        ? "Chưa gán quyền truy cập"
        : isPackaging
          ? "Đóng gói vận chuyển"
          : isAccounting
            ? "Kế toán"
            : isCollaborator
              ? "Cổng CTV bán hàng"
              : "Quản lý bán xe đạp";

    if (loading) {
        return <div className="crm-shell flex min-h-screen items-center justify-center text-slate-500">Đang tải CRM...</div>;
    }

    return (
        <div className="crm-shell flex h-dvh overflow-hidden" style={cssVars}>
            {showNavigation && (
                <aside
                    className={`crm-sidebar hidden shrink-0 overflow-hidden border-r transition-[width] duration-200 lg:block ${
                        sidebarCollapsed ? "lg:w-0 lg:border-r-0" : "lg:w-64"
                    }`}
                >
                    <div className={`h-full w-64 overflow-y-auto p-4 ${sidebarCollapsed ? "lg:invisible" : ""}`}>
                        <div className="mb-6">
                            <p className="text-lg font-bold">Himawari CRM</p>
                            <p className="text-xs opacity-60">{sidebarSubtitle}</p>
                        </div>
                        <GroupedSidebarNav menu={CRM_NAV} roleNames={roleNames} />
                    </div>
                </aside>
            )}

            {showNavigation && mobileMenuOpen && (
                <>
                    <button type="button" className="fixed inset-0 z-40 bg-slate-900/50 lg:hidden" onClick={closeMobileMenu} aria-label="Đóng menu" />
                    <aside className="crm-sidebar fixed inset-y-0 left-0 z-50 w-72 overflow-y-auto border-r p-4 shadow-xl lg:hidden">
                        <div className="mb-6 flex items-center justify-between">
                            <div>
                                <p className="text-lg font-bold">Himawari CRM</p>
                                <p className="text-xs opacity-60">{sidebarSubtitle}</p>
                            </div>
                            <button type="button" onClick={closeMobileMenu} className="sidebar-nav__link rounded-lg p-2" aria-label="Đóng menu">
                                ✕
                            </button>
                        </div>
                        <GroupedSidebarNav menu={CRM_NAV} roleNames={roleNames} onNavigate={closeMobileMenu} />
                    </aside>
                </>
            )}

            <div className="flex min-h-0 flex-1 flex-col overflow-hidden">
                <CrmHeader
                    onToggleSidebar={toggleSidebar}
                    mobileMenuOpen={mobileMenuOpen}
                    sidebarCollapsed={sidebarCollapsed}
                    showNavigation={showNavigation}
                />
                <main className="app-main p-4 md:p-6">{children}</main>
            </div>
        </div>
    );
}
