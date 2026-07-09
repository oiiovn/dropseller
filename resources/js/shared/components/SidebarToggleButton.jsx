import React, { useEffect, useState } from "react";

function useIsDesktop(breakpoint = 1024) {
    const query = `(min-width: ${breakpoint}px)`;

    const [isDesktop, setIsDesktop] = useState(() => {
        if (typeof window === "undefined") {
            return false;
        }
        return window.matchMedia(query).matches;
    });

    useEffect(() => {
        const media = window.matchMedia(query);
        const handleChange = () => setIsDesktop(media.matches);
        handleChange();
        media.addEventListener("change", handleChange);
        return () => media.removeEventListener("change", handleChange);
    }, [query]);

    return isDesktop;
}

export function SidebarToggleButton({ mobileMenuOpen, sidebarCollapsed, onToggle, className = "" }) {
    const isDesktop = useIsDesktop();

    let ariaLabel = "Mở menu";
    if (isDesktop) {
        ariaLabel = sidebarCollapsed ? "Mở sidebar" : "Thu gọn sidebar";
    } else {
        ariaLabel = mobileMenuOpen ? "Đóng menu" : "Mở menu";
    }

    const showClose = !isDesktop && mobileMenuOpen;

    return (
        <button
            type="button"
            onClick={onToggle}
            className={`crm-sidebar-toggle ${className}`.trim()}
            aria-label={ariaLabel}
            aria-expanded={isDesktop ? !sidebarCollapsed : mobileMenuOpen}
        >
            {showClose ? (
                <svg className="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" aria-hidden="true">
                    <path d="M6 6l12 12M18 6L6 18" strokeLinecap="round" />
                </svg>
            ) : isDesktop && !sidebarCollapsed ? (
                <svg className="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" aria-hidden="true">
                    <path d="M15 18l-6-6 6-6" strokeLinecap="round" strokeLinejoin="round" />
                    <path d="M4 6v12" strokeLinecap="round" />
                </svg>
            ) : (
                <svg className="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" aria-hidden="true">
                    <path d="M4 7h16M4 12h16M4 17h10" strokeLinecap="round" />
                </svg>
            )}
        </button>
    );
}

export function readSidebarCollapsed(storageKey) {
    if (typeof window === "undefined") {
        return false;
    }

    return localStorage.getItem(storageKey) === "1";
}

export function persistSidebarCollapsed(storageKey, collapsed) {
    localStorage.setItem(storageKey, collapsed ? "1" : "0");
}

export function toggleSidebarState({ mobileMenuOpen, setMobileMenuOpen, sidebarCollapsed, setSidebarCollapsed, storageKey }) {
    if (window.matchMedia("(min-width: 1024px)").matches) {
        setSidebarCollapsed((current) => {
            const next = !current;
            persistSidebarCollapsed(storageKey, next);
            return next;
        });
        return;
    }

    setMobileMenuOpen(!mobileMenuOpen);
}
