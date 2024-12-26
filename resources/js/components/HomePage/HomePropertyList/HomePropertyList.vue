<template>
  <div class="home-property-list px-0">
    <div v-if="isLoading && homeData.property.displaying === 'list'" class="loading-state">
      <div class="lds-ripple">
        <div></div>
        <div></div>
      </div>
    </div>

    <div v-else class="loaded-content">
      <div v-if="properties == null && (!mapProperties.data.length && !mapProperties.loading && !mapProperties.reloading)" class="form">
        <h4>{{ $t("home.result.text_initial") }}</h4>
        <button @click.prevent="search()">
          {{ $t("home.result.btn_search") }}
        </button>
      </div>
      <div v-else-if="(properties && properties.length > 0)" class="loaded-content-inner">
        <div class="display-option-tabs">
          <button
            :class="[homeData.property.displaying === 'map' ? 'active' : '']"
            @click="setViewAs('map')"
          >
            {{ $t("home.result.btn_map_view") }}
          </button>

          <button
            :class="[homeData.property.displaying === 'list' ? 'active' : '']"
            @click="setViewAs('list')"
          >
            {{ $t("home.result.btn_list_view") }}
          </button>
        </div>

        <h4>{{ totalProperties }} records found.</h4>

        <div v-show="homeData.property.displaying === 'list'" class="property-list-results">
          <div class="d-none d-lg-block">
            <HomeSortMenu
              type="lg"
              :toggles="homeData.property.list.sortToggles"
              :setSort="setSort"
            />
            <HomePropertyListItem
              v-for="item in properties"
              :item="item"
              :key="`lg-${item.pri || item.id}`"
              :isLoggedIn="isLoggedIn"
            />
          </div>
          <div class="d-block d-lg-none">
            <HomeSortMenu
              type="sm"
              :toggles="homeData.property.list.sortToggles"
              :setSort="setSort"
            />
            <HomePropertyListSm
              v-for="property in properties"
              :key="`sm-${property.pri || property.id}`"
              :property="property"
              :isLoggedIn="isLoggedIn"
            />
          </div>
          <infinite-loading @distance="10" @infinite="infiniteHandler">
            <div slot="spinner">
              {{ $t("home.result.infinite.loading") }}
            </div>
            <div slot="no-more">
              {{ $t("home.result.infinite.no_more") }}
            </div>
            <div slot="no-results">
              {{ $t("home.result.infinite.no_more") }}
            </div>
          </infinite-loading>
        </div>
        <div :class="[homeData.property.displaying === 'map' ? 'active' : '']" class="property-map-results">
          <HomePropertyMap
            :loading="mapProperties.loading"
            :reloading="mapProperties.reloading"
            :properties="mapProperties.data"
            :has-polygons="hasPolygons"
            @init="mapPropertiesInit"
            @update="mapPropertiesOnMapUpdate"
          />
        </div>
      </div>
      <h4 v-else-if="!mapProperties.loading && !mapProperties.reloading">{{ $t("home.result.no_data") }}</h4>
    </div>
  </div>
</template>

<script>
import { EventBus } from "../../helpers";
import { getErrorMessage } from "../../utils/helper";
import { mapActions, mapState } from 'vuex';
import HomePropertyListItem from "./HomePropertyListItem/HomePropertyListItem";
import HomePropertyListSm from "./HomePropertyListSm/HomePropertyListSm";
import HomePropertyMap from "./HomePropertyMap/HomePropertyMap";
import HomeSortMenu from "../HomeSortMenu/HomeSortMenu";

