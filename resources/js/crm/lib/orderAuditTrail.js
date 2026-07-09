import { ORDER_STATUS_LABELS, paymentStatusLabel, SHIPPING_MODE_LABELS } from "./orderStatuses";

const MODULE_LABELS = {
    orders: "Đơn hàng",
    payments: "Thanh toán",
};

const DEBT_STATUS_LABELS = {
    unpaid: "Chưa thu",
    partial: "Thu một phần",
    paid: "Đã thu",
    cleared: "Đã tất toán",
};

const AUDIT_EVENT_TITLES = {
    created: "Tạo đơn hàng mới",
    updated: "Cập nhật thông tin đơn",
    deleted: "Xóa mềm đơn hàng",
    restored: "Khôi phục đơn hàng",
};

const FIELD_LABELS = {
    order_status: "Trạng thái đơn",
    payment_status: "Thanh toán",
    debt_status: "Công nợ",
    notes: "Ghi chú",
    delivery_date: "Ngày giao",
    delivery_date_from: "Giao từ ngày",
    delivery_date_to: "Giao đến ngày",
    delivery_time_slot: "Khung giờ giao",
    payment_method: "Hình thức TT",
    shipping_mode: "Vận chuyển",
    battery_capacity: "Pin",
    facebook_url: "Facebook",
    shipping_fee_jpy: "Phí ship",
    total_amount_jpy: "Tổng tiền",
    subtotal_jpy: "Tiền hàng",
    confirmed_at: "Ngày chốt",
    order_code: "Mã đơn",
};

const INTERNAL_FIELDS = new Set([
    "id",
    "customer_id",
    "affiliate_id",
    "created_by",
    "updated_at",
    "created_at",
    "deleted_at",
    "subtotal_jpy",
    "total_cost_jpy",
    "total_profit_jpy",
    "discount_jpy",
    "items",
    "debt",
    "delivery",
    "customer",
    "affiliate",
    "payments",
    "status_histories",
    "audit_logs",
    "commission",
    "creator",
    "order_code",
]);

function isEmptyValue(value) {
    return value === null || value === undefined || value === "";
}

function isComplexValue(value) {
    return typeof value === "object" && value !== null;
}

function normalizeComparable(value) {
    if (isEmptyValue(value)) return "";
    if (typeof value === "boolean") return value ? "1" : "0";
    if (typeof value === "number") return String(value);
    return String(value).trim();
}

export function formatAuditFieldValue(key, value, formatJpy) {
    if (isEmptyValue(value)) return "—";
    if (isComplexValue(value)) return null;

    if (key === "order_status") {
        return ORDER_STATUS_LABELS[value] || value;
    }
    if (key === "payment_status") {
        return paymentStatusLabel(value);
    }
    if (key === "debt_status") {
        return DEBT_STATUS_LABELS[value] || value;
    }
    if (key.endsWith("_jpy")) {
        return formatJpy(value);
    }
    if (key === "confirmed_at" || key === "delivery_date" || key === "delivery_date_from" || key === "delivery_date_to") {
        return String(value).slice(0, 10);
    }
    if (key === "shipping_mode") {
        return SHIPPING_MODE_LABELS[value] || value;
    }

    return String(value);
}

function summarizeFieldChanges(log, formatJpy) {
    const oldValues = log.old_values || {};
    const newValues = log.new_values || {};
    const keys = [...new Set([...Object.keys(oldValues), ...Object.keys(newValues)])];

    const changes = [];

    for (const key of keys) {
        if (INTERNAL_FIELDS.has(key)) continue;
        if (log.event === "updated" && key === "order_status") continue;

        const oldRaw = oldValues[key];
        const newRaw = newValues[key];
        if (normalizeComparable(oldRaw) === normalizeComparable(newRaw)) continue;

        const label = FIELD_LABELS[key] || key;
        const from = formatAuditFieldValue(key, oldRaw, formatJpy);
        const to = formatAuditFieldValue(key, newRaw, formatJpy);
        if (from === null || to === null) continue;

        changes.push(`${label}: ${from} → ${to}`);
    }

    return changes;
}

