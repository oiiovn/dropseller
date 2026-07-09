import React, { useCallback, useEffect, useState } from "react";
import { adminApi } from "../lib/api";
import { useAuth } from "../context/AuthContext";
import { AFFILIATE_REGIONS, affiliateRegionLabel } from "../lib/affiliateRegions";
import TablePagination from "../../shared/components/TablePagination";
import AssignCollaboratorModal from "../components/AssignCollaboratorModal";

const SEARCH_DEBOUNCE_MS = 300;

const ROLE_OPTIONS = [
    { name: "admin", label: "Quản trị viên" },
    { name: "staff", label: "Nhân sự nội bộ" },
    { name: "collaborator", label: "Cộng tác viên" },
    { name: "packaging", label: "Đóng gói vận chuyển" },
    { name: "accounting", label: "Kế toán" },
];

function UserEditModal({ user, onClose, onSaved }) {
    const [form, setForm] = useState({
        name: user.name || "",
        email: user.email || "",
        phone: user.phone || "",
        password: "",
        role_names: (user.crm_roles || []).map((role) => role.name),
        affiliate_region: user.affiliate?.region || "",
    });
    const [saving, setSaving] = useState(false);
    const [error, setError] = useState("");

    const handleChange = (event) => {
        const { name, value } = event.target;
        setForm((current) => ({ ...current, [name]: value }));
    };

    const toggleRole = (roleName) => {
        setForm((current) => ({
            ...current,
            role_names: current.role_names.includes(roleName)
                ? current.role_names.filter((name) => name !== roleName)
                : [...current.role_names, roleName],
        }));
    };

    const handleSubmit = async (event) => {
        event.preventDefault();
        setSaving(true);
        setError("");
        try {
            const payload = {
                name: form.name,
                email: form.email,
                phone: form.phone || null,
                role_names: form.role_names,
            };
            if (form.password.trim()) {
                payload.password = form.password;
            }
            if (user.affiliate?.id) {
                payload.affiliate_region = form.affiliate_region || null;
            }
            await adminApi.put(`/users/${user.id}`, payload);
            onSaved();
        } catch (requestError) {
            setError(requestError.response?.data?.message || "Không cập nhật được tài khoản.");
        } finally {
            setSaving(false);
        }
    };

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4">
            <section className="admin-card w-full max-w-lg">
                <div className="mb-4 flex items-center justify-between">
                    <h3 className="text-lg font-semibold">Sửa tài khoản</h3>
                    <button type="button" className="text-sm text-slate-500 hover:text-slate-700" onClick={onClose}>
                        Đóng
                    </button>
                </div>

                <form className="space-y-4" onSubmit={handleSubmit}>
                    <label className="admin-field">
                        <span>Họ tên *</span>
                        <input name="name" value={form.name} onChange={handleChange} required />
                    </label>
                    <label className="admin-field">
                        <span>Email đăng nhập *</span>
                        <input type="email" name="email" value={form.email} onChange={handleChange} required />
                    </label>
                    <label className="admin-field">
                        <span>Điện thoại</span>
                        <input name="phone" value={form.phone} onChange={handleChange} />
                    </label>
                    <label className="admin-field">
                        <span>Mật khẩu mới</span>
                        <input
                            type="password"
                            name="password"
                            value={form.password}
                            onChange={handleChange}
                            minLength={8}
                            placeholder="Để trống nếu không đổi"
                        />
                    </label>

                    <div>
                        <p className="mb-2 text-sm font-medium text-slate-700">Vai trò</p>
                        <div className="flex flex-wrap gap-2">
                            {ROLE_OPTIONS.map((role) => (
                                <label
                                    key={role.name}
                                    className="inline-flex cursor-pointer items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-sm"
                                >
                                    <input
                                        type="checkbox"
                                        checked={form.role_names.includes(role.name)}
                                        onChange={() => toggleRole(role.name)}
                                    />
                                    {role.label}
                                </label>
                            ))}
                        </div>
                    </div>

                    {user.affiliate?.id && (
                        <label className="admin-field">
                            <span>Khu vực CTV *</span>
                            <select
                                name="affiliate_region"
                                value={form.affiliate_region}
                                onChange={handleChange}
                                required
                            >
                                <option value="">Chọn khu vực</option>
                                {AFFILIATE_REGIONS.map((region) => (
                                    <option key={region.value} value={region.value}>
                                        {region.label}
                                    </option>
                                ))}
                            </select>
                        </label>
                    )}

                    {error && <p className="text-sm text-red-600">{error}</p>}

                    <div className="flex justify-end gap-2">
                        <button type="button" className="admin-btn-secondary" onClick={onClose}>
                            Hủy
                        </button>
                        <button type="submit" className="admin-btn-primary" disabled={saving}>
                            {saving ? "Đang lưu..." : "Lưu thay đổi"}
                        </button>
                    </div>
                </form>
            </section>
        </div>
    );
}