export default {
  name: "HomePropertyList",
  props: ["isLoggedIn", "isActive"],
  components: {
    HomePropertyListItem,
    HomePropertyListSm,
    HomeSortMenu,
    HomePropertyMap,
  },
  data() {
    return {
      isPreLoading: false,
      isLoading: false,
      properties: null,
      mapProperties: {
        data: [],
        loading: true,
        reloading: false,
        component_loaded: false,
        map_info: undefined
      },
      loadingAllProperties: false,
      totalProperties: null,
      lastPropertyPage: null,
      errorMsg: '',
      filters: {
        loadCount: 1,
        status: '',
        propertyType: '',
        polygons: [],
        sortBy: '',
        sortOrder: '',
      },
      page: 1,
    };
  },
  watch: {
    isActive(newVal) {
      if (newVal) {
        this.search();
      }
    },
    loadingAllData(val) {
      const [newLoadingA, newLoadingB] = val.split('|');
      const isLoading = newLoadingA === 'yes' && newLoadingB === 'yes';
      EventBus.$emit('processLoading', isLoading);
    },
  },
  computed: {
    ...mapState('home', { homeData: state => state.data }),

    loadingAllData() {
      const loadingA = this.isLoading ? 'yes' : 'no';
      const loadingB = this.mapProperties.loading ? 'yes' : 'no';
      return `${loadingA}|${loadingB}`
    },
    hasPolygons () {
      return this.filters?.polygons?.length > 0 ? true : false;
    }
  },
  methods: {
    ...mapActions('home', ['saveHistory']),

    async filterProperties(nextPage = false, infiniteState) {
      if (this.properties == null) this.properties = {};

      try {
        let page = nextPage ? this.page + 1 : 1;

        if (nextPage) {
          this.isPreLoading = true;
        } else {
          this.isLoading = true;
          this.properties = null;
        }

        this.setInitialSort();

        await axios.post(`/api/properties/filter?page=${page}&sortBy=${this.filters.sortBy}&orderBy=${this.filters.sortOrder}`, this.filters).then(({ data }) => {
          if (nextPage) {
            data.data.forEach((item) => this.properties.push(item));
            this.page += 1;

            if (this.page >= data.last_page) {
              this.page = data.last_page;
              infiniteState.complete();
            } else {
              infiniteState.loaded();
            }
          } else {
            this.properties = data.data;
          }

          this.totalProperties = data.total;
          this.lastPropertyPage = data.last_page;
          this.isLoading = false;
          this.isPreLoading = false;

          EventBus.$emit('resultsCount', {
            totalProperties: this.totalProperties,
          });

          EventBus.$emit('loadHomeProperties', {
            isLoading: this.isLoading,
          });
        });
      } catch (err) {
        console.error(err);
        this.errorMsg = getErrorMessage(err);
        this.isLoading = false;
        this.isPreLoading = false;
      }

      if (!nextPage) {
        this.page = 1;
      }
    },
    setInitialSort() {
      const sortToggles = this.homeData.property.list.sortToggles;
      const keys = Object.keys(sortToggles);
      for (let i = 0; i < keys.length; i++) {
        const key = keys[i];
        if (sortToggles[key]) {
          this.filters.sortBy = key;
          this.filters.sortOrder = 'asc';
          break;
        }
      }
    },
    mapPropertiesInit({ map_info }) {
      this.mapProperties.map_info = map_info;
      this.mapProperties.component_loaded = true;
      this.fetchMapProperties(true);
    },
    mapPropertiesOnMapUpdate({ map_info }) {
      this.mapProperties.map_info = map_info;
      this.fetchMapProperties();
    },
    async fetchMapProperties(init = false) {
      if (this.mapProperties.loading && init) return;
      if (!this.mapProperties.component_loaded) return;
      if (this.mapProperties.reloading) return;

      this.mapProperties.data = [];

      if (init) {
        this.mapProperties.loading = true;
      } else {
        this.mapProperties.reloading = true;
      }

      try {
        // console.time("Properties fetch time")
        const map_info = { ...this.mapProperties.map_info, init };
        const filters = { ...this.filters, map_info };
        const { data } = await axios.post(`/api/properties/filter?formap=1`, filters);
        if (data?.length) {
          this.mapProperties.data = data;
        }
      } catch (err) {
        console.error(err);
      } finally {
        this.mapProperties.loading = false;
        this.mapProperties.reloading = false;
      }
    },
    async filterPropertiesByAddress(addressQuery) {
      await axios.post(`/api/properties/address-lookup`, { addressQuery }).then(({ data }) => {
        this.saveHistory({ property: { displaying: 'list' } });
        this.properties = data.data;
      });
    },
    infiniteHandler($state) {
      if (this.page > this.lastPropertyPage) {
        return;
      }
      this.filterProperties(true, $state);
    },
    search(dontUpdateBuilderList = false) {
      EventBus.$emit("propertiesFilter", { dontUpdateBuilderList });
    },
    setViewAs(displaying) {
      this.saveHistory({ property: { displaying } });
    },
    setSort(column) {
      let sortToggles = { ...this.homeData.property.list.sortToggles };
      Object.keys(sortToggles).forEach((key) => {
        sortToggles[key] = (key !== column) ? false : !sortToggles[key];
      });

      this.saveHistory({
        property: {
          list: {
            sortToggles: sortToggles
          }
        }
      });

      return this.homeData.property.list.sortToggles[column] ? 'asc' : 'desc';
    },
  },
  created() {
    let cachedSelections = localStorage.getItem('selectedPolygons');
    if (cachedSelections) {
      this.filters.polygons = JSON.parse(cachedSelections);
    }

    EventBus.$on('clearAllFilters', () => delete this.filters.buildersId);

    EventBus.$on('propertiesFilter', async (filter) => {
      this.filters.loadCount = 1;
      this.filters = {
        ...this.filters,
        ...filter,
      };

      if (this.isActive) {
        this.isLoading = true;
        this.filterProperties().then(() => this.isLoading = false);
        this.fetchMapProperties();
      }
    });

    EventBus.$on('orderProperties', (column, order) => {
      this.filters.sortBy = column;
      this.filters.sortOrder = order;
      this.filterProperties(false, null);
    });

    EventBus.$on('clearProperties', () => {
      this.properties = null;
    });

    EventBus.$on('searchAddress', this.filterPropertiesByAddress);

    EventBus.$on('updatedBuilderIds', ({ builders }) => {
      this.filters.buildersId = builders;
      this.page = 1;
      this.search(true);
    });
  },
  mounted() {
    EventBus.$emit('loadHomeProperties', {
      isLoading: this.isLoading,
    });

    if (window.innerWidth < 992) {
      this.saveHistory({
        property: {
          displaying: 'list'
        }
      });
    }
  },
  destroyed() {
    EventBus.$off('clearAllFilters');
    EventBus.$off('propertiesFilter');
    EventBus.$off('orderProperties');
    EventBus.$off('clearProperties');
    EventBus.$off('searchAddress');
    EventBus.$off('updatedBuilderIds');
  }
};
</script>
<style scoped>
.home-property-list,
.loaded-content,
.loaded-content-inner {
  height: 100%;
}

