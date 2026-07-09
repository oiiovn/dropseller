import React, { useEffect, useMemo, useState } from "react";
import { NavLink, useLocation } from "react-router";
import {
    buildNavTarget,
    filterNavigation,
    findActiveGroupIds,
    isNavItemActive,
} from "../lib/navigation";

function NavItem({ item, onNavigate, pathname, searchParams }) {
    const active = isNavItemActive(item, pathname, searchParams);
    const target = buildNavTarget(item);

    return (
        <NavLink
            to={target}
            end={item.exact ?? false}
            onClick={onNavigate}
            className={() =>
                `sidebar-nav__link ${active ? "sidebar-nav__link--active" : "sidebar-nav__link--child"}`
            }
        >
            {item.label}
        </NavLink>
    );
}

export default function GroupedSidebarNav({ menu, roleNames, onNavigate }) {
    const location = useLocation();
    const searchParams = useMemo(() => new URLSearchParams(location.search), [location.search]);
    const filteredMenu = useMemo(() => filterNavigation(menu, roleNames), [menu, roleNames]);
    const activeGroupIds = useMemo(
        () => findActiveGroupIds(filteredMenu, location.pathname, searchParams),
        [filteredMenu, location.pathname, searchParams]
    );

    const [openGroups, setOpenGroups] = useState(() => new Set(activeGroupIds));

    useEffect(() => {
        setOpenGroups((current) => {
            const next = new Set(current);
            activeGroupIds.forEach((id) => next.add(id));
            return next;
        });
    }, [activeGroupIds]);

    const toggleGroup = (groupId) => {
        setOpenGroups((current) => {
            const next = new Set(current);
            if (next.has(groupId)) {
                next.delete(groupId);
            } else {
                next.add(groupId);
            }
            return next;
        });
    };

    return (
        <nav className="sidebar-nav space-y-1">
            {filteredMenu.map((entry) => {
                if (entry.type === "link") {
                    return (
                        <NavItem
                            key={entry.id}
                            item={entry}
                            onNavigate={onNavigate}
                            pathname={location.pathname}
                            searchParams={searchParams}
                        />
                    );
                }

                const isOpen = openGroups.has(entry.id);
                const groupActive = entry.children.some((child) =>
                    isNavItemActive(child, location.pathname, searchParams)
                );

                return (
                    <div key={entry.id} className="sidebar-nav__group">
                        <button
                            type="button"
                            className={`sidebar-nav__group-toggle ${groupActive ? "sidebar-nav__group-toggle--active" : ""}`}
                            onClick={() => toggleGroup(entry.id)}
                            aria-expanded={isOpen}
                        >
                            <span>{entry.label}</span>
                            <svg
                                className={`sidebar-nav__chevron ${isOpen ? "sidebar-nav__chevron--open" : ""}`}
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                aria-hidden="true"
                            >
                                <path
                                    fillRule="evenodd"
                                    d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z"
                                    clipRule="evenodd"
                                />
                            </svg>
                        </button>
                        {isOpen && (
                            <div className="sidebar-nav__children">
                                {entry.children.map((child) => (
                                    <NavItem
                                        key={child.id}
                                        item={child}
                                        onNavigate={onNavigate}
                                        pathname={location.pathname}
                                        searchParams={searchParams}
                                    />
                                ))}
                            </div>
                        )}
                    </div>
                );
            })}
        </nav>
    );
}