export default function UserListPage() {
    const { user: currentUser } = useAuth();
    const [rows, setRows] = useState([]);
    const [loading, setLoading] = useState(true);
    const [editingUser, setEditingUser] = useState(null);
    const [assigningCollaborator, setAssigningCollaborator] = useState(null);
    const [actionError, setActionError] = useState("");

    const [meta, setMeta] = useState({ current_page: 1, last_page: 1, total: 0 });

    const [search, setSearch] = useState("");
    const [debouncedSearch, setDebouncedSearch] = useState("");
    const [roleFilter, setRoleFilter] = useState("");
    const [regionFilter, setRegionFilter] = useState("");
    const [affiliateFilter, setAffiliateFilter] = useState("");

    useEffect(() => {
        const timer = setTimeout(() => setDebouncedSearch(search), SEARCH_DEBOUNCE_MS);
        return () => clearTimeout(timer);
    }, [search]);

    const buildParams = useCallback(
        (page = 1) => {
            const params = { page, per_page: 15 };
            if (debouncedSearch.trim()) {
                params.search = debouncedSearch.trim();
            }
            if (roleFilter) {
                params.role = roleFilter;
            }
            if (regionFilter) {
                params.affiliate_region = regionFilter;
            }
            if (affiliateFilter === "linked") {
                params.affiliate_link = "linked";
            }
            if (affiliateFilter === "none") {
                params.affiliate_link = "none";
            }
            return params;
        },
        [debouncedSearch, roleFilter, regionFilter, affiliateFilter]
    );

    const load = useCallback(
        (page = 1) => {
            setLoading(true);
            adminApi
                .get("/users", { params: buildParams(page) })
                .then((response) => {
                    setRows(response.data?.data ?? []);
                    setMeta({
                        current_page: response.data?.current_page ?? 1,
                        last_page: response.data?.last_page ?? 1,
                        total: response.data?.total ?? 0,
                    });
                })
                .finally(() => setLoading(false));
        },
        [buildParams]
    );

    useEffect(() => {
        load(1);
    }, [load]);

    const clearFilters = () => {
        setSearch("");
        setRoleFilter("");
        setRegionFilter("");
        setAffiliateFilter("");
    };

    const hasActiveFilters = Boolean(search.trim() || roleFilter || regionFilter || affiliateFilter);

    const toggleRole = async (user, roleName) => {
        setActionError("");
        const current = (user.crm_roles || []).map((role) => role.name);
        const hasRole = current.includes(roleName);

        if (!hasRole && roleName === "collaborator" && !user.affiliate?.id) {
            setAssigningCollaborator(user);
            return;
        }

        const next = hasRole ? current.filter((name) => name !== roleName) : [...current, roleName];
        try {
            await adminApi.put(`/users/${user.id}`, { role_names: next });
            load(meta.current_page);
        } catch (requestError) {
            setActionError(requestError.response?.data?.message || "Không cập nhật được vai trò.");
        }
    };

    const handleDelete = async (user) => {
        if (user.id === currentUser?.id) {
            setActionError("Không thể xóa tài khoản đang đăng nhập.");
            return;
        }

        const confirmed = window.confirm(`Xóa tài khoản "${user.name}" (${user.email})?`);
        if (!confirmed) {
            return;
        }

        setActionError("");
        try {
            await adminApi.delete(`/users/${user.id}`);
            load(meta.current_page);
        } catch (requestError) {
            setActionError(requestError.response?.data?.message || "Không xóa được tài khoản.");
        }
    };

    return (
        <>
            <section className="data-table-page admin-card">
                <h2 className="data-table-page__header text-lg font-semibold">Người dùng & phân quyền</h2>

                <div className="data-table-page__filters crm-filter-bar">
                    <input
                        className="crm-filter-input"
                        placeholder="Tìm tên, email, SĐT, mã CTV..."
                        value={search}
                        onChange={(event) => setSearch(event.target.value)}
                    />
                    <select
                        className="crm-filter-select"
                        value={roleFilter}
                        onChange={(event) => setRoleFilter(event.target.value)}
                    >
                        <option value="">Vai trò</option>
                        {ROLE_OPTIONS.map((role) => (
                            <option key={role.name} value={role.name}>
                                {role.label}
                            </option>
                        ))}
                    </select>
                    <select
                        className="crm-filter-select"
                        value={regionFilter}
                        onChange={(event) => setRegionFilter(event.target.value)}
                    >
                        <option value="">Khu vực CTV</option>
                        {AFFILIATE_REGIONS.map((region) => (
                            <option key={region.value} value={region.value}>
                                {region.label}
                            </option>
                        ))}
                    </select>
                    <select
                        className="crm-filter-select"
                        value={affiliateFilter}
                        onChange={(event) => setAffiliateFilter(event.target.value)}
                    >
                        <option value="">Liên kết CTV</option>
                        <option value="linked">Đã gán CTV</option>
                        <option value="none">Chưa gán CTV</option>
                    </select>
                    {hasActiveFilters && (
                        <button type="button" className="crm-filter-clear" onClick={clearFilters}>
                            Xóa bộ lọc
                        </button>
                    )}
                </div>

                <div className="data-table-page__messages">
                    {actionError && <p className="text-sm text-red-600">{actionError}</p>}
                    {loading && <p className="text-sm text-slate-500">Đang tải...</p>}
                </div>

                {!loading && (
                    <>
                        <div className="data-table-page__body">
                            <table className="admin-table min-w-full">
                        <thead>
                            <tr>
                                <th>Tên</th>
                                <th>Email</th>
                                <th>Điện thoại</th>
                                <th>CTV</th>
                                <th>Khu vực</th>
                                <th>Vai trò</th>
                                <th>Thao tác nhanh</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            {rows.length === 0 && (
                                <tr>
                                    <td colSpan={8} className="py-6 text-center text-sm text-slate-500">
                                        Không có tài khoản phù hợp bộ lọc.
                                    </td>
                                </tr>
                            )}
                            {rows.map((user) => (
                                <tr key={user.id}>
                                    <td>{user.name}</td>
                                    <td>{user.email}</td>
                                    <td>{user.phone || "—"}</td>
                                    <td>{user.affiliate?.code || "—"}</td>
                                    <td>{affiliateRegionLabel(user.affiliate?.region)}</td>
                                    <td>{(user.crm_roles || []).map((role) => role.display_name).join(", ") || "—"}</td>
                                    <td className="whitespace-nowrap">
                                        <div className="flex flex-wrap gap-1">
                                        {ROLE_OPTIONS.map((role) => {
                                            const hasRole = (user.crm_roles || []).some((item) => item.name === role.name);
                                            return (
                                            <button
                                                key={role.name}
                                                type="button"
                                                className={`rounded border px-2 py-1 text-xs transition ${
                                                    hasRole
                                                        ? "border-blue-500 bg-blue-50 font-semibold text-blue-700 shadow-sm ring-1 ring-blue-200 hover:bg-blue-100"
                                                        : "border-slate-200 bg-white text-slate-600 hover:bg-slate-50"
                                                }`}
                                                onClick={() => toggleRole(user, role.name)}
                                            >
                                                {hasRole ? "Bỏ" : "Gán"} {role.label}
                                            </button>
                                            );
                                        })}
                                        </div>
                                    </td>
                                    <td className="space-x-2 whitespace-nowrap">
                                        <button
                                            type="button"
                                            className="text-sm text-blue-600 hover:underline"
                                            onClick={() => setEditingUser(user)}
                                        >
                                            Sửa
                                        </button>
                                        <button
                                            type="button"
                                            className="text-sm text-red-600 hover:underline disabled:cursor-not-allowed disabled:opacity-40"
                                            onClick={() => handleDelete(user)}
                                            disabled={user.id === currentUser?.id}
                                        >
                                            Xóa
                                        </button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                        </div>
                        <div className="data-table-page__footer">
                            <TablePagination
                                currentPage={meta.current_page}
                                lastPage={meta.last_page}
                                total={meta.total}
                                loading={loading}
                                onPageChange={load}
                                buttonClassName="admin-btn-secondary px-3 py-1.5 text-xs"
                            />
                        </div>
                    </>
                )}
            </section>

            {editingUser && (
                <UserEditModal
                    user={editingUser}
                    onClose={() => setEditingUser(null)}
                    onSaved={() => {
                        setEditingUser(null);
                        load(meta.current_page);
                    }}
                />
            )}

            {assigningCollaborator && (
                <AssignCollaboratorModal
                    user={assigningCollaborator}
                    onClose={() => setAssigningCollaborator(null)}
                    onSaved={() => {
                        setAssigningCollaborator(null);
                        load(meta.current_page);
                    }}
                />
            )}
        </>
    );
}
