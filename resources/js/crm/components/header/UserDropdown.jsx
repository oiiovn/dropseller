import React, { useState } from "react";
import Dropdown from "../ui/Dropdown";
import { readCsrf } from "../../lib/api";

function UserAvatar({ name, image }) {
    if (image) {
        return <img src={image} alt={name} className="h-10 w-10 rounded-full object-cover" />;
    }

    const initial = (name || "U").charAt(0).toUpperCase();

    return (
        <span className="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-sm font-semibold text-blue-700">
            {initial}
        </span>
    );
}

export default function UserDropdown({ user }) {
    const [isOpen, setIsOpen] = useState(false);

    const handleLogout = () => {
        const form = document.createElement("form");
        form.method = "POST";
        form.action = "/logout";

        const token = document.createElement("input");
        token.type = "hidden";
        token.name = "_token";
        token.value = readCsrf();
        form.appendChild(token);

        document.body.appendChild(form);
        form.submit();
    };

    return (
        <div className="relative">
            <button
                type="button"
                onClick={() => setIsOpen((open) => !open)}
                className="flex items-center gap-2 rounded-lg px-2 py-1.5 text-left transition hover:bg-slate-100"
            >
                <UserAvatar name={user?.name} image={user?.image} />
                <span className="hidden sm:block">
                    <span className="block text-sm font-medium text-slate-800">{user?.name || "Đang tải..."}</span>
                    <span className="block text-xs text-slate-500">{user?.roles?.[0] || "Nhân viên"}</span>
                </span>
                <svg
                    className={`hidden h-4 w-4 text-slate-400 transition sm:block ${isOpen ? "rotate-180" : ""}`}
                    viewBox="0 0 20 20"
                    fill="currentColor"
                >
                    <path
                        fillRule="evenodd"
                        d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z"
                        clipRule="evenodd"
                    />
                </svg>
            </button>

            <Dropdown
                isOpen={isOpen}
                onClose={() => setIsOpen(false)}
                className="absolute right-0 z-50 mt-2 w-64 rounded-xl border border-slate-200 bg-white p-3 shadow-lg"
            >
                <div className="border-b border-slate-100 pb-3">
                    <p className="text-sm font-semibold text-slate-800">{user?.name}</p>
                    <p className="text-xs text-slate-500">{user?.email}</p>
                    {user?.phone && <p className="mt-1 text-xs text-slate-500">{user.phone}</p>}
                </div>

                <ul className="py-2 text-sm text-slate-700">
                    <li className="rounded-lg px-3 py-2">
                        <span className="text-xs uppercase tracking-wide text-slate-400">Tiền tệ</span>
                        <p className="font-medium">{user?.currency === "JPY" || !user?.currency ? "円" : user.currency}</p>
                    </li>
                    <li className="rounded-lg px-3 py-2">
                        <span className="text-xs uppercase tracking-wide text-slate-400">Vai trò</span>
                        <p className="font-medium">{(user?.roles || []).join(", ") || "Chưa gán"}</p>
                    </li>
                </ul>

                <button
                    type="button"
                    onClick={handleLogout}
                    className="mt-1 flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-red-600 transition hover:bg-red-50"
                >
                    <svg className="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path
                            fillRule="evenodd"
                            d="M3 4.25A2.25 2.25 0 015.25 2h5.5A2.25 2.25 0 0113 4.25v2a.75.75 0 01-1.5 0v-2a.75.75 0 00-.75-.75h-5.5a.75.75 0 00-.75.75v11.5c0 .414.336.75.75.75h5.5a.75.75 0 00.75-.75v-2a.75.75 0 011.5 0v2A2.25 2.25 0 0110.75 18h-5.5A2.25 2.25 0 013 15.75V4.25z"
                            clipRule="evenodd"
                        />
                        <path
                            fillRule="evenodd"
                            d="M19.03 9.97a.75.75 0 00-1.06 0l-2.72 2.72V6.75a.75.75 0 00-1.5 0v5.94l-2.72-2.72a.75.75 0 00-1.06 1.06l4 4a.75.75 0 001.06 0l4-4a.75.75 0 000-1.06z"
                            clipRule="evenodd"
                        />
                    </svg>
                    Đăng xuất
                </button>
            </Dropdown>
        </div>
    );
}
