import React, { useEffect, useState } from "react";
import { Link, useParams } from "react-router";
import { api, formatJpy } from "../../lib/api";
import {
    ORDER_STATUS_LABELS,
    formatDeliveryDateRange,
    orderStatusBadgeClass,
    paymentMethodLabel,
    paymentStatusBadgeClass,
    paymentStatusLabel,
    paymentApprovalStatusBadgeClass,
    paymentApprovalStatusLabel,
    paymentTypeLabel,
    shippingModeLabel,
    sortOrderStatuses,
} from "../../lib/orderStatuses";
import { formatStatusHistoryEntry } from "../../lib/orderAuditTrail";
import { defaultHandoffDepartment, handoffDepartments } from "../../lib/orderDepartments";
import { useAuth } from "../../context/AuthContext";
import RejectPaymentModal from "../../components/RejectPaymentModal";
import VehicleDocsCard from "../../components/VehicleDocsCard";
import VehicleDocsModal from "../../components/VehicleDocsModal";

function formatDate(value) {
    if (!value) return "—";
    return String(value).slice(0, 10);
}

function timelineDotClass(status) {
    const map = {
        new: "bg-slate-400",
        consulting: "bg-blue-500",
        confirmed: "bg-blue-500",
        deposit_pending: "bg-orange-400",
        deposit_paid: "bg-blue-400",
        waiting_delivery: "bg-orange-500",
        packaged: "bg-blue-500",
        delivering: "bg-orange-500",
        delivered: "bg-emerald-500",
        completed: "bg-emerald-500",
        cancelled: "bg-red-500",
        refunded: "bg-red-400",
    };
    return map[status] || "bg-slate-400";
}

function CompactLabel({ children }) {
    return <span className="order-detail-k">{children}</span>;
}

