export const PAYMENT_APPROVAL_STATUS_LABELS = {
    pending: "Chờ kế toán",
    approved: "Đã xác nhận",
    rejected: "Từ chối",
};

export const paymentApprovalStatusLabel = (status) => PAYMENT_APPROVAL_STATUS_LABELS[status] || status;

export const paymentApprovalStatusBadgeClass = (status) => {
    const classes = {
        pending: "order-badge order-badge-waiting",
        approved: "order-badge order-badge-success",
        rejected: "order-badge order-badge-danger",
    };

    return classes[status] || "order-badge order-badge-default";
};

export const PAYMENT_STATUS_LABELS = {
    unpaid: "Chưa thanh toán",
    partial: "Thanh toán một phần",
    paid: "Đã thanh toán",
    refunded: "Đã hoàn tiền",
    received: "Đã thu",
    pending: "Đang chờ",
    failed: "Thất bại",
};

export const paymentStatusLabel = (status) => PAYMENT_STATUS_LABELS[status] || status;

export const PAYMENT_TYPE_LABELS = {
    deposit: "Cọc",
    partial: "Thanh toán",
    full: "Tất toán",
};

export const PAYMENT_METHOD_LABELS = {
    bank_transfer: "Chuyển khoản",
    cash: "Tiền mặt",
    card: "Thẻ",
    installment: "Trả góp",
    other: "Khác",
};

export const paymentTypeLabel = (type) => PAYMENT_TYPE_LABELS[type] || type;

export const paymentMethodLabel = (method) => PAYMENT_METHOD_LABELS[method] || method;

export const SHIPPING_MODE_LABELS = {
    boxed: "Đóng thùng",
    direct_ship: "Ship trực tiếp",
};

export const shippingModeLabel = (mode) => SHIPPING_MODE_LABELS[mode] || mode;

export const shippingModeBadgeClass = (mode) => {
    const classes = {
        boxed: "bg-amber-50 text-amber-700",
        direct_ship: "bg-sky-50 text-sky-700",
    };

    return classes[mode] || "bg-slate-100 text-slate-700";
};

export function formatDeliveryDateRange(order) {
    const from = order?.delivery_date_from || order?.delivery_date;
    const to = order?.delivery_date_to;
    if (!from && !to) return null;
    const format = (value) => (value ? String(value).slice(0, 10) : null);
    const fromText = format(from);
    const toText = format(to);
    if (fromText && toText && fromText !== toText) {
        return `${fromText} → ${toText}`;
    }
    return fromText || toText;
}

export const ORDER_STATUS_FLOW = [
    "consulting",
    "confirmed",
    "deposit_pending",
    "deposit_paid",
    "waiting_delivery",
    "packaged",
    "delivering",
    "delivered",
    "completed",
    "cancelled",
    "refunded",
];

export const sortOrderStatuses = (statuses) =>
    [...statuses].sort(
        (a, b) =>
            (ORDER_STATUS_FLOW.indexOf(a) === -1 ? 99 : ORDER_STATUS_FLOW.indexOf(a)) -
            (ORDER_STATUS_FLOW.indexOf(b) === -1 ? 99 : ORDER_STATUS_FLOW.indexOf(b))
    );

export const ORDER_STATUS_LABELS = {
    new: "Mới",
    consulting: "Đang tư vấn",
    confirmed: "Đã chốt",
    deposit_pending: "Chờ cọc",
    deposit_paid: "Đã cọc",
    waiting_delivery: "Chờ đóng gói",
    packaged: "Đã đóng gói",
    delivering: "Đang giao",
    delivered: "Đã giao",
    completed: "Hoàn thành",
    cancelled: "Đã hủy",
    refunded: "Hoàn tiền",
};

export const CONSULTATION_STATUS_LABELS = {
    new: "Mới",
    consulting: "Đang tư vấn",
    confirmed: "Đã chốt mua",
    cancelled: "Đã hủy",
};

