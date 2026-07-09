export const ALL_CRM_ROLES = ["admin", "staff", "collaborator", "packaging", "accounting"];

export const CRM_NAV = [
    {
        type: "link",
        id: "dashboard",
        label: "Dashboard",
        to: "/",
        exact: true,
        roles: ALL_CRM_ROLES,
    },
    {
        type: "group",
        id: "sales",
        label: "Bán hàng",
        roles: ["admin", "staff", "collaborator", "packaging", "accounting"],
        children: [
            { id: "customers", label: "Khách hàng", to: "/customers", roles: ["admin", "staff", "collaborator"] },
            { id: "orders", label: "Đơn hàng", to: "/orders", roles: ["admin", "staff", "collaborator", "accounting", "packaging"] },
        ],
    },
    {
        type: "group",
        id: "finance",
        label: "Tài chính",
        roles: ["admin", "staff", "collaborator", "accounting"],
        children: [
            { id: "debts", label: "Công nợ", to: "/debts", roles: ["admin", "staff"] },
            { id: "payments", label: "Thanh toán", to: "/payments", roles: ["admin", "staff", "accounting"] },
            { id: "commissions", label: "Ví hoa hồng", to: "/commissions", roles: ["admin", "staff", "collaborator", "accounting"] },
        ],
    },
    // Tạm ẩn nhóm Báo cáo — bật lại khi cần
    // {
    //     type: "group",
    //     id: "reports",
    //     label: "Báo cáo",
    //     roles: ["admin", "staff"],
    //     children: [
    //         { id: "reports-overview", label: "Báo cáo tổng quan", to: "/reports", exact: true, roles: ["admin", "staff"] },
    //         { id: "reports-revenue", label: "Báo cáo doanh thu", to: "/reports/revenue", roles: ["admin", "staff"] },
    //         { id: "reports-affiliates", label: "Báo cáo CTV", to: "/reports/affiliates", roles: ["admin", "staff"] },
    //     ],
    // },
];

export const ADMIN_NAV = [
    {
        type: "link",
        id: "dashboard",
        label: "Dashboard",
        to: "/",
        exact: true,
        roles: ["admin"],
    },
    {
        type: "group",
        id: "sales",
        label: "Bán hàng",
        roles: ["admin"],
        children: [{ id: "orders", label: "Đơn hàng", to: "/orders", roles: ["admin"] }],
    },
    {
        type: "group",
        id: "finance",
        label: "Tài chính",
        roles: ["admin"],
        children: [
            { id: "payments", label: "Thanh toán", to: "/payments", roles: ["admin"] },
            { id: "commissions", label: "Ví hoa hồng", to: "/commissions", roles: ["admin"] },
        ],
    },
    // Tạm ẩn nhóm Báo cáo — bật lại khi cần
    // {
    //     type: "group",
    //     id: "reports",
    //     label: "Báo cáo",
    //     roles: ["admin"],
    //     children: [
    //         { id: "reports-overview", label: "Báo cáo tổng quan", to: "/reports", exact: true, roles: ["admin"] },
    //         { id: "reports-revenue", label: "Báo cáo doanh thu", to: "/reports/revenue", roles: ["admin"] },
    //         { id: "reports-affiliates", label: "Báo cáo CTV", to: "/reports/affiliates", roles: ["admin"] },
    //     ],
    // },
    {
        type: "group",
        id: "settings",
        label: "Cài đặt",
        roles: ["admin"],
        children: [
            { id: "affiliates", label: "Cộng tác viên", to: "/affiliates", roles: ["admin"] },
            { id: "pins", label: "Quản lý pin", to: "/pins", roles: ["admin"] },
            { id: "commission-rules", label: "Rule hoa hồng", to: "/commission-rules", roles: ["admin"] },
            { id: "users", label: "Tài khoản", to: "/users", roles: ["admin"] },
            { id: "appearance", label: "Giao diện", to: "/settings/appearance", roles: ["admin"] },
        ],
    },
];

