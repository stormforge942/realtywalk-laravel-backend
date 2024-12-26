<template>
  <div id="page">
    <div id="header-nav" ref="header">
      <AppHeader
        :isLoggedIn="isLoggedIn"
        :logout="logout"
        :showInstructions="$t('home.header.caption')"
      ></AppHeader>
    </div>
    <div
      class="page-content"
      :class="{ 'grid-list': !applyScreenHeight }"
      :style="{ top: `${pageTop}px` }"
    >
      <div class="position-relative">
        <div :style="contentStyle">
          <Transition>
            <HomeMap
              v-if="showHomeMap"
              ref="homeMap"
              :toggleCriteria="toggleCriteria"
              :setActivePanel="setActivePanel"
              :viewCriteria="homeData.viewCriteria"
              :activePanel="homeData.activePanel"
              :searchPolygon="searchPolygon"
              @showSaveSearchPanel="toggleSaveSearchPanel"
            ></HomeMap>
            <StartScreen v-else @selected="onSelectedPolygon" />
          </Transition>
        </div>
        <Footer
          class="d-none d-md-block"
          ref="footer"
          :isLoggedIn="isLoggedIn"
          :logout="logout"
          mini="true"
        ></Footer>

        <ContentArea
          v-if="showHomeMap"
          :isLoggedIn="isLoggedIn"
          :viewCriteria="homeData.viewCriteria"
          :activePanel="homeData.activePanel"
          :showSaveSearchPanel="showSaveSearchPanel"
        ></ContentArea>
      </div>
    </div>
  </div>
</template>

<script>
import { EventBus } from "../helpers";
import AppHeader from "../AppHeader/AppHeader";
import HomeMap from "./HomeMap/HomeMap";
import StartScreen from "./StartScreen";
import ContentArea from "./ContentArea/ContentArea";
import Footer from "../layout/Footer";
import { mapActions, mapState } from 'vuex';

export default {
  name: 'HomePage',
  components: {
    AppHeader,
    HomeMap,
    StartScreen,
    ContentArea,
    Footer,
  },
  data() {
    return {
      searchPolygon: null,
      pageTop: 0,
      footerHeight: 0,
      gridLoaded: false,
      showSaveSearchPanel: false,
    };
  },
  computed: {
    ...mapState('home', { homeData: state => state.data }),

    isLoggedIn() {
      return this.$store.state.auth.status.loggedIn;
    },
    showHomeMap() {
      return this.homeData.showHomeMap;
    },
    contentStyle() {
      return this.applyScreenHeight ? { height: `calc(100% - ${this.footerHeight}px)` } : {};
    },
    applyScreenHeight() {
      return this.showHomeMap || !this.gridLoaded;
    }
  },
  created() {
    EventBus.$on('gridUpdated', () => this.gridLoaded = true);
    EventBus.$on('saveSearch', () => this.showSaveSearchPanel = false);
    EventBus.$on('searchPolygon', (polygon) => this.searchPolygon = polygon);
    EventBus.$on('searchAddress', (query) => this.saveHistory({ activePanel: 'results' }));
    EventBus.$on('selectedPolygon', () => this.saveHistory({ viewCriteria: true }));
  },
  destroyed() {
    EventBus.$off('gridUpdated');
    EventBus.$off('saveSearch');
    EventBus.$off('searchPolygon');
    EventBus.$off('searchAddress');
    EventBus.$off('selectedPolygon');
  },
  mounted() {
    this.reCalculatePixels();
    window.addEventListener("resize", this.reCalculatePixels);

    window.setTimeout(() => {
      if (this.$refs.footer && this.$refs.footer.$el) {
        this.footerHeight =
          this.$refs.footer.$el.getBoundingClientRect().height;
      }
    }, 300);
  },
  unmounted() {
    window.removeEventListener("resize", this.reCalculatePixels);
  },
  methods: {
    ...mapActions('home', ['saveHistory']),

    onSelectedPolygon(polygonId) {
      setTimeout(() => {
        this.$refs.homeMap.initialCheckPolygon(polygonId);
      })
    },
    logout() {
      const { dispatch } = this.$store;
      dispatch("auth/userLogout");
    },
    toggleCriteria(activate = null) {
      if (activate !== null) {
        this.saveHistory({ viewCriteria: activate });
      } else {
        this.saveHistory({ viewCriteria: !this.homeData.viewCriteria });
      }
    },
    setActivePanel(panel) {
      if (panel === "map" || panel === "results") {
        // this.activePanel = panel;
        this.saveHistory({ activePanel: panel });
      }
    },
    toggleSaveSearchPanel(show) {
      this.showSaveSearchPanel = show;
    },
    reCalculatePixels() {
      if (this.$refs.header) {
        const container = this.$refs.header.querySelector(".header-container");
        this.pageTop = container ? container.getBoundingClientRect().height : 0;
      }

      if (this.$refs.footer && this.$refs.footer.$el) {
        this.footerHeight =
          this.$refs.footer.$el.getBoundingClientRect().height;
      }
    },
  },
};
</script>

<style lang="scss" scoped>
#page {
  height: 100vh;
}

.page-content {
  padding-top: 0;
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  overflow: hidden;

  &.grid-list {
    overflow: auto;
  }
}

.page-content > .position-relative {
  height: 100%;
  width: 100%;
}
</style>
