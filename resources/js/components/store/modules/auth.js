import { storeUserSession, deleteUserSession } from "../../utils/helper";
import router from "../../helpers/router";

const user = JSON.parse(localStorage.getItem("user"));
const initialState = user
    ? {
          status: { loggedIn: true },
          user,
          favorites: user?.property_favorites || [],
      }
    : { status: {}, user: null, favorites: [] };

export default {
    namespaced: true,
    state: initialState,
    getters: {
        favorites: (state) => state.favorites,
    },
    actions: {
        _userLogin({ dispatch, commit }, { email, password }) {
            commit("loginRequest", { email });
            /* axios
                .post("/api/user/signin", { email, password })
                .then(response => {
                    commit("loginSuccess", response.data);
                    //router.push("/builders");

                })
                .catch(error => {
                    const errMessage = getErrorMessage(error);
                    commit("loginFailure", errMessage);
                    dispatch("alert/error", errMessage, { root: true });
                }); */
        },
        userLogin({ dispatch, commit }, { response }) {
            // console.log(response.data)
            commit("loginSuccess", response.data);
            router.push("/");
        },
        /* userFailure({dispatch, commit}, {err}){
            commit("loginFailure", err);
        }, */
        userLogout({ commit }) {
            deleteUserSession();
            commit("logout");
            if (router.currentRoute.path !== "/") {
                router.push("/");
            }
        },
        setUserData({ commit }, user) {
            user.last_fetched_at = new Date().getTime();
            localStorage.setItem("user", JSON.stringify(user));
            commit("SET_USER_DATA", user);
        },
    },
    mutations: {
        loginRequest(state, user) {
            state.status = { loggingIn: true };
            state.user = user;
        },
        loginSuccess(state, data) {
            state.status = { loggedIn: true };
            state.user = data.user;
            state.favorites = data.user.property_favorites;
            storeUserSession(data);
        },
        loginFailure(state) {
            state.status = {};
            state.user = null;
            state.favorites = [];
        },
        logout(state) {
            state.status = {};
            state.user = null;
            state.favorites = [];
        },
        updateFavorites(state, data) {
            state.favorites = data;
        },
        SET_USER_DATA(state, user) {
            state.user = user;
        },
    },
};
