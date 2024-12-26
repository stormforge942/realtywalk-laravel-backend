import Vue from "vue";
import VueRouter from "vue-router";

import HomePage from "../HomePage/HomePage";
import About from "../About/About";
import Builders from "../Builders/Builders";
import SingleProperty from "../Property/SingleProperty/SingleProperty";
import AddressLookup from "@/pages/address_lookup";
import Register from "../../components/auth/Register/Register";
import Login from "../../components/auth/Login/Login";
import MagicLogin from "../../components/auth/Login/MagicLogin";
import ActivateAccount from "../../components/auth/Login/ActivateAccount";
import UserProfile from "../UserProfile/UserProfile";
import ListUserFavorites from "../../components/Favorite/ListUserFavorites/ListUserFavorites";
import ForgotPassword from "../auth/ForgotPassword/ForgotPassword";
import ResetPassword from "../auth/ResetPassword/ResetPassword";
import SingleNeighborhood from "../Neighborhoods/SingleNeighborhood/SingleNeighborhood";
import ReportBug from "../ReportBug";

import store from "../store";
import { deleteUserSession } from "../utils/helper";

Vue.use(VueRouter);

// routers
const routes = [
    {
        path: "/",
        name: "Home Page",
        component: HomePage,
        meta: { title: "Home - Realty WALK" },
    },
    {
        path: "/about",
        name: "About",
        component: About,
        meta: { title: "About Us" },
    },
    {
        path: "/builders",
        name: "Builders List",
        component: Builders,
        meta: { title: "Builders List - Realty WALK" },
    },
    {
        path: "/neighborhoods",
        name: "Neigborhoods List",
        component: Builders,
        meta: { title: "Neigborhoods List - Realty WALK" },
    },
    {
        path: "/address_lookup",
        name: "Address Lookup",
        component: AddressLookup,
        meta: { title: "Address Lookup - Realty WALK" },
    },
    {
        path: "/property/:path(.*)",
        name: "Single Property",
        component: SingleProperty,
        // meta: { title: "Single Property - Realty WALK" }
        meta: { title: "Realty WALK" },
    },
    {
        path: "/neighborhood/:path(.*)",
        name: "Neighborhood Profile",
        component: SingleNeighborhood,
        // meta: { title: "Neighborhood Profile - Realty WALK" }
        meta: { title: "Realty WALK" },
    },
    {
        path: "/users/create",
        name: "Register user",
        component: Register,
        meta: { title: "New user - Realty WALK" },
    },
    {
        path: "/users/signin",
        name: "User sign in",
        component: MagicLogin,
        meta: { title: "User Sign in - Realty WALK" },
    },
    {
        path: "/magic-login/:token",
        name: "Magic Login Attempt",
        component: MagicLogin,
        meta: { title: "Magic Login Attempt - Realty WALK" },
    },
    {
        path: "/activate/:token",
        name: "Activate Account",
        component: ActivateAccount,
        meta: { title: "Activate Account - Realty WALK" },
    },
    {
        path: "/users/signin-with-password",
        name: "User sign in",
        component: Login,
        meta: { title: "User Sign in with Password - Realty WALK" },
    },
    {
        path: "/users/favorites",
        name: "User Favorites",
        component: ListUserFavorites,
        meta: { title: "Favorites - Realty WALK", authRequired: true },
    },
    {
        path: "/users/profile",
        name: "User Profile",
        component: UserProfile,
        meta: { title: "User Profile - Realty WALK", authRequired: true },
    },
    {
        path: "/users/forgot-password",
        name: "Forgot Password",
        component: ForgotPassword,
        meta: { title: "Forgot Password - Realty WALK" },
    },
    {
        path: "/user/password-reset/:token",
        name: "Reset Password",
        component: ResetPassword,
        meta: { title: "Reset Password - Realty WALK" },
    },
    {
        path: "/report-bug",
        name: "Report Bug",
        component: ReportBug,
        meta: { tutle: "Report Bug - Realty WALK" },
    },
    {
        path: "/system*",
    },
    {
        path: "*",
        name: "Not Found",
        meta: { title: "Page Not Found - Realty WALK" },
    },
];

const router = new VueRouter({
    mode: "history",
    routes,
    // Title Tag
    linkActiveClass: "active-link",
    linkExactActiveClass: "exact-active-link",
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition;
        } else {
            return { x: 0, y: 0 };
        }
    }
});

// Flag to track if it's the initial page load
let isInitialPageLoad = true;

router.beforeEach(async (to, from, next) => {
    const authRequired = to?.meta?.authRequired;
    const loggedIn = !!localStorage.getItem("user");

    if (loggedIn) {
        const user = JSON.parse(localStorage.getItem("user"));
        const lastFetchedAt = user.last_fetched_at;
        const now = new Date().getTime();
        const fiveMinutes = 5 * 60 * 1000;
        const shouldRefreshUserData =
            now - parseInt(lastFetchedAt) > fiveMinutes;

        if (isInitialPageLoad || !lastFetchedAt || shouldRefreshUserData) {
            try {
                const { data } = await axios.get("/api/user/my-profile");
                if (data) {
                    store.dispatch("auth/setUserData", data);
                }
            } catch (err) {
                console.log(err);
                deleteUserSession();
            }
        }
    }

    if (authRequired && !loggedIn) {
        return next("/users/signin");
    }

    /* It will change the title when the router is change*/
    if (to?.meta?.title) {
        document.title = to.meta.title;
    }

    next();
    // End title tag code
});

router.afterEach((to, from) => {
    isInitialPageLoad = false;
});

export default router;
