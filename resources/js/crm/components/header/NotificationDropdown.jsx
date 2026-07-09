import React, { useCallback, useEffect, useState } from "react";
import Dropdown from "../ui/Dropdown";
import { api as defaultApi } from "../../lib/api";

const CATEGORY_STYLES = {
    info: "border-l-blue-400 bg-blue-50/40",
    success: "border-l-emerald-400 bg-emerald-50/40",
    warning: "border-l-amber-400 bg-amber-50/40",
    danger: "border-l-red-400 bg-red-50/40",
};

function formatRelativeTime(value) {
    if (!value) {
        return "";
    }

    const date = new Date(value);
    const diffMs = Date.now() - date.getTime();
    const diffMinutes = Math.floor(diffMs / 60000);

    if (diffMinutes < 1) {
        return "Vừa xong";
    }
    if (diffMinutes < 60) {
        return `${diffMinutes} phút trước`;
    }

    const diffHours = Math.floor(diffMinutes / 60);
    if (diffHours < 24) {
        return `${diffHours} giờ trước`;
    }

    const diffDays = Math.floor(diffHours / 24);
    if (diffDays < 7) {
        return `${diffDays} ngày trước`;
    }

    return date.toLocaleDateString("vi-VN");
}

export default function NotificationDropdown({ apiClient = defaultApi }) {
    const [isOpen, setIsOpen] = useState(false);
    const [notifications, setNotifications] = useState([]);
    const [unreadCount, setUnreadCount] = useState(0);
    const [loading, setLoading] = useState(false);

    const fetchUnreadCount = useCallback(() => {
        apiClient
            .get("/notifications/unread-count")
            .then((response) => {
                setUnreadCount(response.data?.unread_count ?? 0);
            })
            .catch(() => setUnreadCount(0));
    }, [apiClient]);

    const fetchNotifications = useCallback(() => {
        setLoading(true);
        apiClient
            .get("/notifications", { params: { limit: 12 } })
            .then((response) => {
                setNotifications(response.data?.notifications || []);
                setUnreadCount(response.data?.unread_count ?? 0);
            })
            .catch(() => {
                setNotifications([]);
            })
            .finally(() => setLoading(false));
    }, [apiClient]);

    useEffect(() => {
        fetchUnreadCount();
        const interval = window.setInterval(fetchUnreadCount, 60000);
        return () => window.clearInterval(interval);
    }, [fetchUnreadCount]);

    useEffect(() => {
        if (isOpen) {
            fetchNotifications();
        }
    }, [isOpen, fetchNotifications]);

    const handleToggle = () => {
        setIsOpen((open) => !open);
    };

    const handleMarkRead = async (notification) => {
        if (notification.read_at) {
            return;
        }

        try {
            await apiClient.put(`/notifications/${notification.id}/read`);
            setNotifications((items) =>
                items.map((item) =>
                    item.id === notification.id ? { ...item, read_at: new Date().toISOString() } : item
                )
            );
            setUnreadCount((count) => Math.max(0, count - 1));
        } catch {
            // ignore
        }
    };

    const handleMarkAllRead = async () => {
        try {
            await apiClient.put("/notifications/read-all");
            setNotifications((items) => items.map((item) => ({ ...item, read_at: item.read_at || new Date().toISOString() })));
            setUnreadCount(0);
        } catch {
            // ignore
        }
    };

    const handleOpenNotification = async (notification) => {
        await handleMarkRead(notification);
        setIsOpen(false);

        if (notification.action_url) {
            window.location.href = notification.action_url;
        }
    };

    return (
        <div className="relative">
            <button
                type="button"
                onClick={handleToggle}
                className="relative flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-700"
                aria-label="Thông báo"
            >
                {unreadCount > 0 && (
                    <span className="absolute -right-0.5 -top-0.5 flex h-5 min-w-5 items-center justify-center rounded-full bg-orange-500 px-1 text-[14px] font-semibold text-white">
                        {unreadCount > 99 ? "99+" : unreadCount}
                    </span>
                )}
                <svg className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path
                        fillRule="evenodd"
                        d="M10.75 2.29248C10.75 1.87827 10.4143 1.54248 10 1.54248C9.58583 1.54248 9.25004 1.87827 9.25004 2.29248V2.83613C6.08266 3.20733 3.62504 5.9004 3.62504 9.16748V14.4591H3.33337C2.91916 14.4591 2.58337 14.7949 2.58337 15.2091C2.58337 15.6234 2.91916 15.9591 3.33337 15.9591H4.37504H15.625H16.6667C17.0809 15.9591 17.4167 15.6234 17.4167 15.2091C17.4167 14.7949 17.0809 14.4591 16.6667 14.4591H16.375V9.16748C16.375 5.9004 13.9174 3.20733 10.75 2.83613V2.29248ZM14.875 14.4591V9.16748C14.875 6.47509 12.6924 4.29248 10 4.29248C7.30765 4.29248 5.12504 6.47509 5.12504 9.16748V14.4591H14.875ZM8.00004 17.7085C8.00004 18.1228 8.33583 18.4585 8.75004 18.4585H11.25C11.6643 18.4585 12 18.1228 12 17.7085C12 17.2943 11.6643 16.9585 11.25 16.9585H8.75004C8.33583 16.9585 8.00004 17.2943 8.00004 17.7085Z"
                        clipRule="evenodd"
                    />
                </svg>
            </button>

            <Dropdown
                isOpen={isOpen}
                onClose={() => setIsOpen(false)}
                className="absolute right-0 z-50 mt-2 w-80 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg sm:w-96"
            >
                <div className="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                    <h3 className="text-sm font-semibold text-slate-800">Thông báo</h3>
                    {unreadCount > 0 && (
                        <button
                            type="button"
                            onClick={handleMarkAllRead}
                            className="text-xs font-medium text-blue-600 hover:text-blue-700"
                        >
                            Đánh dấu đã đọc
                        </button>
                    )}
                </div>

                <ul className="max-h-80 overflow-y-auto">
                    {loading && (
                        <li className="px-4 py-8 text-center text-sm text-slate-500">Đang tải...</li>
                    )}
                    {!loading &&
                        notifications.map((notification) => {
                            const isUnread = !notification.read_at;
                            const categoryClass = CATEGORY_STYLES[notification.category] || CATEGORY_STYLES.info;

                            return (
                                <li key={notification.id}>
                                    <button
                                        type="button"
                                        onClick={() => handleOpenNotification(notification)}
                                        className={`w-full border-b border-slate-50 px-4 py-3 text-left transition hover:bg-slate-50 border-l-4 ${categoryClass} ${
                                            isUnread ? "bg-white" : "opacity-80"
                                        }`}
                                    >
                                        <div className="flex items-start justify-between gap-2">
                                            <p className={`text-sm ${isUnread ? "font-semibold text-slate-900" : "font-medium text-slate-700"}`}>
                                                {notification.title}
                                            </p>
                                            {isUnread && <span className="mt-1 h-2 w-2 shrink-0 rounded-full bg-orange-500" />}
                                        </div>
                                        <p className="mt-1 text-xs leading-relaxed text-slate-500">{notification.message}</p>
                                        <p className="mt-1.5 text-[15px] text-slate-400">
                                            {formatRelativeTime(notification.created_at)}
                                        </p>
                                    </button>
                                </li>
                            );
                        })}
                    {!loading && notifications.length === 0 && (
                        <li className="px-4 py-8 text-center text-sm text-slate-500">Không có thông báo mới.</li>
                    )}
                </ul>
            </Dropdown>
        </div>
    );
}
