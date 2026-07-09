export const AFFILIATE_REGIONS = [
    { value: "japan", label: "Nhật Bản" },
    { value: "vietnam", label: "Việt Nam" },
];

export const affiliateRegionLabel = (region) =>
    AFFILIATE_REGIONS.find((item) => item.value === region)?.label || region || "—";
