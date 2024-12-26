<template>
  <section id="ContentArea" class="content-area" ref="el">
    <div class="row">
      <DropdownPanel
        panelClass="col-2 criteria-panel"
        :toggled="viewCriteria || (activePanel === 'results' && !isMobile)"
        :maxHeight="isMobile ? pixelHeight - 45 : pixelHeight"
      >
        <PropertyFilters :isLoading="isPropertyLoading" />
      </DropdownPanel>

      <DropdownPanel panelClass="col-10 results-panel pr-0" :toggled="activePanel === 'results'"
        :maxHeight="isMobile ? pixelHeight - 90 : pixelHeight">
        <HomePropertyList :isActive="activePanel === 'results'" :isLoggedIn="isLoggedIn" />
      </DropdownPanel>

      <DropdownPanel
        panelClass="save-search-panel px-lg-4"
        :toggled="showSaveSearchPanel"
        :maxHeight="saveSearchPanelHeight"
        :maxHeightMeasurement="saveSearchMaxHeightMeasurement"
        :style="{
          left: `${saveSearchLeft - saveSearchAdjustLeft}px`,
          pointerEvents: showSaveSearchPanel ? null : 'none'
        }"
      >
        <SaveThisSearch />
      </DropdownPanel>
    </div>
  </section>
</template>

<script>
import { EventBus } from "../../helpers";
import PropertyFilters from "../PropertyFilters/PropertyFilters";
import HomePropertyList from "../HomePropertyList/HomePropertyList";
import DropdownPanel from "../../DropdownPanel/DropdownPanel";
import SaveThisSearch from "../SaveThisSearch/SaveThisSearch";

export default {
  name: "ContentArea",
  props: ["isLoggedIn", "viewCriteria", "activePanel", 'showSaveSearchPanel'],
  components: {
    DropdownPanel,
    PropertyFilters,
    HomePropertyList,
    SaveThisSearch,
  },
  data() {
    return {
      showBuilder: true,
      isLoading: false,
      builders: [],
      pixelHeight: 0,
      saveSearchLeft: 0,
      saveSearchAdjustLeft: 6,
      isMobile: false,
      saveSearchPanelHeight: 0,
      saveSearchMaxHeightMeasurement: "px",
    };
  },
  computed: {
    shouldShowBuilder() {
      return this.showBuilder;
    },
    isPropertyLoading() {
      return this.isLoading;
    },
  },
  created() {
    this.$Progress.start();

    EventBus.$on("processLoading", (status) => {
      this.isLoading = status;
    });

    EventBus.$on("loadHomeProperties", (data) => {
      this.isLoading = data.isLoading;
      this.builders = data.builders;
      this.$Progress.finish();
    });
  },
  mounted() {
    this.reCalculatePixels();
    window.addEventListener("resize", this.reCalculatePixels);
  },
  unmounted() {
    window.removeEventListener("resize", this.reCalculatePixels);
  },
  methods: {
    reCalculatePixels() {
      setTimeout(() => {
        this.doCalculatePixels();
      }, 500);
    },
    doCalculatePixels() {
      if (this.$refs.el) {
        this.pixelHeight = this.$refs.el.getBoundingClientRect().height - 1;
      }
      this.isMobile = window.innerWidth < 992;
      this.saveSearchLeft = this.getSaveSearchLeft();

      if (!this.isMobile) {
        this.saveSearchPanelHeight =
          document.querySelector(".save-this-search")?.offsetHeight +
          document.querySelector(".save-search-panel")?.offsetHeight;
        this.saveSearchAdjustLeft = 6;
        this.saveSearchMaxHeightMeasurement = "px";
      } else {
        this.saveSearchPanelHeight = "100";
        this.saveSearchAdjustLeft = 0;
        this.saveSearchMaxHeightMeasurement = "%";
      }
    },
    getSaveSearchLeft() {
      if (this.isMobile) return 0;
      const button = document.querySelector(".panel-tabs .btn-save-search");
      if (button) {
        return button.getBoundingClientRect().left;
      }
      return 0;
    },
  },
  watch: {
    activePanel() {
      setTimeout(() => {
        this.saveSearchLeft = this.getSaveSearchLeft();
      }, 1000);
    },
  },
};
</script>

<style scoped>
.content-area {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  pointer-events: none;
}

.content-area .criteria-panel {
  flex: 0 0 300px;
  max-width: 300px;
}

.content-area .results-panel {
  flex: 0 0 calc(100% - 300px);
  max-width: calc(100% - 300px);
}

.dropdown-panel {
  padding-top: 65px;
  pointer-events: all;
}

.save-search-panel {
  position: absolute;
  width: 300px;
  z-index: 400;
  box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
}

@media screen and (max-width: 991px) {
  .content-area {
    z-index: 1000;
    top: 7px;
  }

  .content-area .criteria-panel,
  .content-area .results-panel {
    flex: 0 0 100%;
    max-width: 100%;
  }

  .dropdown-panel {
    position: absolute;
    width: 100%;
    padding: 0;
  }

  .criteria-panel,
  .save-search-panel {
    top: 45px;
    left: 0;
  }

  .results-panel {
    top: 95px;
  }
}

@media screen and (min-width: 992px) {
  .content-area .criteria-panel {
    flex: 0 0 200px;
    max-width: 200px;
  }

  .content-area .results-panel {
    flex: 0 0 calc(100% - 200px);
    max-width: calc(100% - 200px);
  }
}

@media screen and (min-width: 1200px) {
  .content-area .criteria-panel {
    flex: 0 0 250px;
    max-width: 250px;
  }

  .content-area .results-panel {
    flex: 0 0 calc(100% - 250px);
    max-width: calc(100% - 250px);
  }
}

@media screen and (min-width: 1400px) {
  .content-area .criteria-panel {
    flex: 0 0 275px;
    max-width: 275px;
  }

  .content-area .results-panel {
    flex: 0 0 calc(100% - 275px);
    max-width: calc(100% - 275px);
  }
}
</style>
