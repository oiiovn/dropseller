import React from "react";
import { Link } from "react-router";

export default function NoPermissionPage() {
    return (
        <section className="crm-no-role-page">
            <div className="crm-no-role-card crm-no-role-card--denied">
                <div className="crm-no-role-card__icon crm-no-role-card__icon--denied" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" className="h-8 w-8">
                        <path
                            d="M12 15v2m-6 4h12a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2Zm10-10V7a4 4 0 0 0-8 0v4h8Z"
                            stroke="currentColor"
                            strokeWidth="1.8"
                            strokeLinecap="round"
                            strokeLinejoin="round"
                        />
                    </svg>
                </div>

                <h2 className="crm-no-role-card__title">Không có quyền truy cập</h2>
                <p className="crm-no-role-card__lead">
                    Tài khoản của bạn không được phép xem trang này. Vui lòng chọn chức năng khác từ menu hoặc quay
                    về Dashboard.
                </p>

                <div className="crm-no-role-card__actions">
                    <Link to="/" className="crm-btn-primary">
                        Về Dashboard
                    </Link>
                </div>
            </div>
        </section>
    );
}