.home-property-list {
  padding-top: 25px;
}

.loading-state {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

.property-list-results {
  /* height: calc(25% - 25px); */
  overflow: scroll;
  /* margin-top: 25px; */
  min-height: 500px;
  overflow-x: hidden;
  height: 100%;
}

.property-list-results::-webkit-scrollbar-track {
  border: 1px solid #fff;
  padding: 2px 0;
  background-color: #fff;
}

.property-list-results::-webkit-scrollbar {
  width: 10px;
}

.property-list-results::-webkit-scrollbar-thumb {
  border-radius: 10px;
  box-shadow: inset 0 0 6px rgba(255, 255, 255, 0.3);
  background-color: #022E55;
  border: 1px solid #fff;
}

.property-map-results {
  height: calc(100% - 10px);
  margin-top: 10px;
  opacity: 0;
  transform: translateY(-500%);
}
.property-map-results.active {
  opacity: 1;
  transform: translateY(0);
}

.display-option-tabs {
  position: absolute;
  left: 488px;
  top: 10px;
  z-index: 5;
}

.display-option-tabs button {
  height: 40px;
  width: 100px;
  color: #aaa7a7;
  border: 3px solid #aaa7a7;
  background: transparent;
}

.display-option-tabs button:not(:last-child) {
  margin-right: 6px;
}

.display-option-tabs button.active {
  border: 3px solid #ffc501;
  color: #012e55;
}

.total-records {
  color: #012e55;
  font-size: 16px;
}

.form button {
  background: #ffc501;
  color: #012e55;
  border: 0px solid;
  text-align: center;
  vertical-align: middle;
  border-radius: 0px !important;
}

@media screen and (max-width: 991px) {
  .display-option-tabs {
    display: none;
  }

  .property-list-results {
    margin: 0;
    padding: 0;
  }

  .home-property-list {
    padding-top: 0px;
  }
}

@media screen and (min-width: 1200px) {
  .display-option-tabs {
    left: 528px;
  }

  .display-option-tabs button {
    height: 45px;
  }
}

@media screen and (min-width: 1400px) {
  .display-option-tabs {
    left: 558px;
  }

  .display-option-tabs button {
    height: 50px;
  }
}

@media screen and (min-width: 1600px) {
  .display-option-tabs button {
    height: 55px;
    border-width: 4px;
  }

  .display-option-tabs button.active {
    border-width: 4px;
  }
}
</style>
