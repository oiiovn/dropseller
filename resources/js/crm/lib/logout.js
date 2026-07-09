import { readCsrf } from "./api";

export function submitLogout() {
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
}