export default function OrderDetailPage() {
    const { id } = useParams();
    const { loading: authLoading, isPackaging, isAccounting, isStaff, isCollaborator, isAdmin } = useAuth();
    const canSubmitPayment = isStaff || isCollaborator || isPackaging;
    const canApprovePayment = isAccounting || isAdmin;
    const canEditVehicleDocs = isStaff || isCollaborator;
    const [order, setOrder] = useState(null);
    const [transitions, setTransitions] = useState([]);
    const [handoffDepartment, setHandoffDepartment] = useState("");
    const [statusNote, setStatusNote] = useState("");
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);
    const [paymentSaving, setPaymentSaving] = useState(false);
    const [error, setError] = useState("");
    const [paymentError, setPaymentError] = useState("");
    const [paymentActionId, setPaymentActionId] = useState(null);
    const [rejectTarget, setRejectTarget] = useState(null);
    const [rejectSaving, setRejectSaving] = useState(false);
    const [rejectError, setRejectError] = useState("");
    const [paymentForm, setPaymentForm] = useState({
        payment_type: "deposit",
        amount_jpy: "",
        discount_jpy: "",
        payment_method: "bank_transfer",
        payment_date: new Date().toISOString().slice(0, 10),
        notes: "",
    });
    const [vehicleDocsModalOpen, setVehicleDocsModalOpen] = useState(false);
    const [vehicleDocsSaving, setVehicleDocsSaving] = useState(false);
    const [vehicleDocsError, setVehicleDocsError] = useState("");

    const loadOrder = () => {
        setLoading(true);
        return Promise.all([api.get(`/orders/${id}`), api.get(`/orders/${id}/transitions`)])
            .then(([orderRes, transitionRes]) => {
                setOrder(orderRes.data);
                const allowed = sortOrderStatuses(transitionRes.data?.allowed_transitions || []);
                setTransitions(allowed);
                const departments = handoffDepartments(orderRes.data?.order_status, allowed, {
                    packagingRole: isPackaging,
                });
                const defaultDepartment = defaultHandoffDepartment(departments);
                setHandoffDepartment(defaultDepartment?.value || "");
            })
            .catch(() => setError("Không tải được đơn hàng."))
            .finally(() => setLoading(false));
    };

    useEffect(() => {
        loadOrder();
    }, [id, isPackaging]);

    const departments = order
        ? handoffDepartments(order.order_status, transitions, { packagingRole: isPackaging })
        : [];
    const selectedDepartment = departments.find((department) => department.value === handoffDepartment);

    const handleStatusUpdate = async (event) => {
        event.preventDefault();

        const targetStatus = selectedDepartment?.targetStatus;
        if (!targetStatus) return;

        const noteParts = [];
        if (selectedDepartment) {
            noteParts.push(`Chuyển bộ phận: ${selectedDepartment.label}`);
        }
        if (statusNote.trim()) {
            noteParts.push(statusNote.trim());
        }

        setSaving(true);
        setError("");
        try {
            await api.put(`/orders/${id}`, {
                order_status: targetStatus,
                status_note: noteParts.length > 0 ? noteParts.join(" — ") : undefined,
            });
            setStatusNote("");
            await loadOrder();
        } catch (requestError) {
            setError(requestError.response?.data?.message || "Không chuyển được trạng thái.");
        } finally {
            setSaving(false);
        }
    };

    const handlePaymentSubmit = async (event) => {
        event.preventDefault();
        setPaymentSaving(true);
        setPaymentError("");
        try {
            await api.post(`/orders/${id}/payments`, {
                payment_type: paymentForm.payment_type,
                amount_jpy: Number(paymentForm.amount_jpy),
                discount_jpy: paymentForm.discount_jpy === "" ? 0 : Number(paymentForm.discount_jpy),
                payment_method: paymentForm.payment_method,
                payment_date: paymentForm.payment_date,
                notes: paymentForm.notes || undefined,
            });
            setPaymentForm((current) => ({
                ...current,
                amount_jpy: "",
                discount_jpy: "",
                notes: "",
            }));
            await loadOrder();
        } catch (requestError) {
            setPaymentError(requestError.response?.data?.message || "Không gửi được thanh toán để duyệt.");
        } finally {
            setPaymentSaving(false);
        }
    };

    const handleApprovePayment = async (paymentId) => {
        setPaymentActionId(paymentId);
        setPaymentError("");
        try {
            await api.put(`/payments/${paymentId}/approve`);
            await loadOrder();
        } catch (requestError) {
            setPaymentError(requestError.response?.data?.message || "Không duyệt được thanh toán.");
        } finally {
            setPaymentActionId(null);
        }
    };

    const handleRejectPayment = async (note) => {
        if (!rejectTarget) return;
        setRejectSaving(true);
        setRejectError("");
        try {
            await api.put(`/payments/${rejectTarget}/reject`, { rejection_note: note });
            setRejectTarget(null);
            await loadOrder();
        } catch (requestError) {
            const message =
                requestError.response?.data?.errors?.rejection_note?.[0] ||
                requestError.response?.data?.message ||
                "Không từ chối được thanh toán.";
            setRejectError(message);
        } finally {
            setRejectSaving(false);
        }
    };

    const handleVehicleDocsSave = async (form) => {
        setVehicleDocsSaving(true);
        setVehicleDocsError("");
        try {
            await api.put(`/orders/${id}`, {
                vehicle_chassis_number: form.chassis_number.trim() || null,
                vehicle_owner_name_vi: form.owner_name_vi.trim() || null,
                vehicle_owner_name_ja: form.owner_name_ja.trim() || null,
                vehicle_owner_postal_code: form.postal_code.trim() || null,
                vehicle_owner_phone: form.phone.trim() || null,
                vehicle_owner_address: form.address.trim() || null,
            });
            setVehicleDocsModalOpen(false);
            await loadOrder();
        } catch (requestError) {
            setVehicleDocsError(requestError.response?.data?.message || "Không lưu được giấy tờ xe.");
        } finally {
            setVehicleDocsSaving(false);
        }
    };

    if (loading || authLoading) {
        return <p className="py-8 text-center text-sm text-slate-500">Đang tải...</p>;
    }

    if (!order) {
        return <p className="text-sm text-red-600">{error || "Không tìm thấy đơn hàng."}</p>;
    }

    const customer = order.customer;
    const customerAddress = [customer?.address, customer?.city, customer?.prefecture, customer?.postal_code]
        .filter(Boolean)
        .join(", ");
    const facebookUrl = order.facebook_url || customer?.facebook_url;
    const googleMapsUrl = customer?.google_maps_url;
    const hasOutstanding = order.debt && Number(order.debt.outstanding_jpy) > 0;
    const statusHistories = [...(order.status_histories || [])].sort(
        (a, b) => new Date(b.created_at || 0) - new Date(a.created_at || 0)
    );
    const vehicleDocsInitialValues = {
        chassis_number: order.vehicle_chassis_number || "",
        owner_name_vi: order.vehicle_owner_name_vi || "",
        owner_name_ja: order.vehicle_owner_name_ja || "",
        postal_code: order.vehicle_owner_postal_code || "",
        phone: order.vehicle_owner_phone || "",
        address: order.vehicle_owner_address || "",
    };

    return (
        <div className="order-detail-page order-detail-compact">
            <header className="order-detail-hero-compact">
                <Link to="/orders" className="order-detail-back-compact">
                    ← Danh sách đơn
                </Link>
                <div className="flex flex-wrap items-center gap-3">
                    <h1 className="order-detail-hero-code-compact">{order.order_code}</h1>
                    <span className={orderStatusBadgeClass(order.order_status)}>
                        {ORDER_STATUS_LABELS[order.order_status] || order.order_status}
                    </span>
                    <span className={paymentStatusBadgeClass(order.payment_status)}>
                        {paymentStatusLabel(order.payment_status)}
                    </span>
                </div>
                {order.confirmed_at && (
                    <p className="order-detail-confirmed-compact">
                        <span>Ngày chốt:</span> {formatDate(order.confirmed_at)}
                    </p>
                )}
            </header>

            <div className="order-detail-grid">
                <section className="order-detail-card-compact order-detail-span-people">
                    <h3 className="order-detail-h">Khách hàng</h3>
                    <div className="space-y-1.5">
                        <p className="order-detail-v font-semibold">{customer?.full_name || "—"}</p>
                        {customer?.phone && (
                            <p className="order-detail-line">
                                <CompactLabel>SĐT</CompactLabel> {customer.phone}
                            </p>
                        )}
                        {customerAddress && (
                            <p className="order-detail-line order-detail-clamp-2">
                                <CompactLabel>Địa chỉ</CompactLabel> {customerAddress}
                            </p>
                        )}
                        {(facebookUrl || googleMapsUrl) && (
                            <div className="flex flex-wrap gap-1.5 pt-1">
                                {facebookUrl && (
                                    <a href={facebookUrl} target="_blank" rel="noreferrer" className="order-detail-link-btn-compact">
                                        Facebook
                                    </a>
                                )}
                                {googleMapsUrl && (
                                    <a href={googleMapsUrl} target="_blank" rel="noreferrer" className="order-detail-link-btn-compact">
                                        Maps
                                    </a>
                                )}
                            </div>
                        )}
                    </div>

                    <VehicleDocsCard
                        order={order}
                        canEdit={canEditVehicleDocs}
                        onEdit={() => {
                            setVehicleDocsError("");
                            setVehicleDocsModalOpen(true);
                        }}
                    />
                </section>

                <section className="order-detail-card-compact order-detail-span-money">
                    <h3 className="order-detail-h">Thanh toán & Công nợ</h3>
                    <div className="flex items-baseline justify-between gap-2">
                        <p className="order-detail-total">{formatJpy(order.total_amount_jpy)}</p>
                        <span className={paymentStatusBadgeClass(order.payment_status)}>
                            {paymentStatusLabel(order.payment_status)}
                        </span>
                    </div>
                    {order.debt ? (
                        <div className="order-detail-stat-grid mt-2">
                            {Number(order.discount_jpy) > 0 && (
                                <div>
                                    <CompactLabel>Giảm giá</CompactLabel>
                                    <p className="order-detail-v text-rose-600">-{formatJpy(order.discount_jpy)}</p>
                                </div>
                            )}
                            <div>
                                <CompactLabel>Phải thu</CompactLabel>
                                <p className="order-detail-v">{formatJpy(order.debt.total_payable_jpy)}</p>
                            </div>
                            <div>
                                <CompactLabel>Đã thu</CompactLabel>
                                <p className="order-detail-v text-emerald-600">{formatJpy(order.debt.total_paid_jpy)}</p>
                            </div>
                            <div>
                                <CompactLabel>Còn lại</CompactLabel>
                                <p className={`order-detail-v ${hasOutstanding ? "text-orange-600 font-bold" : ""}`}>
                                    {formatJpy(order.debt.outstanding_jpy)}
                                </p>
                            </div>
                            <div>
                                <CompactLabel>Hạn TT</CompactLabel>
                                <p className="order-detail-v">{formatDate(order.debt.due_date)}</p>
                            </div>
                        </div>
                    ) : (
                        <p className="mt-1 text-xs text-slate-500">Chưa có công nợ</p>
                    )}
                    {(order.payment_method ||
                        order.shipping_mode ||
                        formatDeliveryDateRange(order) ||
                        order.battery_capacity) && (
                        <div className="mt-2 flex flex-wrap gap-x-4 gap-y-1 border-t border-slate-100 pt-2 text-xs text-slate-600">
                            {order.shipping_mode && <span>VC: {shippingModeLabel(order.shipping_mode)}</span>}
                            {order.payment_method && <span>HT: {order.payment_method}</span>}
                            {order.battery_capacity && <span>Pin: {order.battery_capacity}</span>}
                            {formatDeliveryDateRange(order) && (
                                <span>
                                    Giao: {formatDeliveryDateRange(order)}
                                    {order.delivery_time_slot ? ` (${order.delivery_time_slot})` : ""}
                                </span>
                            )}
                        </div>
                    )}

                    {hasOutstanding && canSubmitPayment && (
                        <form className="order-detail-payment-form mt-3 border-t border-slate-100 pt-3" onSubmit={handlePaymentSubmit}>
                            <p className="order-detail-h !mb-2">Gửi thanh toán chờ kế toán duyệt</p>
                            <div className="grid gap-2 sm:grid-cols-2">
                                <select
                                    className="order-detail-input"
                                    value={paymentForm.payment_type}
                                    onChange={(e) => setPaymentForm({ ...paymentForm, payment_type: e.target.value })}
                                >
                                    <option value="deposit">Cọc</option>
                                    <option value="partial">Thanh toán</option>
                                    <option value="full">Tất toán</option>
                                </select>
                                <input
                                    type="number"
                                    min="1"
                                    className="order-detail-input"
                                    placeholder="Số tiền (円)"
                                    value={paymentForm.amount_jpy}
                                    onChange={(e) => setPaymentForm({ ...paymentForm, amount_jpy: e.target.value })}
                                    required
                                />
                                <input
                                    type="number"
                                    min="0"
                                    className="order-detail-input"
                                    placeholder="Giảm giá (円)"
                                    value={paymentForm.discount_jpy}
                                    onChange={(e) => setPaymentForm({ ...paymentForm, discount_jpy: e.target.value })}
                                />
                                <select
                                    className="order-detail-input"
                                    value={paymentForm.payment_method}
                                    onChange={(e) => setPaymentForm({ ...paymentForm, payment_method: e.target.value })}
                                >
                                    <option value="bank_transfer">Chuyển khoản</option>
                                    <option value="cash">Tiền mặt</option>
                                    <option value="card">Thẻ</option>
                                    <option value="other">Khác</option>
                                </select>
                                <input
                                    type="date"
                                    className="order-detail-input"
                                    value={paymentForm.payment_date}
                                    onChange={(e) => setPaymentForm({ ...paymentForm, payment_date: e.target.value })}
                                    required
                                />
                            </div>
                            <input
                                className="order-detail-input mt-2"
                                placeholder="Ghi chú thanh toán..."
                                value={paymentForm.notes}
                                onChange={(e) => setPaymentForm({ ...paymentForm, notes: e.target.value })}
                            />
                            <p className="mt-1 text-[14px] text-slate-500">
                                Giảm giá sẽ được áp dụng vào tổng đơn sau khi kế toán duyệt.
                            </p>
                            <button type="submit" className="order-detail-btn-gradient-compact mt-2" disabled={paymentSaving}>
                                {paymentSaving ? "Đang gửi..." : "Gửi duyệt kế toán"}
                            </button>
                            {paymentError && <p className="mt-1 text-xs text-red-600">{paymentError}</p>}
                        </form>
                    )}

                    {(order.payments || []).length > 0 && (
                        <ul className="order-detail-payment-list mt-3 border-t border-slate-100 pt-2">
                            {[...(order.payments || [])]
                                .sort((a, b) => new Date(b.payment_date || b.created_at) - new Date(a.payment_date || a.created_at))
                                .map((payment) => (
                                    <li key={payment.id} className="order-detail-payment-item flex-wrap">
                                        <span className="order-detail-payment-badge">{paymentTypeLabel(payment.payment_type)}</span>
                                        <span className="font-semibold text-emerald-700">{formatJpy(payment.amount_jpy)}</span>
                                        {Number(payment.discount_jpy) > 0 && (
                                            <span className="text-rose-600">
                                                Giảm {formatJpy(payment.discount_jpy)}
                                                {payment.approval_status === "pending" ? " (chờ duyệt)" : ""}
                                            </span>
                                        )}
                                        <span className="text-slate-500">{formatDate(payment.payment_date)}</span>
                                        <span className="text-slate-400">{paymentMethodLabel(payment.payment_method)}</span>
                                        <span className={paymentApprovalStatusBadgeClass(payment.approval_status)}>
                                            {paymentApprovalStatusLabel(payment.approval_status)}
                                        </span>
                                        {payment.approval_status === "pending" && canApprovePayment && (
                                            <span className="flex gap-1">
                                                <button
                                                    type="button"
                                                    className="rounded bg-emerald-600 px-2 py-0.5 text-[15px] text-white hover:bg-emerald-700 disabled:opacity-50"
                                                    disabled={paymentActionId === payment.id}
                                                    onClick={() => handleApprovePayment(payment.id)}
                                                >
                                                    Duyệt
                                                </button>
                                                <button
                                                    type="button"
                                                    className="rounded border border-red-200 px-2 py-0.5 text-[15px] text-red-600 hover:bg-red-50 disabled:opacity-50"
                                                    disabled={paymentActionId === payment.id}
                                                    onClick={() => {
                                                        setRejectError("");
                                                        setRejectTarget(payment.id);
                                                    }}
                                                >
                                                    Từ chối
                                                </button>
                                            </span>
                                        )}
                                        {payment.rejection_note && (
                                            <span className="w-full text-[15px] text-red-600">{payment.rejection_note}</span>
                                        )}
                                    </li>
                                ))}
                        </ul>
                    )}
                </section>

                <section className="order-detail-card-compact order-detail-span-action">
                    <h3 className="order-detail-h">Chuyển trạng thái</h3>
                    {transitions.length > 0 ? (
                        <form className="space-y-2" onSubmit={handleStatusUpdate}>
                            <div className="grid gap-2 sm:grid-cols-2">
                                <div>
                                    <label className="mb-1 block text-[15px] font-medium text-slate-500">Bộ phận</label>
                                    <select
                                        className="order-detail-input"
                                        value={handoffDepartment}
                                        onChange={(e) => setHandoffDepartment(e.target.value)}
                                    >
                                        {departments.map((department) => (
                                            <option key={department.value} value={department.value}>
                                                {department.label}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                                <div className="flex items-end">
                                    <button
                                        type="submit"
                                        className="order-detail-btn-gradient-compact w-full"
                                        disabled={saving || !selectedDepartment}
                                    >
                                        {saving ? "Đang lưu..." : "Cập nhật"}
                                    </button>
                                </div>
                            </div>
                            <textarea
                                className="order-detail-input min-h-[52px]"
                                rows={2}
                                placeholder="Ghi chú chuyển bộ phận..."
                                value={statusNote}
                                onChange={(e) => setStatusNote(e.target.value)}
                            />
                        </form>
                    ) : (
                        <p className="text-xs text-slate-500">
                            {isPackaging && order.order_status === "delivered"
                                ? "Đơn đã giao — tự hoàn thành khi khách thanh toán đủ."
                                : order.order_status === "waiting_delivery" && !isPackaging
                                  ? "Đơn đã chuyển bộ phận Đóng gói — bộ phận vận chuyển tiếp tục xử lý."
                                  : "Không còn bước chuyển tiếp."}
                        </p>
                    )}
                    {error && <p className="mt-1 text-xs text-red-600">{error}</p>}
                    {!isPackaging && (
                    <p className="order-detail-commission-line mt-2">
                        💰{" "}
                        {order.commission ? (
                            <>
                                Hoa hồng {formatJpy(order.commission.commission_amount_jpy)} (đã ghi ví) ·{" "}
                                <Link to={`/commissions/${order.commission.id}`} className="text-blue-600 hover:underline">
                                    Chi tiết
                                </Link>
                            </>
                        ) : (
                            "Hoa hồng sẽ tự cộng vào ví khi đơn hoàn thành"
                        )}
                    </p>
                    )}
                </section>

                <section className="order-detail-card-compact order-detail-span-products">
                    <h3 className="order-detail-h">Sản phẩm</h3>
                    <div className="order-detail-table-wrap-compact">
                        <table className="order-detail-table-compact">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th className="text-center w-12">SL</th>
                                    <th className="text-right">Đơn giá</th>
                                    <th className="text-right">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                {(order.items || []).map((item) => (
                                    <tr key={item.id}>
                                        <td>{item.product?.name || `#${item.product_id}`}</td>
                                        <td className="text-center">{item.quantity}</td>
                                        <td className="text-right">{formatJpy(item.unit_price_jpy)}</td>
                                        <td className="text-right font-medium">{formatJpy(item.line_total_jpy)}</td>
                                    </tr>
                                ))}
                            </tbody>
                            <tfoot>
                                {Number(order.shipping_fee_jpy) > 0 && (
                                    <tr>
                                        <td colSpan={3} className="text-right text-slate-500">
                                            Phí ship
                                        </td>
                                        <td className="text-right">{formatJpy(order.shipping_fee_jpy)}</td>
                                    </tr>
                                )}
                                <tr>
                                    <td colSpan={3} className="text-right font-semibold">
                                        Tổng
                                    </td>
                                    <td className="text-right font-bold text-blue-600">{formatJpy(order.total_amount_jpy)}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    {order.notes && (
                        <p className="order-detail-note-inline mt-2">
                            <span className="font-medium text-amber-800">Ghi chú:</span> {order.notes}
                        </p>
                    )}
                </section>

                <section className="order-detail-card-compact order-detail-span-history">
                    <h3 className="order-detail-h">Lịch sử & dấu ấn</h3>
                    {statusHistories.length > 0 ? (
                        <ul className="order-detail-timeline-compact">
                            {statusHistories.map((history) => {
                                const formatted = formatStatusHistoryEntry(history);
                                const isPayment = formatted.kind === "payment";
                                const isAuto = formatted.kind === "auto";

                                return (
                                <li key={history.id} className="order-detail-timeline-item-compact">
                                    <div
                                        className={`order-detail-timeline-dot-compact ${
                                            isPayment || isAuto ? "bg-emerald-500" : timelineDotClass(history.to_status)
                                        }`}
                                    />
                                    <div className="min-w-0 flex-1">
                                        <p
                                            className={`text-xs font-semibold ${
                                                isPayment || isAuto ? "text-emerald-700" : "text-slate-800"
                                            }`}
                                        >
                                            {formatted.title}
                                        </p>
                                        {formatted.note && (
                                            <p className="text-[15px] text-slate-500">{formatted.note}</p>
                                        )}
                                        <p className="text-[14px] text-slate-400">{history.changer?.name || "Hệ thống"}</p>
                                    </div>
                                </li>
                                );
                            })}
                        </ul>
                    ) : (
                        <p className="text-xs text-slate-500">Chưa có lịch sử</p>
                    )}
                </section>
            </div>

            <RejectPaymentModal
                open={Boolean(rejectTarget)}
                saving={rejectSaving}
                error={rejectError}
                onClose={() => {
                    if (!rejectSaving) {
                        setRejectTarget(null);
                        setRejectError("");
                    }
                }}
                onSubmit={handleRejectPayment}
            />

            <VehicleDocsModal
                open={vehicleDocsModalOpen}
                saving={vehicleDocsSaving}
                error={vehicleDocsError}
                initialValues={vehicleDocsInitialValues}
                onClose={() => {
                    if (!vehicleDocsSaving) {
                        setVehicleDocsModalOpen(false);
                        setVehicleDocsError("");
                    }
                }}
                onSubmit={handleVehicleDocsSave}
            />
        </div>
    );
}
