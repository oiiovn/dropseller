import React, { useEffect, useMemo, useState } from "react";
import { Link, useNavigate, useParams } from "react-router";
import { adminApi, formatJpy } from "../../lib/api";
import { buildOrderAuditTrail } from "../../../crm/lib/orderAuditTrail";
import RejectPaymentModal from "../../../crm/components/RejectPaymentModal";
import VehicleDocsCard from "../../../crm/components/VehicleDocsCard";
import VehicleDocsModal from "../../../crm/components/VehicleDocsModal";
import { defaultHandoffDepartment, handoffDepartments } from "../../../crm/lib/orderDepartments";
import {
    ORDER_STATUS_LABELS,
    orderStatusAdminBadgeClass,
    formatDeliveryDateRange,
    paymentMethodLabel,
    paymentStatusLabel,
    paymentApprovalStatusBadgeClass,
    paymentApprovalStatusLabel,
    paymentTypeLabel,
    shippingModeLabel,
    sortOrderStatuses,
    statusBadgeClass,
} from "../../../crm/lib/orderStatuses";

function formatDate(value) {
    if (!value) return "—";
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) {
        return String(value).slice(0, 10);
    }
    return date.toLocaleString("vi-VN", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
}

function AdminOrderDetailPage() {
    const { id } = useParams();
    const navigate = useNavigate();
    const [order, setOrder] = useState(null);
    const [pins, setPins] = useState([]);
    const [transitions, setTransitions] = useState([]);
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);
    const [paymentSaving, setPaymentSaving] = useState(false);
    const [error, setError] = useState("");
    const [paymentError, setPaymentError] = useState("");
    const [paymentActionId, setPaymentActionId] = useState(null);
    const [rejectTarget, setRejectTarget] = useState(null);
    const [rejectSaving, setRejectSaving] = useState(false);
    const [rejectError, setRejectError] = useState("");
    const [nextStatus, setNextStatus] = useState("");
    const [handoffDepartment, setHandoffDepartment] = useState("");
    const [statusNote, setStatusNote] = useState("");
    const [editForm, setEditForm] = useState({
        notes: "",
        shipping_mode: "boxed",
        delivery_date_from: "",
        delivery_date_to: "",
        delivery_time_slot: "",
        payment_method: "",
        pin_id: "",
        facebook_url: "",
        shipping_fee_jpy: "",
    });
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
        return Promise.all([
            adminApi.get(`/orders/${id}`),
            adminApi.get(`/orders/${id}/transitions`),
            adminApi.get("/pins", { params: { per_page: 200 } }),
        ])
            .then(([orderRes, transitionRes, pinsRes]) => {
                const data = orderRes.data;
                const pinRows = pinsRes.data?.data ?? pinsRes.data ?? [];
                const matchedPin = pinRows.find((pin) => pin.name === data.battery_capacity);
                setPins(pinRows);
                setOrder(data);
                const allowed = sortOrderStatuses(transitionRes.data?.allowed_transitions || []);
                setTransitions(allowed);
                setNextStatus(allowed[0] || "");
                const departments = handoffDepartments(data.order_status, allowed);
                setHandoffDepartment(defaultHandoffDepartment(departments)?.value || "");
                setEditForm({
                    notes: data.notes || "",
                    shipping_mode: data.shipping_mode || "boxed",
                    delivery_date_from: data.delivery_date_from
                        ? String(data.delivery_date_from).slice(0, 10)
                        : data.delivery_date
                          ? String(data.delivery_date).slice(0, 10)
                          : "",
                    delivery_date_to: data.delivery_date_to ? String(data.delivery_date_to).slice(0, 10) : "",
                    delivery_time_slot: data.delivery_time_slot || "",
                    payment_method: data.payment_method || "",
                    pin_id: matchedPin ? String(matchedPin.id) : "",
                    facebook_url: data.facebook_url || "",
                    shipping_fee_jpy: data.shipping_fee_jpy ?? "",
                });
            })
            .catch(() => setError("Không tải được đơn hàng."))
            .finally(() => setLoading(false));
    };

    useEffect(() => {
        loadOrder();
    }, [id]);

    const isDeleted = Boolean(order?.deleted_at);
    const hasOutstanding = order?.debt && Number(order.debt.outstanding_jpy) > 0;

    const mergedTrail = useMemo(() => {
        if (!order) return [];
        return buildOrderAuditTrail(order, formatJpy);
    }, [order]);

    const departments = order ? handoffDepartments(order.order_status, transitions) : [];
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
            await adminApi.put(`/orders/${id}`, {
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

    const selectedPin = useMemo(
        () => pins.find((pin) => String(pin.id) === String(editForm.pin_id)),
        [pins, editForm.pin_id]
    );

    const handleEditSubmit = async (event) => {
        event.preventDefault();
        setSaving(true);
        setError("");
        try {
            await adminApi.put(`/orders/${id}`, {
                notes: editForm.notes || null,
                shipping_mode: editForm.shipping_mode || null,
                delivery_date_from: editForm.delivery_date_from || null,
                delivery_date_to: editForm.delivery_date_to || null,
                delivery_time_slot: editForm.delivery_time_slot || null,
                payment_method: editForm.payment_method || null,
                battery_capacity: selectedPin?.name || null,
                facebook_url: editForm.facebook_url || null,
                shipping_fee_jpy: editForm.shipping_fee_jpy === "" ? null : Number(editForm.shipping_fee_jpy),
            });
            await loadOrder();
        } catch (requestError) {
            setError(requestError.response?.data?.message || "Không lưu được thông tin đơn.");
        } finally {
            setSaving(false);
        }
    };

    const handlePaymentSubmit = async (event) => {
        event.preventDefault();
        setPaymentSaving(true);
        setPaymentError("");
        try {
            await adminApi.post(`/orders/${id}/payments`, {
                payment_type: paymentForm.payment_type,
                amount_jpy: Number(paymentForm.amount_jpy),
                discount_jpy: paymentForm.discount_jpy === "" ? 0 : Number(paymentForm.discount_jpy),
                payment_method: paymentForm.payment_method,
                payment_date: paymentForm.payment_date,
                notes: paymentForm.notes || undefined,
            });
            setPaymentForm((current) => ({ ...current, amount_jpy: "", discount_jpy: "", notes: "" }));
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
            await adminApi.put(`/payments/${paymentId}/approve`);
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
            await adminApi.put(`/payments/${rejectTarget}/reject`, { rejection_note: note });
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
            await adminApi.put(`/orders/${id}`, {
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

    const handleDelete = async () => {
        if (!window.confirm(`Xóa mềm đơn ${order.order_code}? Dữ liệu vẫn được lưu và có thể khôi phục.`)) {
            return;
        }
        setError("");
        try {
            await adminApi.delete(`/orders/${id}`);
            navigate("/orders");
        } catch (requestError) {
            setError(requestError.response?.data?.message || "Không xóa được đơn hàng.");
        }
    };

    const handleRestore = async () => {
        setError("");
        try {
            await adminApi.post(`/orders/${id}/restore`);
            await loadOrder();
        } catch (requestError) {
            setError(requestError.response?.data?.message || "Không khôi phục được đơn hàng.");
        }
    };

    if (loading) {
        return <p className="text-sm text-slate-500">Đang tải...</p>;
    }

    if (!order) {
        return <p className="text-sm text-red-600">{error || "Không tìm thấy đơn hàng."}</p>;
    }

    const vehicleDocsInitialValues = {
        chassis_number: order.vehicle_chassis_number || "",
        owner_name_vi: order.vehicle_owner_name_vi || "",
        owner_name_ja: order.vehicle_owner_name_ja || "",
        postal_code: order.vehicle_owner_postal_code || "",
        phone: order.vehicle_owner_phone || "",
        address: order.vehicle_owner_address || "",
    };

    return (
        <div className="space-y-4">
            <div className="admin-card">
                <div className="mb-3 flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <Link to="/orders" className="text-sm text-blue-600 hover:underline">
                            ← Danh sách đơn
                        </Link>
                        <div className="mt-2 flex flex-wrap items-center gap-2">
                            <h2 className="text-xl font-bold">{order.order_code}</h2>
                            <span className={orderStatusAdminBadgeClass(order.order_status)}>
                                {ORDER_STATUS_LABELS[order.order_status] || order.order_status}
                            </span>
                            <span className={`admin-badge ${statusBadgeClass(order.payment_status)}`}>
                                {paymentStatusLabel(order.payment_status)}
                            </span>
                            {isDeleted && (
                                <span className="admin-badge bg-red-100 text-red-700">Đã xóa mềm</span>
                            )}
                        </div>
                        <p className="mt-1 text-sm text-slate-500">
                            CTV: {order.affiliate?.code} — {order.affiliate?.full_name} · Tạo bởi{" "}
                            {order.creator?.name || "—"}
                            {order.confirmed_at && (
                                <>
                                    {" "}
                                    · Ngày chốt: {String(order.confirmed_at).slice(0, 10)}
                                </>
                            )}
                        </p>
                    </div>
                    <div className="flex flex-wrap gap-2">
                        {isDeleted ? (
                            <button type="button" className="admin-btn-primary" onClick={handleRestore}>
                                Khôi phục đơn
                            </button>
                        ) : (
                            <button type="button" className="admin-btn-secondary text-red-600" onClick={handleDelete}>
                                Xóa mềm
                            </button>
                        )}
                    </div>
                </div>
                {error && <p className="text-sm text-red-600">{error}</p>}
            </div>

            <div className="grid gap-4 xl:grid-cols-2">
                <section className="admin-card">
                    <h3 className="mb-3 font-semibold">Khách hàng</h3>
                    <p className="font-medium">{order.customer?.full_name || "—"}</p>
                    <p className="text-sm text-slate-600">{order.customer?.phone || "—"}</p>
                    <p className="mt-2 text-sm text-slate-600">
                        {[order.customer?.address, order.customer?.city, order.customer?.prefecture]
                            .filter(Boolean)
                            .join(", ") || "—"}
                    </p>

                    <VehicleDocsCard
                        order={order}
                        canEdit={!isDeleted}
                        onEdit={() => {
                            setVehicleDocsError("");
                            setVehicleDocsModalOpen(true);
                        }}
                    />
                </section>

                <section className="admin-card">
                    <h3 className="mb-3 font-semibold">Thanh toán & Công nợ</h3>
                    <p className="text-2xl font-bold text-blue-700">{formatJpy(order.total_amount_jpy)}</p>
                    {order.debt && (
                        <div className="mt-3 grid grid-cols-2 gap-2 text-sm">
                            {Number(order.discount_jpy) > 0 && (
                                <div>
                                    <p className="text-slate-500">Giảm giá</p>
                                    <p className="font-medium text-rose-600">-{formatJpy(order.discount_jpy)}</p>
                                </div>
                            )}
                            <div>
                                <p className="text-slate-500">Phải thu</p>
                                <p className="font-medium">{formatJpy(order.debt.total_payable_jpy)}</p>
                            </div>
                            <div>
                                <p className="text-slate-500">Đã thu</p>
                                <p className="font-medium text-emerald-600">{formatJpy(order.debt.total_paid_jpy)}</p>
                            </div>
                            <div>
                                <p className="text-slate-500">Còn lại</p>
                                <p className={`font-medium ${hasOutstanding ? "text-orange-600" : ""}`}>
                                    {formatJpy(order.debt.outstanding_jpy)}
                                </p>
                            </div>
                            <div>
                                <p className="text-slate-500">Hạn TT</p>
                                <p className="font-medium">{formatDate(order.debt.due_date)}</p>
                            </div>
                        </div>
                    )}

                    {!isDeleted && hasOutstanding && (
                        <form className="mt-4 space-y-2 border-t border-slate-100 pt-4" onSubmit={handlePaymentSubmit}>
                            <p className="text-sm font-medium">Gửi thanh toán chờ kế toán duyệt</p>
                            <div className="grid gap-2 sm:grid-cols-2">
                                <select
                                    className="rounded-lg border border-slate-200 px-3 py-2 text-sm"
                                    value={paymentForm.payment_type}
                                    onChange={(event) =>
                                        setPaymentForm((current) => ({ ...current, payment_type: event.target.value }))
                                    }
                                >
                                    <option value="deposit">Cọc</option>
                                    <option value="partial">Thanh toán</option>
                                    <option value="full">Tất toán</option>
                                </select>
                                <input
                                    type="number"
                                    min="1"
                                    required
                                    className="rounded-lg border border-slate-200 px-3 py-2 text-sm"
                                    placeholder="Số tiền (円)"
                                    value={paymentForm.amount_jpy}
                                    onChange={(event) =>
                                        setPaymentForm((current) => ({ ...current, amount_jpy: event.target.value }))
                                    }
                                />
                                <input
                                    type="number"
                                    min="0"
                                    className="rounded-lg border border-slate-200 px-3 py-2 text-sm"
                                    placeholder="Giảm giá (円)"
                                    value={paymentForm.discount_jpy}
                                    onChange={(event) =>
                                        setPaymentForm((current) => ({ ...current, discount_jpy: event.target.value }))
                                    }
                                />
                            </div>
                            <p className="text-xs text-slate-500">Giảm giá sẽ được áp dụng vào tổng đơn sau khi kế toán duyệt.</p>
                            <button type="submit" className="admin-btn-primary" disabled={paymentSaving}>
                                {paymentSaving ? "Đang gửi..." : "Gửi duyệt kế toán"}
                            </button>
                            {paymentError && <p className="text-xs text-red-600">{paymentError}</p>}
                        </form>
                    )}

                    {(order.payments || []).length > 0 && (
                        <ul className="mt-3 space-y-1 border-t border-slate-100 pt-3 text-sm">
                            {order.payments.map((payment) => (
                                <li key={payment.id} className="flex flex-wrap items-center gap-2 text-slate-600">
                                    <span className="font-medium text-emerald-700">
                                        {paymentTypeLabel(payment.payment_type)} {formatJpy(payment.amount_jpy)}
                                    </span>
                                    {Number(payment.discount_jpy) > 0 && (
                                        <span className="text-rose-600">
                                            Giảm {formatJpy(payment.discount_jpy)}
                                            {payment.approval_status === "pending" ? " (chờ duyệt)" : ""}
                                        </span>
                                    )}
                                    <span>{formatDate(payment.payment_date)}</span>
                                    <span>{paymentMethodLabel(payment.payment_method)}</span>
                                    <span className={paymentApprovalStatusBadgeClass(payment.approval_status)}>
                                        {paymentApprovalStatusLabel(payment.approval_status)}
                                    </span>
                                    {payment.approval_status === "pending" && (
                                        <>
                                            <button
                                                type="button"
                                                className="rounded bg-emerald-600 px-2 py-0.5 text-xs text-white hover:bg-emerald-700 disabled:opacity-50"
                                                disabled={paymentActionId === payment.id}
                                                onClick={() => handleApprovePayment(payment.id)}
                                            >
                                                Duyệt
                                            </button>
                                            <button
                                                type="button"
                                                className="rounded border border-red-200 px-2 py-0.5 text-xs text-red-600 hover:bg-red-50 disabled:opacity-50"
                                                disabled={paymentActionId === payment.id}
                                                onClick={() => {
                                                    setRejectError("");
                                                    setRejectTarget(payment.id);
                                                }}
                                            >
                                                Từ chối
                                            </button>
                                        </>
                                    )}
                                    {payment.rejection_note && (
                                        <span className="w-full text-xs text-red-600">{payment.rejection_note}</span>
                                    )}
                                </li>
                            ))}
                        </ul>
                    )}
                </section>

                {!isDeleted && (
                    <>
                        <section className="admin-card">
                            <h3 className="mb-3 font-semibold">Chuyển trạng thái</h3>
                            {transitions.length > 0 ? (
                                <form className="space-y-2" onSubmit={handleStatusUpdate}>
                                    <div className="grid gap-2 sm:grid-cols-2">
                                        <div>
                                            <label className="mb-1 block text-xs font-medium text-slate-500">Bộ phận</label>
                                            <select
                                                className="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                                                value={handoffDepartment}
                                                onChange={(event) => setHandoffDepartment(event.target.value)}
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
                                                className="admin-btn-primary w-full"
                                                disabled={saving || !selectedDepartment}
                                            >
                                                {saving ? "Đang lưu..." : "Cập nhật"}
                                            </button>
                                        </div>
                                    </div>
                                    <textarea
                                        className="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                                        rows={2}
                                        placeholder="Ghi chú chuyển bộ phận..."
                                        value={statusNote}
                                        onChange={(event) => setStatusNote(event.target.value)}
                                    />
                                </form>
                            ) : (
                                <p className="text-sm text-slate-500">Không còn bước chuyển tiếp.</p>
                            )}
                        </section>

                        <section className="admin-card">
                            <h3 className="mb-3 font-semibold">Chỉnh sửa thông tin đơn</h3>
                            <form className="space-y-3" onSubmit={handleEditSubmit}>
                                <label className="admin-field">
                                    <span>Ghi chú</span>
                                    <textarea
                                        rows={2}
                                        value={editForm.notes}
                                        onChange={(event) =>
                                            setEditForm((current) => ({ ...current, notes: event.target.value }))
                                        }
                                    />
                                </label>
                                <label className="admin-field">
                                    <span>Hình thức vận chuyển</span>
                                    <select
                                        value={editForm.shipping_mode}
                                        onChange={(event) =>
                                            setEditForm((current) => ({
                                                ...current,
                                                shipping_mode: event.target.value,
                                            }))
                                        }
                                    >
                                        <option value="boxed">Đóng thùng</option>
                                        <option value="direct_ship">Ship trực tiếp</option>
                                    </select>
                                </label>
                                <div className="grid gap-3 sm:grid-cols-2">
                                    <label className="admin-field">
                                        <span>Giao từ ngày</span>
                                        <input
                                            type="date"
                                            value={editForm.delivery_date_from}
                                            onChange={(event) =>
                                                setEditForm((current) => ({
                                                    ...current,
                                                    delivery_date_from: event.target.value,
                                                }))
                                            }
                                        />
                                    </label>
                                    <label className="admin-field">
                                        <span>Giao đến ngày</span>
                                        <input
                                            type="date"
                                            value={editForm.delivery_date_to}
                                            min={editForm.delivery_date_from || undefined}
                                            onChange={(event) =>
                                                setEditForm((current) => ({
                                                    ...current,
                                                    delivery_date_to: event.target.value,
                                                }))
                                            }
                                        />
                                    </label>
                                    <label className="admin-field">
                                        <span>Khung giờ giao</span>
                                        <input
                                            value={editForm.delivery_time_slot}
                                            onChange={(event) =>
                                                setEditForm((current) => ({
                                                    ...current,
                                                    delivery_time_slot: event.target.value,
                                                }))
                                            }
                                        />
                                    </label>
                                    <label className="admin-field">
                                        <span>Pin</span>
                                        <select
                                            value={editForm.pin_id}
                                            onChange={(event) =>
                                                setEditForm((current) => ({
                                                    ...current,
                                                    pin_id: event.target.value,
                                                }))
                                            }
                                        >
                                            <option value="">-- Chọn pin --</option>
                                            {pins.map((pin) => (
                                                <option key={pin.id} value={pin.id}>
                                                    {pin.bike_line ? `${pin.bike_line} — ${pin.name}` : pin.name}
                                                </option>
                                            ))}
                                        </select>
                                        {selectedPin?.description && (
                                            <p className="mt-1 text-xs text-slate-500">{selectedPin.description}</p>
                                        )}
                                    </label>
                                    <label className="admin-field">
                                        <span>Phí ship (円)</span>
                                        <input
                                            type="number"
                                            min="0"
                                            value={editForm.shipping_fee_jpy}
                                            onChange={(event) =>
                                                setEditForm((current) => ({
                                                    ...current,
                                                    shipping_fee_jpy: event.target.value,
                                                }))
                                            }
                                        />
                                    </label>
                                </div>
                                <button type="submit" className="admin-btn-primary" disabled={saving}>
                                    {saving ? "Đang lưu..." : "Lưu thay đổi"}
                                </button>
                            </form>
                        </section>
                    </>
                )}

                <section className="admin-card xl:col-span-2">
                    <h3 className="mb-3 font-semibold">Sản phẩm</h3>
                    <table className="admin-table">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>SL</th>
                                <th>Đơn giá</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            {(order.items || []).map((item) => (
                                <tr key={item.id}>
                                    <td>{item.product?.name || `#${item.product_id}`}</td>
                                    <td>{item.quantity}</td>
                                    <td>{formatJpy(item.unit_price_jpy)}</td>
                                    <td>{formatJpy(item.line_total_jpy)}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </section>

                <section className="admin-card xl:col-span-2">
                    <h3 className="mb-3 font-semibold">Lịch sử & dấu vết</h3>
                    {mergedTrail.length === 0 ? (
                        <p className="text-sm text-slate-500">Chưa có dấu vết.</p>
                    ) : (
                        <ul className="space-y-3">
                            {mergedTrail.map((entry) => (
                                <li
                                    key={entry.id}
                                    className={`rounded-lg border px-3 py-2 text-sm ${
                                        entry.kind === "deleted"
                                            ? "border-red-200 bg-red-50"
                                            : entry.kind === "payment" || entry.kind === "auto"
                                              ? "border-emerald-200 bg-emerald-50"
                                              : entry.type === "audit"
                                                ? "border-blue-100 bg-blue-50/50"
                                                : "border-slate-200 bg-slate-50"
                                    }`}
                                >
                                    <p className="font-medium text-slate-800">{entry.title}</p>
                                    {entry.note && <p className="mt-0.5 text-slate-600">{entry.note}</p>}
                                    <p className="mt-1 text-xs text-slate-400">
                                        {entry.actor} · {formatDate(entry.at)}
                                    </p>
                                </li>
                            ))}
                        </ul>
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

export default AdminOrderDetailPage;