export const PAGE_TITLES = {
    "/": "Dashboard",
    "/customers": "Khách hàng",
    "/customers/new": "Thêm khách hàng",
    "/orders": "Đơn hàng",
    "/orders/new": "Tạo đơn hàng",
    "/debts": "Công nợ",
    "/payments": "Thanh toán",
    "/commissions": "Ví hoa hồng",
    "/reports": "Báo cáo tổng quan",
    "/reports/revenue": "Báo cáo doanh thu",
    "/reports/affiliates": "Báo cáo CTV",
    "/affiliates": "Cộng tác viên",
    "/users": "Tài khoản",
    "/commission-rules": "Rule hoa hồng",
    "/pins": "Quản lý pin",
    "/settings/appearance": "Giao diện",
};

export function filterNavigation(menu, roleNames) {
    const hasRoleGate = Array.isArray(roleNames) && roleNames.length > 0;

    if (!hasRoleGate) {
        return [];
    }

    const canAccess = (roles) => roles.some((role) => roleNames.includes(role));

    return menu
        .map((entry) => {
            if (entry.type === "link") {
                return canAccess(entry.roles) ? entry : null;
            }

            if (!canAccess(entry.roles)) {
                return null;
            }

            const children = (entry.children || []).filter((child) => canAccess(child.roles));
            if (children.length === 0) {
                return null;
            }

            return { ...entry, children };
        })
        .filter(Boolean);
}

export function buildNavTarget(item) {
    if (item.tab) {
        return { pathname: item.to, search: `?tab=${item.tab}` };
    }

    return item.to;
}

export function isNavItemActive(item, pathname, searchParams) {
    const targetPath = item.to;
    const tab = item.tab || null;
    const currentTab = searchParams.get("tab") || "orders";

    if (tab) {
        return pathname === targetPath && currentTab === tab;
    }

    if (pathname === targetPath && targetPath === "/payments" && currentTab === "commissions") {
        return false;
    }

    if (item.exact) {
        return pathname === targetPath;
    }

    if (targetPath === "/") {
        return pathname === "/";
    }

    return pathname === targetPath || pathname.startsWith(`${targetPath}/`);
}

export function findActiveGroupIds(menu, pathname, searchParams) {
    return menu
        .filter((entry) => entry.type === "group")
        .filter((entry) => entry.children.some((child) => isNavItemActive(child, pathname, searchParams)))
        .map((entry) => entry.id);
}

export function getPageTitleFromPath(pathname, searchParams) {
    if (pathname === "/payments" && searchParams.get("tab") === "commissions") {
        return "Quyết toán CTV";
    }

    if (pathname.startsWith("/customers/") && pathname.endsWith("/edit")) {
        return "Cập nhật khách hàng";
    }

    if (pathname.startsWith("/orders/") && pathname !== "/orders/new") {
        return "Chi tiết đơn hàng";
    }

    if (pathname.startsWith("/commissions/")) {
        return "Chi tiết hoa hồng";
    }

    return PAGE_TITLES[pathname] || "Himawari";
}

const CRM_ROUTE_RULES = [
    {
        match: (pathname) => pathname === "/",
        roles: ALL_CRM_ROLES,
    },
    {
        match: (pathname) => pathname === "/customers" || pathname.startsWith("/customers/"),
        roles: ["admin", "staff", "collaborator"],
    },
    {
        match: (pathname) => pathname === "/orders" || pathname.startsWith("/orders/"),
        roles: ["admin", "staff", "collaborator", "accounting", "packaging"],
    },
    {
        match: (pathname) => pathname === "/debts" || pathname.startsWith("/debts/"),
        roles: ["admin", "staff"],
    },
    {
        match: (pathname) => pathname === "/payments" || pathname.startsWith("/payments/"),
        roles: ["admin", "staff", "accounting"],
    },
    {
        match: (pathname) => pathname === "/commissions" || pathname.startsWith("/commissions/"),
        roles: ["admin", "staff", "collaborator", "accounting"],
    },
    {
        match: (pathname) => pathname === "/reports" || pathname.startsWith("/reports/"),
        roles: ["admin", "staff"],
    },
];

export function canAccessCrmRoute(pathname, searchParams, roleNames) {
    if (!Array.isArray(roleNames) || roleNames.length === 0) {
        return false;
    }

    const rule = CRM_ROUTE_RULES.find((entry) => entry.match(pathname, searchParams));
    if (!rule) {
        return false;
    }

    return rule.roles.some((role) => roleNames.includes(role));
}
