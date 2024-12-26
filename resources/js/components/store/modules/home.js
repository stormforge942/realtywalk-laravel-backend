import _ from "lodash";

const STORAGE_NAME = "homescreen_history";
const DATA = {
  showHomeMap: true,
  viewCriteria: false,
  activePanel: "map",
  showNeighborhoodPanel: false,
  toggles: {
    floodPlanes: false,
    schoolZones: false,
    bikeTrails: false,
    showUnselectedAreas: false,
    showInstructions: true,
    showToggleMessage: false,
    initialToggle: true,
    activeSchoolZones: "elementary",
    activeFloodPlanes: null,
    activeNeighborhood: "",
  },
  filters: {
    minimumPrice: null,
    maximumPrice: null,
    listingStatus: "any",
    selectedPropertyTypes: [],
    bedroomsMin: "any",
    bathrooms: "any",
    bathroomsHalf: "any",
    squareFeetMin: "any",
    squareFeetMax: "any",
    garageOptions: "any",
    storiesMin: "any",
    storiesMax: "any",
    hasPool: false,
    hasElevator: false,
  },
  map: {
    center: {
      lat: 29.757224,
      lng: -95.360641,
    },
    zoom: 10,
  },
  property: {
    displaying: "map",
    map: {
      center: {
        lat: 29.760427,
        lng: -95.369803,
      },
      zoom: 10,
      selected_polygons: null,
    },
    list: {
      sortToggles: {
        price_from: false,
        address: false,
        neighborhood: false,
      },
    }
  },
};

function deepClone(obj) {
    return JSON.parse(JSON.stringify(obj));
}

export default {
  namespaced: true,
  state: {
    master: deepClone(DATA),
    data: JSON.parse(localStorage.getItem(STORAGE_NAME)) || deepClone(DATA),
  },
  getters: {
    getData: state => {
      return state.data;
    }
  },
  actions: {
    saveHistory({ commit }, payload) {
      commit("SET_HISTORY", payload);
    },
    resetHistory({ commit }) {
      commit("RESET_HISTORY");
    },
    clearFilters({ commit }) {
      commit("CLEAR_FILTERS");
    },
  },
  mutations: {
    SET_HISTORY(state, payload) {
      const hasSameKeys = (a, b) => {
        let aKeys = Object.keys(a).sort();
        let bKeys = Object.keys(b).sort();
        return JSON.stringify(aKeys) === JSON.stringify(bKeys);
      };

      const flattenObject = (ob) => {
        let result = {};
        for (const i in ob) {
          if (typeof ob[i] === "object" && !Array.isArray(ob[i])) {
            const temp = flattenObject(ob[i]);
            for (const j in temp) {
              result[i + "." + j] = temp[j];
            }
          } else {
            result[i] = ob[i];
          }
        }
        return result;
      };

      _.merge(state.data, deepClone(payload));
      localStorage.setItem(STORAGE_NAME, JSON.stringify(state.data));
    },
    RESET_HISTORY (state) {
      state.data = { ...state.master };
      localStorage.setItem(STORAGE_NAME, JSON.stringify(state.master));
    },
    CLEAR_FILTERS(state) {
      state.data.filters = { ...state.master.filters };
      localStorage.setItem(STORAGE_NAME, JSON.stringify(state.data));
    },
  },
};
