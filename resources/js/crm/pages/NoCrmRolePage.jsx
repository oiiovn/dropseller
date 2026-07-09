import React from "react";
import { useAuth } from "../context/AuthContext";
import { submitLogout } from "../lib/logout";

export default function NoCrmRolePage() {
    const { user } = useAuth();

    return (
        <section className="crm-no-role-page">
            <div className="crm-no-role-card">
                <div className="crm-no-role-card__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" className="h-8 w-8">
                        <path
                            d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"
                            stroke="currentColor"
                            strokeWidth="1.8"
                            strokeLinecap="round"
                            strokeLinejoin="round"
                        />
                    </svg>
                </div>

                <h2 className="crm-no-role-card__title">Tài khoản chưa gán role CRM</h2>
                <p className="crm-no-role-card__lead">
                    Tài khoản của bạn đã đăng nhập thành công nhưng chưa được phân quyền truy cập hệ thống Himawari CRM.
                    Vui lòng liên hệ quản trị viên để được gán vai trò phù hợp.
                </p>

                <div className="crm-no-role-card__info">
                    <div>
                        <p className="crm-no-role-card__label">Họ tên</p>
                        <p className="crm-no-role-card__value">{user?.name || "—"}</p>
                    </div>
                    <div>
                        <p className="crm-no-role-card__label">Email đăng nhập</p>
                        <p className="crm-no-role-card__value">{user?.email || "—"}</p>
                    </div>
                </div>

                <div className="crm-no-role-card__note">
                    <p className="font-medium text-amber-900">Bạn chưa thể sử dụng hệ thống CRM.</p>
                    <p className="mt-1 text-amber-800/90">
                        Sau khi được gán vai trò (CTV, Nhân sự, Kế toán, Đóng gói hoặc Quản trị), hãy đăng nhập lại
                        để truy cập các chức năng tương ứng.
                    </p>
                </div>

                <div className="crm-no-role-card__actions">
                    <button type="button" className="crm-btn-secondary" onClick={submitLogout}>
                        Đăng xuất
                    </button>
                </div>
            </div>
        </section>
    );
}
