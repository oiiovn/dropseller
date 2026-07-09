export const COMMISSION_WALLET_STATUS_LABELS = {
    pending_record: "Chờ ghi nhận",
    credited: "Đã ghi ví",
    pending_settlement: "Chờ quyết toán",
    settled: "Đã quyết toán",
    cancelled: "Đã huỷ",
};

export const commissionWalletStatusLabel = (status) => COMMISSION_WALLET_STATUS_LABELS[status] || status;

export const commissionWalletStatusBadgeClass = (status) => {
    const classes = {
        pending_record: "bg-amber-50 text-amber-700",
        credited: "bg-blue-50 text-blue-700",
        pending_settlement: "bg-blue-50 text-blue-700",
        settled: "bg-emerald-50 text-emerald-700",
        cancelled: "bg-slate-100 text-slate-500",
    };

    return classes[status] || "bg-slate-100 text-slate-700";
};

export const SETTLEMENT_STATUS_LABELS = {
    pending: "Chờ quyết toán",
    paid: "Đã quyết toán",
};

export const settlementStatusLabel = (status) => SETTLEMENT_STATUS_LABELS[status] || status;
