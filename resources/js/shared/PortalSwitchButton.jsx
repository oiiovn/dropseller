import React from "react";

export default function PortalSwitchButton({ mode = "crm" }) {
    const target = mode === "crm" ? "/admin" : "/crm";
    const label = mode === "crm" ? "Quản trị hệ thống" : "Về CRM";

    return (
        <a
            href={target}
            className={
                mode === "crm"
                    ? "inline-flex items-center rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700 transition hover:bg-blue-100"
                    : "inline-flex items-center rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700 transition hover:bg-blue-100"
            }
        >
            {label}
        </a>
    );
}
