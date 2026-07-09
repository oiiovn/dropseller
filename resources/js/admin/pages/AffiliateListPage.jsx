import React from "react";
import { Link } from "react-router";
import { formatJpy } from "../lib/api";
import { affiliateRegionLabel } from "../lib/affiliateRegions";
import EntityListPage from "../components/EntityListPage";

export default function AffiliateListPage() {
    return (
        <EntityListPage
            title="Danh sách cộng tác viên"
            endpoint="/affiliates"
            action={
                <Link to="/users" className="admin-btn-primary">
                    Gán CTV
                </Link>
            }
            columns={[
                { key: "code", label: "Mã CTV" },
                { key: "full_name", label: "Họ tên" },
                { key: "phone", label: "Điện thoại" },
                {
                    key: "region",
                    label: "Khu vực",
                    render: (row) => affiliateRegionLabel(row.region),
                },
                { key: "area", label: "Chi tiết KV" },
                { key: "status", label: "Trạng thái" },
                {
                    key: "gross_sales_jpy",
                    label: "Doanh số",
                    render: (row) => formatJpy(row.gross_sales_jpy),
                },
            ]}
        />
    );
}