export const orderStatusBadgeClass = (status) => {
    const classes = {
        new: "order-badge bg-slate-100 text-slate-700",
        consulting: "order-badge bg-sky-100 text-sky-700",
        confirmed: "order-badge bg-blue-100 text-blue-700",
        deposit_pending: "order-badge bg-amber-100 text-amber-800",
        deposit_paid: "order-badge bg-yellow-100 text-yellow-800",
        waiting_delivery: "order-badge bg-orange-100 text-orange-800",
        packaged: "order-badge bg-lime-100 text-lime-800",
        delivering: "order-badge bg-cyan-100 text-cyan-800",
        delivered: "order-badge bg-teal-100 text-teal-800",
        completed: "order-badge bg-emerald-100 text-emerald-800",
        cancelled: "order-badge bg-red-100 text-red-800",
        refunded: "order-badge bg-rose-100 text-rose-800",
    };

    return classes[status] || "order-badge bg-slate-100 text-slate-600";
};

export const orderStatusCrmBadgeClass = (status) => {
    const classes = {
        new: "crm-badge bg-slate-100 text-slate-700",
        consulting: "crm-badge bg-sky-100 text-sky-700",
        confirmed: "crm-badge bg-blue-100 text-blue-700",
        deposit_pending: "crm-badge bg-amber-100 text-amber-800",
        deposit_paid: "crm-badge bg-yellow-100 text-yellow-800",
        waiting_delivery: "crm-badge bg-orange-100 text-orange-800",
        packaged: "crm-badge bg-lime-100 text-lime-800",
        delivering: "crm-badge bg-cyan-100 text-cyan-800",
        delivered: "crm-badge bg-teal-100 text-teal-800",
        completed: "crm-badge bg-emerald-100 text-emerald-800",
        cancelled: "crm-badge bg-red-100 text-red-800",
        refunded: "crm-badge bg-rose-100 text-rose-800",
    };

    return classes[status] || "crm-badge bg-slate-100 text-slate-600";
};

export const orderStatusAdminBadgeClass = (status) => {
    const classes = {
        new: "admin-badge bg-slate-100 text-slate-700",
        consulting: "admin-badge bg-sky-100 text-sky-700",
        confirmed: "admin-badge bg-blue-100 text-blue-700",
        deposit_pending: "admin-badge bg-amber-100 text-amber-800",
        deposit_paid: "admin-badge bg-yellow-100 text-yellow-800",
        waiting_delivery: "admin-badge bg-orange-100 text-orange-800",
        packaged: "admin-badge bg-lime-100 text-lime-800",
        delivering: "admin-badge bg-cyan-100 text-cyan-800",
        delivered: "admin-badge bg-teal-100 text-teal-800",
        completed: "admin-badge bg-emerald-100 text-emerald-800",
        cancelled: "admin-badge bg-red-100 text-red-800",
        refunded: "admin-badge bg-rose-100 text-rose-800",
    };

    return classes[status] || "admin-badge bg-slate-100 text-slate-600";
};

export const paymentStatusBadgeClass = (status) => {
    const classes = {
        unpaid: "order-badge order-badge-unpaid",
        partial: "order-badge order-badge-waiting",
        paid: "order-badge order-badge-success",
        refunded: "order-badge order-badge-danger",
        received: "order-badge order-badge-success",
        pending: "order-badge order-badge-waiting",
        failed: "order-badge order-badge-danger",
    };

    return classes[status] || "order-badge order-badge-default";
};

export const statusBadgeClass = (status) => {
    if (["completed", "confirmed", "paid", "delivered"].includes(status)) {
        return "bg-emerald-50 text-emerald-700";
    }
    if (["cancelled", "refunded", "failed", "overdue"].includes(status)) {
        return "bg-red-50 text-red-700";
    }
    if (["consulting", "partial", "deposit_pending", "waiting_delivery", "packaged", "delivering"].includes(status)) {
        return "bg-amber-50 text-amber-700";
    }
    return "bg-slate-100 text-slate-700";
};
