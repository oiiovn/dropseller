import React, { useEffect, useState } from "react";
import { Link, useParams } from "react-router";
import { api, formatJpy } from "../../lib/api";
import { commissionWalletStatusBadgeClass, commissionWalletStatusLabel } from "../../lib/commissionStatuses";
import { useAuth } from "../../context/AuthContext";

export default function CommissionDetailPage() {
    const { id } = useParams();
    const { isStaff } = useAuth();
    const [commission, setCommission] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState("");

    useEffect(() => {
        api.get(`/commissions/${id}`)
            .then((response) => setCommission(response.data))
            .catch(() => setError("Không tải được hoa hồng."))
            .finally(() => setLoading(false));
    }, [id]);

    if (loading) {
        return <p className="text-sm text-slate-500">Đang tải...</p>;
    }

    if (!commission) {
        return <p className="text-sm text-red-600">{error || "Không tìm thấy hoa hồng."}</p>;
    }

    return (
        <section className="crm-card max-w-3xl">
            <div className="mb-4 flex items-center justify-between">
                <div>
                    <Link to="/commissions" className="text-sm text-blue-600 hover:underline">
                        ← Ví hoa hồng
                    </Link>
                    <h2 className="mt-1 text-xl font-bold">Hoa hồng #{commission.id}</h2>
                </div>
                {!isStaff && <span className="crm-badge bg-blue-50 text-blue-700">Chế độ xem CTV</span>}
            </div>

            <div className="grid gap-4 md:grid-cols-2">
                <div>
                    <p className="text-xs uppercase text-slate-400">CTV</p>
                    <p className="font-medium">{commission.affiliate?.full_name}</p>
                </div>
                <div>
                    <p className="text-xs uppercase text-slate-400">Đơn hàng</p>
                    <p className="font-medium">{commission.order?.order_code}</p>
                </div>
                <div>
                    <p className="text-xs uppercase text-slate-400">Rule áp dụng</p>
                    <p className="font-medium">{commission.applied_rule_name}</p>
                </div>
                <div>
                    <p className="text-xs uppercase text-slate-400">Tỷ lệ</p>
                    <p className="font-medium">{commission.commission_rate}%</p>
                </div>
                <div>
                    <p className="text-xs uppercase text-slate-400">Số tiền hoa hồng</p>
                    <p className="text-lg font-bold text-blue-600">{formatJpy(commission.commission_amount_jpy)}</p>
                </div>
                <div>
                    <p className="text-xs uppercase text-slate-400">Trạng thái ví</p>
                    <span className={`crm-badge ${commissionWalletStatusBadgeClass(commission.wallet_status)}`}>
                        {commissionWalletStatusLabel(commission.wallet_status)}
                    </span>
                </div>
                <div>
                    <p className="text-xs uppercase text-slate-400">Ngày ghi ví</p>
                    <p className="font-medium">
                        {commission.credited_at ? String(commission.credited_at).slice(0, 10) : "—"}
                    </p>
                </div>
            </div>

            {commission.settlement && (
                <div className="mt-6 rounded-lg border border-slate-100 p-3 text-sm">
                    <p className="font-semibold">Phiếu quyết toán tháng {commission.settlement.period_month}</p>
                    <p className="text-slate-500">{formatJpy(commission.settlement.total_amount_jpy)}</p>
                </div>
            )}
        </section>
    );
}
