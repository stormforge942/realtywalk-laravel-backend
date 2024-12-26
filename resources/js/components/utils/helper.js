import Cookies from "js-cookie";

export const getErrorMessage = errObj => {
    const errResponse = errObj.response || null;
    const errorMessage =
        errResponse && errResponse.data.error
            ? errResponse.data.error.message
            : errObj.statusText
                ? "Something went wrong Please try again"
                : "Something went wrong Please try again";
    return errorMessage;
};

export const scrollTop = () => {
    window.scrollTo({
        top: 0,
        behavior: "smooth"
    });
};

export const scrollToId = (id) => {
    document.getElementById(id).scrollIntoView()
}

export const storeUserSession = ({ token, user }) => {
    Cookies.set("access-token", token);
    user.last_fetched_at = new Date().getTime();
    localStorage.setItem("user", JSON.stringify(user));
};

export const deleteUserSession = () => {
    Cookies.remove("access-token");
    localStorage.removeItem("user");
};

export const updateStorageValue = (key, value) => {
    const record = localStorage.getItem("user");

    if (record) {
        const existing = JSON.parse(record);
        existing[key] = value;
        existing.last_fetched_at = new Date().getTime();
        localStorage.setItem("user", JSON.stringify(existing));
    }
};

export const propertyStatusColor = (status) => {
    const actives = ['active', 'completed'];
    const pendings = ['pending', 'pending continue to show', 'option pending'];
    const invalids = ['sold', 'withdrawn', 'terminated', 'expired'];

    return {
        'badge-success': actives.includes(status ? status.toLowerCase() : null),
        'badge-warning': pendings.includes(status ? status.toLowerCase() : null),
        'badge-danger': invalids.includes(status ? status.toLowerCase() : null),
        'badge-info': ![...actives, ...pendings, ...invalids].includes(status ? status.toLowerCase() : null),
    }
}