function summarizeCreatedOrder(log, formatJpy) {
    const values = log.new_values || {};
    const parts = [];

    if (values.order_code) {
        parts.push(`Mã đơn ${values.order_code}`);
    }
    if (values.order_status) {
        parts.push(`Trạng thái ${ORDER_STATUS_LABELS[values.order_status] || values.order_status}`);
    }
    if (values.total_amount_jpy) {
        parts.push(`Tổng ${formatJpy(values.total_amount_jpy)}`);
    }

    return parts.length > 0 ? parts.join(" · ") : null;
}

export function formatAuditLogEntry(log, formatJpy) {
    if (log.module === "payments") {
        return null;
    }

    const title = AUDIT_EVENT_TITLES[log.event] || `[${log.event}] ${MODULE_LABELS[log.module] || log.module}`;

    let note = null;
    if (log.event === "created") {
        note = summarizeCreatedOrder(log, formatJpy);
    } else if (log.event === "deleted") {
        note = "Đơn hàng được ẩn khỏi danh sách, có thể khôi phục sau.";
    } else if (log.event === "restored") {
        note = "Đơn hàng đã hiển thị lại trong danh sách.";
    } else {
        const changes = summarizeFieldChanges(log, formatJpy);
        if (changes.length === 0) {
            return null;
        }
        note = changes.join(" · ");
    }

    return { title, note };
}

function shouldShowStatusNote(note, fromStatus, toStatus) {
    if (!note) return false;
    if (fromStatus === toStatus) return false;
    if (note.startsWith("Chuyển trạng thái:")) return false;
    if (note.startsWith("Chuyển bộ phận:")) return false;
    if (note.startsWith("Tự động chuyển") && note.includes("→")) return false;
    return true;
}

export function formatStatusHistoryEntry(history) {
    const from = history.from_status;
    const to = history.to_status;
    const isPayment =
        from === to &&
        (history.note?.startsWith("Ghi nhận") ||
            history.note?.startsWith("Gửi duyệt") ||
            history.note?.startsWith("Kế toán"));

    if (isPayment) {
        return {
            title: history.note,
            note: null,
            kind: "payment",
        };
    }

    if (!from && to) {
        return {
            title: `Khởi tạo đơn — ${ORDER_STATUS_LABELS[to] || to}`,
            note: history.note === "Đơn được tạo mới" ? null : history.note,
            kind: "status",
        };
    }

    if (history.note?.startsWith("Tự động chuyển")) {
        const toLabel = ORDER_STATUS_LABELS[to] || to;
        const afterMatch = history.note.match(/sau khi (.+)$/i);
        return {
            title: `Tự động chuyển sang ${toLabel}`,
            note: afterMatch ? `Sau khi ${afterMatch[1]}` : null,
            kind: "auto",
        };
    }

    if (history.note?.startsWith("Chuyển bộ phận:")) {
        const [departmentPart, ...rest] = history.note.split(" — ");
        const department = departmentPart.replace("Chuyển bộ phận:", "").trim();
        const toLabel = ORDER_STATUS_LABELS[to] || to;
        return {
            title: `Chuyển bộ phận ${department} → ${toLabel}`,
            note: rest.length > 0 ? rest.join(" — ") : null,
            kind: "handoff",
        };
    }

    return {
        title: `${ORDER_STATUS_LABELS[from] || from || "—"} → ${ORDER_STATUS_LABELS[to] || to}`,
        note: shouldShowStatusNote(history.note, from, to) ? history.note : null,
        kind: "status",
    };
}

export function buildOrderAuditTrail(order, formatJpy) {
    const statusItems = (order.status_histories || []).map((history) => {
        const formatted = formatStatusHistoryEntry(history);
        return {
            id: `status-${history.id}`,
            type: "status",
            kind: formatted.kind,
            at: history.created_at,
            actor: history.changer?.name || "Hệ thống",
            title: formatted.title,
            note: formatted.note,
        };
    });

    const auditItems = (order.audit_logs || [])
        .map((log) => {
            const formatted = formatAuditLogEntry(log, formatJpy);
            if (!formatted) return null;

            return {
                id: `audit-${log.id}`,
                type: "audit",
                kind: log.event,
                at: log.created_at,
                actor: log.user?.name || "Hệ thống",
                title: formatted.title,
                note: formatted.note,
            };
        })
        .filter(Boolean);

    return [...statusItems, ...auditItems].sort((a, b) => new Date(b.at || 0) - new Date(a.at || 0));
}
