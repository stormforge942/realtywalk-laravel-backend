<template>
  <section class="map-container">
    <div class="container-fluid px-0">
      <div class="map-actions row">
        <div class="col-2 panel-actions">
          <div class="pl-xl-2 pr-lg-1 pr-xl-4">
            <button
              type="button"
              class="map-toggle text-left"
              :class="[
                viewCriteria ? 'toggled' : '',
                activePanel === 'results' ? 'static' : '',
              ]"
              @click="onCriteriaToggle"
            >
              {{ $t("home.map.btn_search_criteria") }}

              <span class="d-lg-none">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  xmlns:xlink="http://www.w3.org/1999/xlink"
                  aria-hidden="true"
                  role="img"
                  width="1em"
                  height="1em"
                  preserveAspectRatio="xMidYMid meet"
                  viewBox="0 0 20 20"
                >
                  <path
                    d="M13.25 10L6.109 2.58a.697.697 0 0 1 0-.979a.68.68 0 0 1 .969 0l7.83 7.908a.697.697 0 0 1 0 .979l-7.83 7.908a.68.68 0 0 1-.969 0a.697.697 0 0 1 0-.979L13.25 10z"
                    fill="currentColor"
                  />
                </svg>
              </span>
            </button>

            <button
              type="button"
              class="btn-save-search-sm d-lg-none"
              @click="toggleSaveSearchPanel()"
              :class="showSaveSearch ? 'is-active' : ''"
            >
              {{ $t("home.map.btn_save_search_short") }}
              <span>
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  xmlns:xlink="http://www.w3.org/1999/xlink"
                  aria-hidden="true"
                  role="img"
                  width="32"
                  height="32"
                  preserveAspectRatio="xMidYMid meet"
                  viewBox="0 0 32 32"
                >
                  <g
                    fill="none"
                    stroke="currentColor"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                  >
                    <path d="M6 2h20v28L16 20L6 30z" />
                  </g>
                </svg>
              </span>
            </button>
          </div>
        </div>
        <div class="col-10 panel-tabs">
          <div
            class="panel-tabs-list"
            :class="{
              'shrink-size': homeData.showNeighborhoodPanel && activePanel === 'map',
            }"
          >
            <div class="panel-left">
              <button
                :class="[
                  activePanel === 'map' ? 'active' : '',
                  activePanel === 'results' ? 'wide' : ''
                ]"
                class="btn-neighborhoods"
                @click="onMapAction('map')"
              >
                <span v-if="activePanel !== 'results' || xSmScreen">
                  {{ $t("home.map.btn_neighborhoods") }}
                </span>
                <span v-else>{{ $t("home.map.btn_back_to_neighborhoods") }}</span>
              </button>
              <button
                :class="activePanel === 'results' ? 'active' : ''"
                @click="onMapAction('results')"
                class="btn-results"
              >
                {{ $t("home.map.btn_results") }}
              </button>
              <button
                class="btn-save-search d-none d-lg-inline"
                :class="showSaveSearch ? 'active' : ''"
                @click="toggleSaveSearchPanel()"
              >
                <span class="d-none d-xl-block">{{ $t("home.map.btn_save_search") }}</span>
                <span class="d-lg-block d-xl-none">{{ $t("home.map.btn_save_search_short") }}</span>
              </button>
            </div>

            <div class="panel-right" v-if="activePanel === 'map'">
              <div class="toggle-checkbox-rounded">
                <label :title="$t('home.toggles.flood_planes')" :class="{ checked: toggles.floodPlanes }">
                  <input type="checkbox" v-model="toggles.floodPlanes" @change="updateFloodPlanes()" />
                  <span class="slider" />
                  <span class="label text">
                    {{ $t('home.toggles.flood_planes') }}
                  </span>
                  <span class="label icon">
                    <i class="fa fa-tint" aria-hidden="true"></i>
                  </span>
                </label>
                <div v-if="toggles.floodPlanes && floodDataRequestsCount" class="flood-loader">
                  <i class="fa fa-spinner fa-spin"></i>
                  <span>{{ $t('home.map.loading_flood_planes') }}</span>
                </div>
              </div>

              <div class="toggle-checkbox-rounded">
                <label :title="$t('home.toggles.school_zones')" :class="{ checked: toggles.schoolZones, active: toggles.schoolZones }">
                  <input type="checkbox" v-model="toggles.schoolZones" @change="updateSchoolZones()" />
                  <span class="slider" />
                  <span class="label text">
                    {{ $t('home.toggles.school_zones') }}
                  </span>
                  <span class="label icon">
                    <i class="fa fa-graduation-cap" aria-hidden="true"></i>
                  </span>
                </label>

                <div v-if="toggles.schoolZones" class="child-toggles has-legends">
                  <div class="toggle-checkbox-rounded" v-for="(color, legend) in schoolZoneLegends" :key="legend">
                    <label :title="$t('home.toggles.' + legend)" :class="{ checked: toggles.activeSchoolZones === legend }" :style="{ backgroundColor: toggles.activeSchoolZones === legend ? color : null }">
                      <input type="radio" :value="legend" v-model="toggles.activeSchoolZones" @change="updateSchoolZones()" />
                      <span class="legend" :style="{ backgroundColor: toggles.activeSchoolZones === legend ? color : 'transparent' }" />
                      <span class="label text">
                        {{ $t('home.toggles.' + legend) }}
                      </span>
                      <span class="label icon">
                        <i class="fa fa-graduation-cap" aria-hidden="true"></i>
                      </span>
                    </label>
                  </div>
                </div>
              </div>

              <div class="toggle-checkbox-rounded">
                <label :title="$t('home.toggles.bike_trails')" :class="{ checked: toggles.bikeTrails }">
                  <input type="checkbox" v-model="toggles.bikeTrails" @change="updateBikeLayer()" />
                  <span class="slider" />
                  <span class="label text">
                    {{ $t('home.toggles.bike_trails') }}
                  </span>
                  <span class="label icon">
                    <i class="fa fa-bicycle" aria-hidden="true"></i>
                  </span>
                </label>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="loading-spinner" v-if="mapBusyCount + loadingCount > 0">
        <div class="loading-spinner-inner">
          <i class="fa fa-spinner fa-spin fa-3x text-light"></i>
        </div>
      </div>

      <div
        v-show="activePanel === 'map'"
        class="neighborhoods-container"
        :class="{
          active: homeData.showNeighborhoodPanel,
        }"
      >
        <div class="neighborhoods-inner">
          <div
            class="selections-tab d-none d-lg-flex"
            :class="[homeData.showNeighborhoodPanel ? 'toggled' : '']"
            @click="toggleNeighborhood()"
          >
            <span>
              {{ $t("home.map.selections") }}
              <svg
                xmlns="http://www.w3.org/2000/svg"
                xmlns:xlink="http://www.w3.org/1999/xlink"
                aria-hidden="true"
                role="img"
                width="1em"
                height="1em"
                preserveAspectRatio="xMidYMid meet"
                viewBox="0 0 20 20"
              >
                <path
                  d="M13.25 10L6.109 2.58a.697.697 0 0 1 0-.979a.68.68 0 0 1 .969 0l7.83 7.908a.697.697 0 0 1 0 .979l-7.83 7.908a.68.68 0 0 1-.969 0a.697.697 0 0 1 0-.979L13.25 10z"
                  fill="currentColor"
                />
              </svg>
            </span>
          </div>
          <div class="neighborhoods" v-if="renderComponent">
            <div class="neighborhood-levels">
              <button
                :class="polygonsActiveLevel === 0 ? 'active' : ''"
                @click="setPolygonsActiveLevel(0, false, false)"
              >
                <span>{{ $t("home.map.level", { n: 1 }) }}</span>
              </button>
              <button
                :class="polygonsActiveLevel === 1 ? 'active' : ''"
                @click="setPolygonsActiveLevel(1, false, false)"
              >
                <span>{{ $t("home.map.level", { n: 2 }) }}</span>
              </button>
              <button
                :class="polygonsActiveLevel === 2 ? 'active' : ''"
                @click="setPolygonsActiveLevel(2, false, false)"
              >
                <span>{{ $t("home.map.level", { n: 3 }) }}</span>
              </button>
            </div>

            <div class="toggle-checkbox-fullwidth">
              <label :title="$t('home.toggles.show_unselected_areas')" :class="{ checked: toggles.showUnselectedAreas }">
                <input type="checkbox" v-model="toggles.showUnselectedAreas" />
                <span class="slider" />
                <span class="label">
                  {{ $t('home.toggles.show_unselected_areas') }}
                </span>
              </label>
            </div>

            <div class="neighborhoods-content">
              <LiquorTree
                ref="tree"
                :class="!toggles.showUnselectedAreas ? 'hide-unchecked' : 'hide-all-unchecked'"
                :options="treeOptions"
                @node:clicked="onNeighborhoodClicked"
                @node:unchecked="onUncheckPoly"
                @node:checked="onCheckPoly"
              />
            </div>

            <!-- Clear selected polygons button -->
            <div class="neighborhoods-footer px-3">
              <a
                href="javascript:;"
                class="btn-clear-selected"
                @click="clearAllSelected"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"
                  />
                </svg>

                <span>{{ $t("home.map.btn_clear_selected") }}</span>
              </a>
            </div>
          </div>
        </div>
      </div>

      <div
        v-show="activePanel === 'map'"
        class="selections-tab d-lg-none"
        :class="[homeData.showNeighborhoodPanel ? 'toggled' : '']"
        @click="toggleNeighborhood()"
      >
        <span v-if="!homeData.showNeighborhoodPanel">{{
          $t("home.map.selections")
        }}</span>
        <span v-if="homeData.showNeighborhoodPanel" style="padding: 15px 0px">{{
          $t("home.map.close")
        }}</span>

        <svg
          xmlns="http://www.w3.org/2000/svg"
          xmlns:xlink="http://www.w3.org/1999/xlink"
          aria-hidden="true"
          role="img"
          width="1em"
          height="1em"
          preserveAspectRatio="xMidYMid meet"
          viewBox="0 0 20 20"
        >
          <path
            d="M13.25 10L6.109 2.58a.697.697 0 0 1 0-.979a.68.68 0 0 1 .969 0l7.83 7.908a.697.697 0 0 1 0 .979l-7.83 7.908a.68.68 0 0 1-.969 0a.697.697 0 0 1 0-.979L13.25 10z"
            fill="currentColor"
          />
        </svg>
      </div>

      <div class="map-messages">
          <div class="map-message" v-if="homeData.toggles.showInstructions" :class="[
            homeData.showNeighborhoodPanel ? 'adjusted' : ''
          ]">
            <span class="close-message fa fa-close text-warning" @click="toggles.showInstructions = false"></span>
            <span v-if="toggles.activeNeighborhood">{{ $t("home.map.instructions_selected", { neighborhood: toggles.activeNeighborhood }) }}</span>
            <span v-else>{{ $t("home.map.instructions") }}</span>
          </div>

          <div class="map-message" v-if="homeData.toggles.showToggleMessage && activeToggle" :class="[
            homeData.showNeighborhoodPanel ? 'adjusted' : ''
          ]">
            <span class="close-message fa fa-close text-warning" @click="toggles.showToggleMessage = false"></span>
            {{ $t("home.map.initial_toggle", { neighborhood: toggles.activeNeighborhood }) }}
          </div>
      </div>

      <div class="map-legends" v-if="homeData.toggles.floodPlanes || homeData.toggles.schoolZones">
        <div class="map-legend" v-if="homeData.toggles.floodPlanes">
          <ul>
            <li v-for="(color, legend) in floodZoneLegends" :key="legend">
              <label :title="legend" :class="{ checked: toggles.activeFloodPlanes.includes(legend) }">
                <input type="checkbox" @change="updateFloodPlanes(legend)" />
                <span class="legend-box" :style="{backgroundColor: color}"></span>
                <span class="label text">
                  {{ $t('home.flood_zones.' + legend) }}
                </span>
              </label>
            </li>
          </ul>
        </div>
      </div>

      <div id="map-canvas"></div>
    </div>
  </section>
</template>

<script>
import _ from 'lodash';
import { EventBus } from '../../helpers';
import { mapActions, mapState } from 'vuex';
import LiquorTree from 'liquor-tree';

let map;
let crushed = false;
let tabOut = false;
let opacitySelected = 0.9;
let opacityHover = 0.6;
let opacityChillin = 0.25;

const defaultZoom = 10;
const zoomBreak1 = 13;
const zoomBreak2 = 15;

export default {
  name: 'HomeMap',
  components: { LiquorTree },
  props: [
    'toggleCriteria',
    'setActivePanel',
    'viewCriteria',
    'activePanel',
    'mapHeight',
    'searchPolygon',
  ],
  data: () => ({
    totalProperties: 0,
    showSaveSearch: false,
    toggles: {},
    bikeLayer: null,
    selectedPolygons: [],
    ancestorPolygons: [],
    selectedParents: [],
    polygonLabel: null,
    lastZoomUpdate: null,
    polys: [],
    polysTree: [],
    selectedPolysTree: [],
    levelPolys: [[], [], []],
    existingPolygonIds: [],
    levelStates: [true, false, false],
    lastZoom: defaultZoom,
    renderComponent: false,
    lastRequestNumber: 0,
    loadingCount: 1,
    mapBusyCount: 1,
    polygonsActiveLevel: 0,
    zoomOnPolyClick: true,
    polygonTrunks: [[], [], []],
    treeOptions: {
      checkbox: true,
      checkOnSelect: false,
      keyboardNavigation: false,
    },
    selectedPolygonsDebounce: 0,
    floodPlanesIds: new Set(),
    floodZoneLegends: [],
    floodDataRequest: null,
    floodDataRequestsCount: 0,
    schoolZonesIds: new Set(),
    schoolZoneLegends: [],
    xSmScreen: false,
    popupDisplayed: false
  }),
  computed: {
    ...mapState('home', {
      homeMaster: state => state.master,
      homeData: state => state.data
    }),
    activeToggle() {
      if (this.toggles.floodPlanes) return "floodPlanes";
      if (this.toggles.schoolZones) return "schoolZones";
      if (this.toggles.bikeTrails) return "bikeTrails";

      return null;
    },
  },
  watch: {
    selectedPolygons() {
      this.selectedPolygonsDebounce++;
      let bounce = this.selectedPolygonsDebounce;
      setTimeout(() => {
        if (bounce != this.selectedPolygonsDebounce) return;
        localStorage.setItem('selectedPolygons', JSON.stringify(this.selectedPolygons));
        localStorage.setItem('ancestorPolygons', JSON.stringify(this.ancestorPolygons));
        localStorage.setItem('selectedParents', JSON.stringify(this.selectedParents));
        this.setPolygonDataStyling();
        this.clearFilter();
        this.updateFilter();
        this.getPolygonList();
        // this.getPolygonListV2();
      }, 250);
    },
    popupDisplayed(val) {
      if (val === true) {
        setTimeout(() => {
          this.popupDisplayed = false;
        }, 3000);
      }
    },
    searchPolygon: function (val) {
      this.polyFilterEvent(val);
    },
    toggles: {
      handler() {
        this.updateToggles();
      },
      deep: true
    },
    showSaveSearch(val) {
      this.$emit('showSaveSearchPanel', val);
    }
  },
  methods: {
    ...mapActions('home', ['saveHistory']),

    updateToggles: _.debounce(function () {
      this.saveHistory({ toggles: this.toggles });
    }, 500),
    forceRerender() {
      // Remove my-component from the DOM
      this.renderComponent = false;

      this.$nextTick(() => {
        // Add the component back in
        this.renderComponent = true;
      });
    },
    getPolygonList() {
      axios
        .post("/api/polygon/get-list", { ids: this.selectedParents })
        .then((response) => {
          let notExpanded = this.$refs.tree
            .findAll({ state: { expanded: false } })
            .map((node) => node.id);

          this.$refs.tree?.remove({}, true);
          this.polygonTrunks = [[], [], []];

          this.buildTrunk(response.data).forEach((list, index) => {
            list.forEach((data) => {
              let node = this.prepareNodeData(data);
              this.polygonTrunks[index].push(node);

              if (index === this.polygonsActiveLevel) {
                let trunk = this.$refs.tree.prepend(node);

                if (!notExpanded.includes(trunk.id)) {
                  trunk.expand();
                }
              }
            });
          });
        });
    },
    getPolygonListV2() {
      axios
        .post("/api/polygon/get-list-v2", { ids: this.selectedParents })
        .then(res => {
          const { status, data } =  res;
          if (status === 200) {
            let notExpanded = this.$refs.tree
              .findAll({ state: { expanded: false } })
              .map((node) => node.id);

            this.$refs.tree?.remove({}, true);
            this.polygonTrunks = [[], [], []];

            this.buildTrunk(data).forEach((list, index) => {
              list.forEach(item => {
                let node = this.prepareNodeData(item);
                this.polygonTrunks[index].push(node);

                if (index === this.polygonsActiveLevel) {
                  let trunk = this.$refs.tree.prepend(node);

                  if (!notExpanded.includes(trunk.id)) {
                    trunk.expand();
                  }
                }
              });
            });
          }
        });
    },
    buildTrunk(nodes) {
      if (nodes && nodes.length) {
        let trunk = [];
        for (let i = 1; i <= 3; i++) {
          trunk.push(this.getTrunkByLevel(nodes, i));
        }
        return trunk;
      }
      return [[], [], []];
    },
    getTrunkByLevel(nodes, maxLevel) {
      let data = [];
      for (let i = 0; i < nodes.length; i++) {
        let currentNode = { ...nodes[i] };
        delete currentNode.children;
        if (
          nodes[i].children.length &&
          ((maxLevel < 3 && currentNode.zoom < maxLevel) || maxLevel === 3)
        ) {
          currentNode.children = this.getTrunkByLevel(
            nodes[i].children,
            maxLevel
          );
        }
        data.push(currentNode);
      }
      return data;
    },
    colorHash(polyId) {
      let mega = polyId.substring(0, 12);
      let hashed = md5(mega);
      let hexcolor = hashed.substring(0, 6);

      return hexcolor;
    },
    toggleNeighborhood () {
      this.saveHistory({
        showNeighborhoodPanel: !this.homeData.showNeighborhoodPanel
      });
    },
    clearAllSelected() {
      EventBus.$emit("clearAllFilters");
    },
    onNeighborhoodClicked(node) {
      console.log(node);

      // If clicked on the text
      if (event.target.tagName == "SPAN" || event.target.nodeName == "SPAN") {
        window.location.href = node.data.page_url;
      }

      if (!(node.id in this.selectedPolygons)) {
        this.checkPolygon(node.id);
      }

      return false;
    },
    onUncheckPoly(node) {
      let that = this;
      setTimeout(function () {
        if (node.states.checked) return;
        if (node.children.length && node.states.indeterminate) return;
        if (that.selectedPolygons.indexOf(node.id) === -1) return;
        if (that.selectedParents.indexOf(node.id) !== -1) {
          that.selectedParents.splice(that.selectedParents.indexOf(node.id), 1);
        }
        that.removePolygonChildrenRecursively(node.id);
        that.selectedPolygons.splice(that.selectedPolygons.indexOf(node.id), 1);
      }, 300);
    },
    onCheckPoly(node) {
      let that = this;
      setTimeout(function () {
        if (!node.states.checked) return;
        if (that.selectedPolygons.indexOf(node.id) !== -1) return;
        that.addPolygonChildrenRecursively(node.id);
        that.selectedPolygons.push(node.id);
      }, 300);
    },
    findInsideTree(id, tree = this.polysTree) {
      for (let node of tree) {
        if (node.id == id) {
          return node;
        }

        if (!node.children) continue;
        for (let child of node.children) {
          if (child.id == id) {
            return child;
          }

          if (!child.children) continue;
          for (let child2 of child.children) {
            if (child2.id == id) {
              return child2;
            }

            if (!child2.children) continue;
            for (let child3 of child2.children) {
              if (child3.id == id) {
                return child3;
              }
            }
          }
        }
      }
      return null;
    },
    initialCheckPolygon(polyId) {
      this.saveHistory({ property: { map: { selected_polygons: [] } } });
      localStorage.setItem("selectedParents", JSON.stringify([]));
      localStorage.setItem("ancestorPolygons", JSON.stringify([]));

      if (polyId) {
        this.checkPolygon(polyId);
      }
    },
    checkPolygon(polyId) {
      this.addToPolygonList(polyId);

      if (this.selectedPolygons.some((id) => id == polyId)) {
        return;
      }

      this.selectedPolygons.push(polyId);

      axios.get("/api/polygon/trunk/" + polyId).then((response) => {
        this.selectedPolygons = [...this.selectedPolygons, ...response.data.ids].filter((value, index, self) => self.indexOf(value) === index);
        this.ancestorPolygons = [...this.ancestorPolygons, ...response.data.ancestors].filter((value, index, self) => self.indexOf(value) === index);
        this.selectedParents = [...this.selectedParents, ...response.data.ancestors, +response.data.id].filter((value, index, self) => self.indexOf(value) === index);
      });
    },
    uncheckPolygon(polyId) {
      const selection = this.$refs.tree.find({ data: { id: polyId } });
      selection.uncheck();

      this.removePolygonChildrenRecursively(polyId);
      this.selectedPolygons.splice(this.selectedPolygons.indexOf(polyId), 1);
      this.removeFromPolygonList(polyId);
      if (this.selectedParents.indexOf(polyId) !== -1) {
        this.selectedParents.splice(this.selectedParents.indexOf(polyId), 1);
      }
    },
    addToPolygonList(polyId) {
      const selected_polygons = this.homeData?.property?.map?.selected_polygons;
      if (!selected_polygons) {
        this.saveHistory({ property: { map: { selected_polygons: [polyId] }}})
      } else if (!selected_polygons.includes(polyId)) {
        selected_polygons.push(polyId);
        this.saveHistory({ property: { map: { selected_polygons }}})
      }
    },
    removeFromPolygonList(polyId) {
      const selected_polygons = this.homeData.property.map.selected_polygons;
      if (selected_polygons !== null) {
        const new_selected_polygons = selected_polygons.filter(id => id !== polyId);
        this.saveHistory({ property: { map: { selected_polygons: null }}});
        this.saveHistory({ property: { map: { selected_polygons: new_selected_polygons }}});
      }
    },
    prepareNodeData(node) {
      node.data = { id: node.id, page_url: node.path };
      node.state = { selected: true };

      if (node.children) {
        let children = this.flattenData(node.children);
        let totalMatch = this.totalMatch(
          children.map((x) => x.id),
          this.selectedPolygons
        );
        let indeterminate =
          totalMatch === 0 ? false : totalMatch !== children.length;
        node.state = {
          ...node.state,
          indeterminate: indeterminate,
          expanded: totalMatch > 0,
        };
      }

      if (this.selectedPolygons.some((id) => id == node.id)) {
        node.state = {
          ...node.state,
          checked: true,
          selected: true,
        };
      } else if (!this.ancestorPolygons.some((id) => id == node.id)) {
        this.ancestorPolygons.push(node.id);
      }

      if (node.children) {
        node.children.map((childrenNode) => {
          return this.prepareNodeData(childrenNode);
        });
      }

      return node;
    },
    totalMatch(needles, haystack) {
      let i = 0;
      haystack.forEach((x) => {
        if (needles.includes(x)) {
          i += 1;
        }
      });
      return i;
    },
    removePolygonChildrenRecursively(polyId) {
      let data = this.polygonTrunks[this.polygonTrunks.length - 1];
      let items = this.flattenData(data);
      let item = items.filter((x) => x.id == polyId);

      if (item.length && item[0].children !== undefined) {
        let children = this.flattenData(item[0].children).map((x) => x.id);
        children.forEach((id) => {
          const index = this.selectedPolygons.indexOf(id);
          if (index !== -1) {
            this.selectedPolygons.splice(index, 1);
          }
        });
        this.selectedPolygons = [...new Set(this.selectedPolygons)];
      }
    },
    addPolygonChildrenRecursively(polyId) {
      let data = this.polygonTrunks[this.polygonTrunks.length - 1];
      let items = this.flattenData(data);
      let item = items.filter((x) => x.id == polyId);

      if (item.length && item[0].children !== undefined) {
        let children = item[0].children.map((x) => x.id);
        const selectedPolygons = [...this.selectedPolygons, ...children];
        this.selectedPolygons = [...new Set(selectedPolygons)];
      }
    },
    flattenData(data, prop = "children") {
      let children = [];
      const flattenData = data.map((m) => {
        if (m[prop] && m[prop].length) {
          children = [...children, ...m[prop]];
        }
        return m;
      });

      return flattenData.concat(
        children.length ? this.flattenData(children) : children
      );
    },
    updateFilter() {
      this.lastRequestNumber++;
      let updateNumber = this.lastRequestNumber;
      // Debounces requests by 250 miliseconds
      setTimeout(() => {
        if (updateNumber != this.lastRequestNumber) {
          return;
        }
        EventBus.$emit("propertiesFilter", {
          polygons: this.selectedPolygons,
        });
      }, 100);
    },
    clearFilter() {
      EventBus.$emit("clearProperties");
    },
    async showPopupL3Alert() {
      const swal = await this.$swal({
        text: this.$t("home.alerts.polygon_level_3"),
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: 'LOG IN',
        denyButtonText: 'SIGN UP',
        cancelButtonText: 'PROCEED',
        customClass: {
          denyButton: 'swal2-confirm',
        }
      });

      return await new Promise((resolve, reject) => {
        // login
        if (swal.isConfirmed) {
          this.$router.push('/users/signin');
          reject(false);
        }

        // Register
        else if (swal.isDenied) {
          this.$router.push('/users/create');
          reject(false);
        }

        // Proceed
        else {
          resolve(true);
        }
      });
    },
    makePolygon(el, index, array) {
      if (this.existingPolygonIds.some((id) => id == el.id)) {
        return;
      }

      this.existingPolygonIds.push(el.id);
      // let leafNode = this.findInsideTree(el.id);
      el.geometry = JSON.parse(el.geometry);

      let newPolygon = map.data.addGeoJson({
        type: "FeatureCollection",
        features: [
          {
            id: el.id,
            type: "Feature",
            geometry: el.geometry,
            properties: {
              title: el.title,
              id: el.id,
              parent_id: el.parent_id,
              // Use the approximate size as the zIndex to get the smallest polys on top.
              zIndex: 40075000 - el.area,
              fillColor: el.color ?? "275b30",
              fillOpacity: opacityChillin,
              zoom: el.zoom,
              display_as_background: el.display_as_background,
              page_url: el.page_url
            },
          },
        ],
      });

      const z = parseFloat(el.zoom);
      this.levelPolys[z - 1].push(newPolygon);

      this.polys[el.id] = [newPolygon];
    },

    isPolygonVisible(zoom, displayAsBg) {
      if (this.activeToggle) return false;

      return (
        //Note: polygonsActiveLevel is 0-indexed while zoom is 1-indexed
        zoom === this.polygonsActiveLevel + 1 ||
        (zoom == 2 && this.polygonsActiveLevel === 2 && displayAsBg === 1)
      );
    },

    polyClickEvent(poly, event) {
      let zoom = poly.getProperty("zoom");

      if (zoom == 2 && this.polygonsActiveLevel === 2) {
        return false;
      }

      if (crushed === true) {
        return false;
      }

      if (
        this.zoomOnPolyClick &&
        this.selectedPolygons.length === 0 &&
        this.level1Enabled
      ) {
        map.setCenter(event.latLng);
        map.setZoom(13);
      }

      this.zoomOnPolyClick = false;

      const polyId = poly.getProperty("id");
      if (this.selectedPolygons.some((sid) => sid == polyId)) {
        this.uncheckPolygon(polyId);
      } else {
        this.checkPolygon(polyId);
        this.saveHistory({ showNeighborhoodPanel: true });
        EventBus.$emit('selectedPolygon');
      }

      this.clearFilter();
    },

    polyFilterEvent(poly) {
      if (poly !== null) {
        map.fitBounds(
          new google.maps.LatLngBounds(
            { lat: parseFloat(poly.max_lat), lng: parseFloat(poly.min_lng) },
            { lat: parseFloat(poly.min_lat), lng: parseFloat(poly.max_lng) }
          )
        );
      }
    },

    polyMouseMoveEvent(poly, event) {
      let zoom = poly.getProperty("zoom");
      if (zoom == 2 && this.polygonsActiveLevel === 2) {
        return false;
      }

      this.polygonLabel.setPosition(event.latLng);
      this.polygonLabel.setVisible(true);
      this.polygonLabel.setOptions({
        labelContent: `<div>${poly.getProperty("title")}<br/><span class="text-muted"><em>right click to view more information.</em></span></div>`,
      });
    },

    polyMouseOverEvent(poly, event) {
      let zoom = poly.getProperty("zoom");
      if (zoom == 2 && this.polygonsActiveLevel === 2) {
        return false;
      }
      if (crushed === true || poly.checked) {
        return false;
      }

      poly.setProperty("fillOpacity", opacityHover);
      this.polygonLabel.setOptions({
        labelContent: `<div>${poly.getProperty("title")}<br/><span class="text-muted"><em>right click to view more information.</em></span></div>`,
      });
    },
    polyMouseOutEvent(poly, event) {
      let zoom = poly.getProperty("zoom");
      if (zoom == 2 && this.polygonsActiveLevel === 2) {
        return false;
      }
      this.polygonLabel.setVisible(false);

      if (crushed === true || poly.checked) {
        return false;
      }

      poly.setProperty("fillOpacity", poly.getProperty('opacityChillin') || opacityChillin);
    },

    redraw() {
      setTimeout(() => {
        this.mapBusyCount++;
      }, 250);

      setTimeout(() => {
        this.setPolygonDataStyling();
        this.mapBusyCount--;
      }, 10);
    },

    setPolygonDataStyling() {
      const currentZoom = map.getZoom();
      const level3Enabled = currentZoom >= zoomBreak2;
      const level2Enabled = !level3Enabled && currentZoom >= zoomBreak1;

      let level = 0;
      if (level2Enabled) {
        level = 1;
      } else if (level3Enabled) {
        level = 2;
      }

      this.setPolygonsActiveLevel(level, true);

      map.data.setStyle((feature) => {
        const visibilityToggle = feature.getProperty("visibilityToggle");
        const type = feature.getProperty("type");
        const activeToggle = visibilityToggle ? "active" + visibilityToggle.charAt(0).toUpperCase() + visibilityToggle.slice(1) : null;
        const visibleToggle = visibilityToggle && type && this.toggles[activeToggle].includes(type);
        let zoom = feature.getProperty("zoom");
        let displayAsBg = feature.getProperty("display_as_background");
        let visible = visibilityToggle ? this.toggles[visibilityToggle] && visibleToggle : this.isPolygonVisible(zoom, displayAsBg);

        let selected = false;
        if (visible && !visibilityToggle) {
          let id = feature.getProperty("id");
          selected = this.selectedPolygons.some((sid) => sid == id);
        }

        let fillColor = feature.getProperty("fillColor");
        if (!fillColor.includes("#")) {
          fillColor = "#" + fillColor;
        }

        let strokeColor = feature.getProperty("strokeColor");
        if (strokeColor && !strokeColor.includes("#")) {
          strokeColor = "#" + strokeColor;
        }

        return {
          fillColor: fillColor,
          strokeColor: strokeColor || "#101010",
          strokeWeight: 1,
          strokeOpacity: 0.5,
          fillOpacity: selected
            ? opacitySelected
            : feature.getProperty("fillOpacity"),
          visible,
          zIndex: feature.getProperty("zIndex"),
        };
      });
    },

    toggleTab() {
      let rightBar = $("#right-bar");

      if (tabOut === false) {
        openTab();
      } else {
        closeTab();
      }
    },
    openTab() {
      if (tabOut === true) {
        return;
      }

      let now = new Date().getTime();
      let rightBar = $("#right-bar");

      tabOut = true;

      $(rightBar).animate({
        // right bar should be the same width as the pester
        // box in the header bar
        width: $("#pester").width() + 8 + "px",
      });

      tabTime = now;
    },
    closeTab() {
      if (tabOut === false) {
        return;
      }
      tabOut = false;

      let rightBar = $("#right-bar");

      $(rightBar).animate({
        // right bar should be the same width as the pester
        // box in the header bar
        width: "0px",
      });
    },
    initMap() {
      const mapOptions = {
        center: this.homeData.map.center
          ? new google.maps.LatLng(this.homeData.map.center.lat, this.homeData.map.center.lng)
          : new google.maps.LatLng(this.homeMaster.map.center.lat, this.homeMaster.map.center.lng),
        zoom: this.homeData.map.zoom || this.homeMaster.map.zoom,
        maxZoom: 18,
        minZoom: 8,
        disableDefaultUI: true,
        zoomControl: true,
        zoomControlOptions: {
          position: google.maps.ControlPosition.LEFT_BOTTOM,
        },
        panControl: true,
      };

      map = new google.maps.Map(
        document.getElementById('map-canvas'),
        mapOptions
      );

      this.bikeLayer = new google.maps.BicyclingLayer();
      this.updateBikeLayer();
      this.updateSchoolZones();

      this.polygonLabel = new MarkerWithLabel({
        map,
        position: new google.maps.LatLng(0, 0),
        draggable: false,
        raiseOnDrag: false,
        labelAnchor: new google.maps.Point(200, 40),
        labelClass: "polygon-label",
        icon: "/images/transparent.png",
        visible: false,
        label: "",
      });

      this.forceRerender();
      this.loadingCount--;

      google.maps.event.addListener(map, 'idle', () => {
        const center = map.getCenter();
        this.updateMapPositionState({
          center: {
            lat: center.lat(),
            lng: center.lng()
          },
          zoom: map.getZoom()
        });
        this.updatePolygonViewport();
        this.updateFloodPlanes();
      });

      this.updatePolygonViewport(true);
      this.updateFloodPlanes();

      google.maps.event.addListener(map, 'click', function () {
        if (crushed === true) {
          $('#map-toggle').click();
        }
      });

      this.setPolygonDataStyling();

      map.data.addListener("mouseover", (event) => {
        if (event.feature.getProperty("disableEvents")) return false;

        this.polyMouseOverEvent(event.feature, event);
      });

      map.data.addListener("mousemove", (event) => {
        if (event.feature.getProperty("disableEvents")) return false;

        this.polyMouseMoveEvent(event.feature, event);
      });

      map.data.addListener("mouseout", (event) => {
        if (event.feature.getProperty("disableEvents")) return false;

        this.polyMouseOutEvent(event.feature, event);
      });

      map.data.addListener("click", (event) => {
        if (event.feature.getProperty("disableClick")) return false;

        this.polyClickEvent(event.feature, event);
        this.setPolygonDataStyling();
      });

      map.data.addListener("rightclick", (event) => {
        const feature = event.feature;
        const page_url = feature.getProperty('page_url');
        if (feature.getProperty("disableClick")) return false;
        if (page_url === undefined || page_url === null) return false;

        window.location.href = page_url;
      });

      this.mapBusyCount--;
    },
    onCriteriaToggle() {
      if (this.activePanel !== "results") {
        this.toggleCriteria();
      }

      if (window.innerWidth < 992) {
        this.setActivePanel("map");
      }
    },
    updateBikeLayer() {
      this.bikeLayer.setMap(this.toggles.bikeTrails ? map : null);
      this.redraw();

      if (this.toggles.bikeTrails) {
        this.setActiveToggle("bikeTrails");
      }
    },
    toggleSaveSearchPanel() {
      if (!this.$store.state.auth.status.loggedIn) {
        this.$swal({ text: this.$t("favorite.item.alert_login") });
        return;
      }

      this.showSaveSearch = !this.showSaveSearch;
    },
    onMapAction(newPanel) {
      this.showSaveSearch = false;

      if (this.activePanel === newPanel) {
        this.setActivePanel('map');
      } else {
        this.setActivePanel(newPanel);
      }

      if (this.viewCriteria) {
        this.toggleCriteria(false);
      }
    },
    async setPolygonsActiveLevel(newVal, skipZoom, ignorePopup = true) {
      let confirmed = true;

      if (!this.$store.state.auth.status.loggedIn && newVal >= 2 && ignorePopup === false && this.polygonsActiveLevel !== newVal && this.popupDisplayed === false) {
        this.popupDisplayed = true;
        confirmed = await this.showPopupL3Alert();
        // console.log('Showing alert from set polygon active');
      }

      if (confirmed === false) return;

      if (newVal >= 0 && newVal < 3 && newVal !== this.polygonsActiveLevel) {
        this.polygonsActiveLevel = newVal;
        this.$refs.tree?.remove({}, true);

        this.polygonTrunks[newVal].forEach((trunkData) => {
          let trunk = this.$refs.tree.prepend(trunkData);
          trunk.expand();
        });

        if (!skipZoom) {
          let zoom = 10;
          if (newVal === 1) {
            zoom = 13;
          } else if (newVal === 2) {
            zoom = 15;
          }
          map.setZoom(zoom);
        }
      }
    },
    async updatePolygonViewport(initiate = false) {
      let mapBounds = map.getBounds();

      if (!mapBounds) {
        return;
      }

      let currentZoom = map.getZoom();
      let ne = mapBounds.getNorthEast();
      let sw = mapBounds.getSouthWest();
      let z =
        currentZoom >= zoomBreak1 ? (currentZoom >= zoomBreak2 ? 3 : 2) : 1;

      let minLat = sw.lat();
      let maxLat = ne.lat();
      let minLng = ne.lng();
      let maxLng = sw.lng();

      if (
        currentZoom == this.lastZoomUpdate &&
        z == 1 &&
        this.existingPolygonIds.length > 0
      ) {
        return;
      }
      this.lastZoomUpdate = currentZoom;

      let confirmed = true;
      if (!this.$store.state.auth.status.loggedIn && z > 2 && (this.polygonsActiveLevel + 1) !== z && initiate === false && this.popupDisplayed === false) {
        this.popupDisplayed = true;
        confirmed = await this.showPopupL3Alert();
        // console.log('Showing alert from update viewport', z, this.polygonsActiveLevel);
      }
      if (confirmed === false) return;

      // Debounce spinner by 250ms to prevent flicker
      setTimeout(() => {
        this.loadingCount++;
      }, 250);

      try {
        const { data } = await axios.post("/api/polygons/list-points", {
          bounds: [minLat, minLng, maxLat, maxLng],
          excludeList: this.existingPolygonIds,
          disableCulling: window.disableCulling,
          zoom: z,
        })

        data.forEach(this.makePolygon);

        this.redraw();
        this.loadingCount--;
      } catch (err) {
        this.loadingCount--;
        console.log(err.response);
      }
    },
    updateMapPositionState(map) {
        this.saveHistory({ map: map });
    },
    updateSchoolZones() {
      if (!this.toggles.schoolZones) {
        this.redraw();
        return false;
      }

      this.setActiveToggle("schoolZones");

      // Debounce spinner by 250ms to prevent flicker
      setTimeout(() => {
        this.loadingCount++;
      }, 250);

      axios
        .post("/api/school-zones/list", { type: this.toggles.activeSchoolZones })
        .then((response) => {
          map.data.addGeoJson({
            type: "FeatureCollection",
            features: response?.data?.map(item => mapSchools(item)).filter(filterSchools),
          });
          this.redraw();
          this.loadingCount--;
        })
        .catch((err) => {
          this.loadingCount--;
          throw err;
        });

      const mapSchools = (item) => ({
        type: "Feature",
        geometry: item.geometry_json,
        properties: {
          id: item.id,
          title: item.title_short,
          zIndex: -1,
          fillColor: item.color,
          fillOpacity: .8,
          opacityChillin: .8,
          visibilityToggle: "schoolZones",
          type: item.type,
          disableClick: 1,
          disableEvents: 0,
        }
      });

      const filterSchools = (item) => {
        const isPlotted = this.schoolZonesIds.has(item.properties.id);
        this.schoolZonesIds.add(item.properties.id);

        if (!isPlotted && item.geometry?.coordinates) {
          const bounds = new google.maps.LatLngBounds();

          for (let i = 0; i < item.geometry.coordinates[0][0].length; i++) {
            bounds.extend(new google.maps.LatLng(item.geometry.coordinates[0][0][i][1], item.geometry.coordinates[0][0][i][0]));
          }
        }

        return !isPlotted;
      }
    },
    updateFloodPlanes(zone = null) {
      if (!this.toggles.floodPlanes) {
        this.redraw();
        return;
      }

      this.setActiveToggle("floodPlanes");

      if (!this.toggles.activeFloodPlanes) {
        this.toggles.activeFloodPlanes = Object.keys(this.floodZoneLegends)
      }

      if (this.toggles.activeFloodPlanes.includes(zone)) {
        this.toggles.activeFloodPlanes = this.toggles.activeFloodPlanes.filter((item) => item !== zone);
      } else if (zone) {
        this.toggles.activeFloodPlanes.push(zone);
      }

      const mapBounds = map.getBounds();
      if (!mapBounds) return;

      const zoom = map.getZoom();
      const ne = mapBounds.getNorthEast();
      const sw = mapBounds.getSouthWest();

      const minLat = sw.lat();
      const maxLat = ne.lat();
      const minLng = ne.lng();
      const maxLng = sw.lng();
      const bounds = [minLat, minLng, maxLat, maxLng];

      // cancel existing flood data requests to avoid multiple overlapping requests
      if (this.floodDataRequest) {
        this.floodDataRequest = this.floodDataRequest.cancel();
      }

      const CancelToken = axios.CancelToken;
      this.floodDataRequest = CancelToken.source();
      this.floodDataRequestsCount = 0;
      this.fetchFloodData({ bounds, zoom, exclude: Array.from(this.floodPlanesIds), init: true });
    },
    async fetchFloodData({ url = "", bounds = [], zoom = 10, exclude = [], init = false }) {
      if (!this.toggles.floodPlanes) return false;

      try {
        this.floodDataRequestsCount++;
        const response = await axios.post(`/api/flood-zones/list${url}`, {
          exclude,
          flood_zone: this.toggles.activeFloodPlanes,
          bounds,
          zoom,
        }, { cancelToken: this.floodDataRequest.token });

        if (!response.data.data?.length) {
          this.redraw();
          this.floodDataRequest = this.floodDataRequest.cancel();
          this.floodDataRequestsCount--;
          return;
        };

        map.data.addGeoJson({
          type: "FeatureCollection",
          features: response.data.data.map(item => {
            return {
              id: item.id,
              type: "Feature",
              geometry: JSON.parse(item.geometry),
              properties: {
                id: item.id,
                zIndex: -1,
                type: item.flood_zone,
                fillColor: item.color,
                fillOpacity: .8,
                strokeColor: "232323",
                visibilityToggle: "floodPlanes",
                disableClick: 1,
                disableEvents: 1,
              }
            }
          }).filter(item => {
            const isPlotted = this.floodPlanesIds.has(item.properties.id);
            this.floodPlanesIds.add(item.properties.id);
            return !isPlotted;
          }),
        });

        if (response.data.current_page !== response.data.last_page) {
          if (init) {
            for (let i = 2; i < response.data.last_page + 1; i++) {
              this.fetchFloodData({ url: "?page=" + i, bounds, zoom });
            }
          }
        }

        this.redraw();
        this.floodDataRequestsCount--;
      } catch (error) {
        if (!axios.isCancel(error)) {
          this.floodDataRequest = this.floodDataRequest?.cancel();
          this.floodDataRequestsCount--;
        }
      }
    },
    setActiveToggle(toggle) {
      if (this.toggles.initialToggle) {
        this.toggles.initialToggle = false;
        this.toggles.showToggleMessage = true;
      }

      switch (toggle) {
        case "floodPlanes":
          this.toggles.schoolZones = false;
          this.toggles.bikeTrails = false;
          this.updateSchoolZones();
          this.updateBikeLayer();
          break;
        case "schoolZones":
          this.toggles.floodPlanes = false;
          this.toggles.bikeTrails = false;
          this.updateFloodPlanes();
          this.updateBikeLayer();
          break;
        case "bikeTrails":
          this.toggles.schoolZones = false;
          this.toggles.floodPlanes = false;
          this.updateSchoolZones();
          this.updateFloodPlanes();
          break;
      }
    },
  },
  created() {
    EventBus.$on('clearAllFilters', () => {
      this.$refs.tree?.remove({}, true);
      this.selectedPolygons = [];
      this.ancestorPolygons = [];
      this.selectedParents = [];
      this.setPolygonDataStyling();
      this.saveHistory({ property: { map: { selected_polygons: null }}});
    });
    EventBus.$on('searchCriteria', () => this.onMapAction('results'));
    EventBus.$on('saveSearch', () => this.showSaveSearch = false);
    EventBus.$on('resultsCount', (results) => this.totalProperties = results.totalProperties);
    EventBus.$on('applySavedSearch', (criteria) => {
        map.setCenter(criteria.homeData.map.center);
        map.setZoom(criteria.homeData.map.zoom);
        this.selectedPolygons = criteria.polygons;
        this.getPolygonList();
        // this.getPolygonListV2();
    });
  },
  destroyed() {
    EventBus.$off('clearAllFilters');
    EventBus.$off('searchCriteria');
    EventBus.$off('saveSearch');
    EventBus.$off('applySavedSearch');
  },
  mounted() {
    this.toggles = this.homeData.toggles || this.homeMaster.toggles;
    this.initMap();

    this.xSmScreen = window.innerWidth < 576;
    window.addEventListener("resize", () => {
      this.xSmScreen = window.innerWidth < 576;
    });

    let cachedParents = localStorage.getItem("selectedParents");
    if (cachedParents) {
      this.selectedParents = JSON.parse(cachedParents);
    }

    let cachedPolygons = localStorage.getItem("selectedPolygons");
    if (cachedPolygons) {
      this.selectedPolygons = JSON.parse(cachedPolygons);
    }

    let cachedAncestors = localStorage.getItem("ancestorPolygons");
    if (cachedAncestors) {
      this.ancestorPolygons = JSON.parse(cachedAncestors);
    }

    this.getPolygonList();

    axios
        .get("/api/flood-zones/legends")
        .then((response) => {
            this.floodZoneLegends = response.data;
        });

    axios
        .get("/api/school-zones/legends")
        .then((response) => {
            this.schoolZoneLegends = response.data;
        });
  },
};
</script>

<style lang="scss" scoped>
.map-container,
.map-container > .container-fluid {
  width: 100%;
  height: 100%;
}

.map-container > .container-fluid {
  display: flex;
  flex-direction: column;
}

#map-canvas {
  flex-grow: 1;
}

.map-actions {
  position: absolute;
  z-index: 900;
  top: 10px;
  vertical-align: middle;
  pointer-events: none;

  button {
    background: #012e55;
    cursor: pointer;
    user-select: none;
    outline: none;
    box-shadow: none;
    border: 0px solid;
    color: #fff;
    height: 40px;
    pointer-events: all;
    transition: background-color 250ms;
  }
}

.btn-neighborhoods {
  width: 180px;

  &.active {
    width: 180px;
  }

  &.wide {
    width: 250px;
  }
}

.btn-results {
  width: 106px;
}

.btn-save-search {
  width: 173px;
}

.map-actions {
  button.active {
    background: #ffc501;
    color: #012e55;
    font-weight: bold;
  }

  button:not(.active):hover {
    background: #01325d;
  }

  .panel-tabs button {
    text-transform: uppercase;
    margin-right: 10px;
  }

  .panel-tabs .panel-tabs-list {
    display: flex;
    flex-direction: column;
    width: 100%;
    justify-content: space-between;
    align-items: center;
    transition: width 250ms;
  }
}


button {
  &.map-toggle {
    width: 100%;
    position: relative;
    font-size: 1rem;
    padding: 0px 15px;
    height: 40px;

    &::after {
      font-family: "FontAwesome";
      display: flex;
      justify-content: center;
      align-items: center;
      position: absolute;
      top: 0;
      right: 0;
      bottom: 0;
      width: 40px;
      height: 100%;
      background: #12253c;
      color: #ffc501;
      transition: 250ms;
    }

    &:hover::after {
      background: #132942;
    }

    &.static::after {
      display: none;
    }

    &:not(.toggled)::after {
      content: "\f107";
    }

    &.toggled::after {
      content: "\f106";
    }
  }
}

.panel-left {
  display: flex;
  width: 100%;
  margin-left: -10px;
}

.panel-right {
  display: flex;
  width: 100%;

  .toggle-checkbox-rounded:not(:first-child) {
    margin-left: 8px;
  }
}

.toggle-checkbox-rounded {
  width: 33.33%;
  position: relative;

  label {
    position: relative;
    display: inline-block;
    width: 100%;
    height: 38px;
    cursor: pointer;
    pointer-events: all;
    user-select: none;
    background-color: #0a4271;
    transition: width 250ms;
    margin-bottom: 0px;

    input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    .slider {
      display: none;
      position: absolute;
      top: 50%;
      left: 8px;
      transform: translateY(-50%);
      width: 32px;
      height: 14px;
      background-color: #052B4A;
      transition: 300ms;
      border-radius: 7px;

      &:before {
        content: '';
        position: absolute;
        height: 16px;
        width: 16px;
        border-radius: 50%;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        background-color: #fff;
        transition: 300ms;
      }
    }

    .label {
      position: absolute;
      left: 50%;
      top: 50%;
      transform: translate(-50%, -50%);
      display: block;
      transition: 250ms;
      text-align: center;

      &.text {
        width: 100%;
        font-size: 14px;
        font-weight: 700;
        color: rgba(255, 255, 255, 0.5);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }

      &.icon {
        opacity: 0;
        color: rgba(255, 255, 255, 0.65);
        top: calc(50% + 2px);
      }
    }

    &.checked .slider:before {
      background-color: #ffc501;
      transform: translate(16px, -50%);
    }

    &.checked .text {
      color: #ffc501;
    }
  }

  &:not(:first-child):before {
    content: '';
    display: block;
    width: 2px;
    height: 100%;
    position: absolute;
    top: 0;
    left: -1px;
    z-index: 4;
    background-color: #012e55;
  }

  .child-toggles {
    position: absolute;
    left: -100%;
    width: 100vw;
    display: flex;

    &.has-legends {
      label {
        &.checked {
          .text {
            color: #fff;
          }
        }

      }
    }

    .toggle-checkbox-rounded {
      width: 100%;

      &:not(:first-child) {
        margin-left: 0;
      }
    }
  }
}

.toggle-checkbox-fullwidth {
  font-size: 12px;
  display: flex;
  align-items: center;
  background-color: #0a4271;

  label {
    position: relative;
    cursor: pointer;
    pointer-events: all;
    user-select: none;
    transition: width 250ms;
    margin-bottom: 0px;
    width: 100%;
    height: 100%;

    input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    .slider {
      width: 42px;
      height: 16px;
      position: absolute;
      top: 50%;
      left: 30px;
      transform: translateY(-50%);
      border-radius: 8px;
      background-color: #052B4A;
      transition: 300ms;

      &:before {
        content: "";
        position: absolute;
        height: 18px;
        width: 18px;
        border-radius: 50%;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        background-color: #fff;
        transition: 300ms;
      }
    }

    .label {
      position: absolute;
      top: 50%;
      left: 85px;
      transform: translateY(-50%);
      font-weight: 700;
      display: block;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      color: rgba(255, 255, 255, 0.5);
      width: calc(100% - 110px);
    }

    &.checked .slider:before {
      background-color: #ffc501;
      transform: translate(24px, -50%);
    }
  }
}

@media (min-width: 992px) and (max-width: 1800px) {
  .shrink-size {
    .toggle-checkbox-rounded {
      label {
        width: 90px;
      }

      .label.text {
        opacity: 0;
      }

      .label.icon {
        opacity: 1;
      }
    }
  }
}

@media (max-width: 1400px) {
  .map-actions .panel-tabs .panel-tabs-list {
    align-items: flex-start;
  }

  .panel-right {
    .toggle-checkbox-rounded:not(:first-child) {
      margin-left: 0px;
    }
  }
}

@media (min-width: 992px) and (max-width: 1400px) {
  .panel-right {
    flex-direction: column;
  }
  .flood-loader {
    position: relative;
    top: 0;
    left: 0;
  }
}

@media screen and (min-width: 992px) {
  .map-actions .panel-actions {
    flex: 0 0 200px;
    max-width: 200px;
  }

  .map-actions .panel-tabs {
    flex: 0 0 calc(100% - 200px);
    max-width: calc(100% - 200px);
  }

  .map-actions .panel-tabs .panel-tabs-list {
    flex-direction: row;

    &.shrink-size {
      width: calc(100% - 275px);

      @media (min-width: 1200px) {
        width: calc(100% - 300px);
      }

      @media (min-width: 1400px) {
        width: calc(100% - 325px);
      }

      @media (min-width: 1600px) {
        width: calc(100% - 350px);
      }
    }


  }

  .map-actions button {
    font-size: 0.975rem;
  }

  .map-actions button,
  button.map-toggle {
    height: 40px;
  }

  button.map-toggle {
    font-size: 1rem;
    padding: 0px 10px;
  }

  button.map-toggle::after {
    width: 40px;
  }

  .btn-neighborhoods {
    width: 155px;

    &.active {
      width: 155px;
    }

    &.wide {
      width: 225px;
    }
  }

  .btn-results {
    width: 81px;
  }

  .btn-save-search {
    width: 148px;
  }

  .panel-right {
    width: auto;
  }

  .toggle-checkbox-rounded {
    width: auto;

    label {
      width: 160px;
      height: 28px;
      border-radius: 14px;
      margin-bottom: 10px;

      &.active {
        border-radius: 16px 16px 0 0;
        margin-bottom: 0;
      }

      .slider {
        display: block;
      }

      .label {
        left: 46px;
        transform: translateY(-50%);

        &.text {
          text-align: left;
          font-size: 12px;
          width: calc(100% - 70px);
        }
      }

      &.checked .text {
        color: rgba(255, 255, 255, 0.5);
      }
    }

    &:not(:first-child):before {
      display: none;
    }

    .child-toggles {
      position: relative;
      left: 0;
      width: auto;
      display: block;

      &.has-legends {
        label {
          background-color: #0a4271 !important;

          .legend {
            width: 10px;
            height: 10px;
            display: block;
            position: absolute;
            top: 50%;
            left: 20px;
            transform: translateY(-50%);
            padding: 2px;
            outline: 1px gray solid;
            outline-offset: 1px;
          }
        }

        .text {
          left: 45px;
        }
      }

      .toggle-checkbox-rounded {
        &:not(:last-child) {
          label {
            margin: 0;
            border-radius: 0;
          }
        }

        &:last-child {
          label {
            border-radius: 0 0 16px 16px;
          }
        }

        .text {
          font-size: 10px;
        }
      }
    }
  }

}

@media screen and (min-width: 1200px) {
  .map-actions .panel-actions {
    flex: 0 0 275px;
    max-width: 275px;
  }

  .map-actions .panel-tabs {
    flex: 0 0 calc(100% - 275px);
    max-width: calc(100% - 275px);
  }

  .map-actions button,
  button.map-toggle {
    height: 45px;
  }

  button.map-toggle {
    font-size: 1.175rem;
    padding: 0px 15px;
  }

  button.map-toggle::after {
    width: 45px;
  }

  .btn-neighborhoods {
    width: 160px;

    &.active {
      width: 160px;
    }

    &.wide {
      width: 230px;
    }
  }

  .btn-results {
    width: 86px;
  }

  .btn-save-search {
    width: 153px;
  }

  .toggle-checkbox-rounded {
    label {
      height: 32px;
      border-radius: 16px;

      .slider {
        width: 42px;
        height: 16px;
        border-radius: 8px;

        &:before {
          height: 18px;
          width: 18px;
        }
      }

      .label {
        left: 58px;
      }

      &.checked .slider:before {
        transform: translate(24px, -50%);
      }
    }
  }
}

@media screen and (min-width: 1400px) {
  .map-actions .panel-actions {
    flex: 0 0 300px;
    max-width: 300px;
  }

  .map-actions .panel-tabs {
    flex: 0 0 calc(100% - 300px);
    max-width: calc(100% - 300px);
  }

  .map-actions button,
  button.map-toggle {
    height: 50px;
  }

  button.map-toggle {
    font-size: 1.25rem;
    padding: 0px 20px;
  }

  button.map-toggle::after {
    width: 50px;
  }

  .btn-neighborhoods {
    width: 160px;

    &.active {
      width: 160px;
    }

    &.wide {
      width: 250px;
    }
  }

  .btn-results {
    width: 92px;
  }

  .btn-save-search {
    width: 156px;
  }

  .toggle-checkbox-rounded {
    .child-toggles {
      position: absolute;
    }
  }
}

@media screen and (min-width: 1600px) {
  .map-actions button,
  button.map-toggle {
    height: 55px;
  }

  button.map-toggle {
    font-size: 1.25rem;
  }

  button.map-toggle::after {
    width: 55px;
  }
}

@media screen and (max-width: 575px) {
  .map-actions .panel-actions > div button,
  .map-actions .panel-tabs button {
    font-size: 1.125rem;
  }
}

@media screen and (max-width: 991px) {
  .map-actions {
    position: static;

  }

  .map-actions > div {
    padding: 0;
    display: flex;
  }

  .map-actions .panel-left {
    margin-left: 0;
  }

  .map-actions .panel-tabs button {
    margin: 0;
    width: 50%;
    text-transform: unset;
  }

  .map-actions .panel-actions,
  .map-actions .panel-tabs {
    display: flex;
    flex: 0 0 100%;
    max-width: 100%;
  }

  .map-actions .panel-actions > div {
    display: flex;
    width: 100%;
    border: 1px solid #12253c;
    border-left: 0px;
    border-right: 0px;
  }

  .map-actions .panel-actions > div button {
    flex: 0 0 50%;
    max-width: 50%;
    height: 50px;
    padding: 0 15px;
    font-size: 1.25rem;
    background: #012e55;
    background: linear-gradient(
      180deg,
      #013b6e,
      #013b6d,
      #013a6c,
      #013869,
      #013665,
      #013462,
      #01325e,
      #01305a,
      #012f57,
      #012d54,
      #012c53,
      #012c52
    );
  }

  .map-actions .panel-actions > div button span {
    display: flex;
    justify-content: center;
    align-items: center;
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    width: 50px;
    height: 100%;
    background: #12253c;
    color: #ffc501;
    transition: 250ms;
  }

  button.map-toggle svg {
    transform: rotate(90deg);
    transition: transform 250ms;
  }

  button.map-toggle.toggled svg {
    transform: rotate(-90deg);
  }

  button.map-toggle::after {
    content: "";
    display: none;
  }

  button.btn-save-search-sm {
    text-align: left;
    pointer-events: all;
    user-select: none;
    outline: none;
    box-shadow: none;
    transition: 250ms;
  }

  button.btn-save-search-sm svg {
    width: 22px;
  }

  .map-actions button.map-toggle.toggled,
  .map-actions button.btn-save-search-sm.is-active {
    background: #ffc501;
    color: #001e38;
  }

  .panel-actions .d-lg-none button {
    flex-grow: 1;
  }

  .panel-actions .d-lg-none.toggled svg {
    transform: rotate(180deg);
  }

  .map-actions .panel-tabs button {
    height: 50px;
    font-size: 1.25rem;
    background: #012e55;
    background: linear-gradient(
      180deg,
      #013b6e,
      #013b6d,
      #013a6c,
      #013869,
      #013665,
      #013462,
      #01325e,
      #01305a,
      #012f57,
      #012d54,
      #012c53,
      #012c52
    );
  }

  .map-actions .panel-tabs button.active {
    background: #ffc501;
    color: #001e38;
    font-weight: normal;
  }

  .navbar.navbar-light .navbar-nav .nav-link {
    padding: 0.75rem 1.5rem;
  }
}
</style>

<style lang="scss">
.neighborhoods-container {
  height: 100%;
  position: absolute;
  right: 0px;
  z-index: 1000;
  transition: transform 250ms;
  transform: translateX(275px);
}

.neighborhoods-container.active {
  transform: translateX(0);
}

.neighborhoods-inner {
  position: relative;
  width: 275px;
  height: 100%;
}

.neighborhoods {
  position: relative;
  display: flex;
  flex-direction: column;
  color: #fff;
  background: #012e55;
  width: 275px;
  height: 100%;
}

.neighborhoods-footer {
  border-top: 1px solid #001e38;
}

.btn-clear-selected {
  display: inline-flex;
  justify-content: flex-start;
  align-items: center;
}

.btn-clear-selected svg {
  color: #ffc501;
  opacity: 0.75;
  width: 1.125rem;
  height: 1.125rem;
  margin-right: 0.5rem;
  position: relative;
  bottom: 1px;
  transition: 250ms;
}

.btn-clear-selected {
  color: #fff;
  transition: 250ms;
}

.btn-clear-selected:hover,
.btn-clear-selected svg {
  color: #ffc501;
  opacity: 1;
  text-decoration: none;
}

.neighborhood-levels {
  display: flex;
  justify-content: space-between;

  > button {
    position: relative;
    background-color: #001e38;
    color: rgba(256, 256, 256, 0.5);
    width: 33.33%;
    height: 100%;
    text-align: center;
    text-transform: uppercase;
    border: none;
    transition: 250ms;

    &.active {
      background-color: #ffc501;
      color: #001e38;
    }

    &:not(.active):hover {
      background: #002241;
    }
  }
}

.selections-tab {
  position: absolute;
  transform: rotate(180deg);
  top: 30%;
  left: -40px;
  background: #012e55;
  background: linear-gradient(270deg, #012e55, #012c51, #012749, #012545);
  writing-mode: vertical-rl;
  color: #fff;
  height: 140px;
  width: 40px;
  display: flex;
  justify-content: center;
  align-items: center;
  border-top-right-radius: 10px;
  border-bottom-right-radius: 10px;
  letter-spacing: 0.025rem;
  cursor: pointer;
}

.selections-tab svg {
  position: relative;
  left: -2px;
  margin-top: 10px;
  color: #ffc501;
  opacity: 0.5;
  width: 18px;
  height: auto;
  transition: 250ms;
}

.selections-tab:hover svg {
  opacity: 1;
}

.selections-tab.toggled svg {
  transform: rotate(180deg);
}

.tree::-webkit-scrollbar {
  width: 8px;
}
.tree::-webkit-scrollbar-thumb {
  border-radius: 10px;
  background-color: rgb(211, 211, 211);
}

.neighborhoods .tree {
  max-height: 100%;
  overflow-x: hidden;
  width: calc(100% + 15px);
  scrollbar-color: gray none;
  scrollbar-width: thin;
}

.neighborhoods .tree.hide-unchecked .tree-node:not(.checked):not(.indeterminate) {
  display: none;
}

.neighborhoods .tree.hide-all-unchecked {
  .tree-node.has-child {
    display: none;

    &.checked,
    &.indeterminate {
      display: flex;
    }
  }

  .tree-node:not(.has-child):not(.checked):not(.indeterminate) {
    display: none;
  }
}

.neighborhoods .tree > .tree-filter-empty,
.neighborhoods .tree > .tree-root {
  padding: 0;
  width: 100%;
}

.neighborhoods .tree .tree-children {
  position: relative;
}

.neighborhoods .tree-node {
  z-index: 1;
}

.neighborhoods .tree .tree-node.expanded .tree-children::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: #001e38;
  z-index: -1;
}

.neighborhoods .tree-anchor {
  color: #ffc501;
  font-size: 18px;
  padding: 0;
  margin-left: 1rem;
}

.neighborhoods .tree-node {
  white-space: normal;
  padding: 5px 0;
}

.neighborhoods .tree-content {
  align-items: flex-start;
  padding: 0.675rem;
}

.neighborhoods .tree-node.selected > .tree-content,
.neighborhoods .tree-node.disabled > .tree-content:hover,
.neighborhoods .tree-node:not(.selected) > .tree-content:hover {
  background: transparent;
}

.neighborhoods .tree-root .expanded.has-child > .tree-content > .tree-anchor {
  font-weight: bold;
}

.neighborhoods .tree-arrow {
  top: -5px;
}

.neighborhoods .tree-arrow.has-child::after {
  content: "";
  border-color: #ffc501;
  transform: rotate(45deg) translateY(-50%) translateX(-5px);
  transition: none;
}

.neighborhoods .tree-arrow.expanded.has-child:after {
  left: 16px;
  transform: rotate(-135deg) translateY(-50%) translateX(6px);
}

.neighborhoods .tree-checkbox {
  width: 15px;
  height: 15px;
  background: transparent;
  border: 1px solid #ffc501;
  top: 3px;
}

.neighborhoods .tree-checkbox::after {
  opacity: 0;
  transition: 0.2s;
}

.neighborhoods .tree-checkbox.checked,
.neighborhoods .tree-checkbox.indeterminate {
  background: transparent;
  border-color: #ffc501;
}

.neighborhoods .tree-checkbox.checked::after,
.neighborhoods .tree-checkbox.indeterminate::after {
  background: #fff;
  border: none;
  width: 6px;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  opacity: 1;
  transition: 0.2s;
}

.neighborhoods .tree-checkbox.checked::after {
  height: 6px;
}

.neighborhoods .tree-checkbox.indeterminate::after {
  right: initial;
  height: 2px;
}

.collapse-enter-active,
.collapse-leave-active {
  transition: font-size 0.25s, margin 0.25s, padding 0.25s, opacity 0.5s 0.25s;
}
.collapse-enter,
.collapse-leave-to {
  font-size: 0;
  margin: 0;
  opacity: 0;
  padding: 0;
  transition: opacity 0.25s, font-size 0.5s 0.25s, margin 0.5s 0.25s,
    padding 0.5s 0.25s;
}

.flood-loader {
  border-radius: 100px;
  background-color: #0a4271;
  color: rgba(255, 255, 255, 0.5);
  padding: 5px 10px;
  font-size: 12px;
  margin-bottom: 10px;
  text-align: center;
  position: absolute;
  top: 40px;
  left: 5px;
  display: flex;
  align-items: center;
  gap: 5px;
}

.loading-spinner {
  display: flex;
  justify-content: center;
  align-items: center;
  position: absolute;
  width: 100%;
  height: 100%;
  text-align: center;
  z-index: 999;
  background-color: rgba(0, 0, 0, 0.4);
}

.loading-spinner-inner {
  width: 100%;
  margin: 0 auto;
}

.map-messages {
  .map-message {
    display: none;
    background: #012e55;
    position: absolute;
    right: 0;
    bottom: 0;
    max-width: 100%;
    width: 100%;
    height: auto;
    z-index: 1000;
    padding: 50px;
    padding-right: 70px;
    color: #fff;
    font-size: 20px;
    transition: right .3s ease-in-out;

    &:last-child {
      display: block;
    }

    &.adjusted {
      right: 400px;
    }

    .close-message {
      position: absolute;
      top: 10px;
      right: 10px;
      cursor: pointer;
    }
  }
}

.map-legends {
  position: absolute;
  left: 50%;
  bottom: 5%;
  transform: translateX(-50%);
  max-width: 100%;
  width: 600px;
  height: auto;
  z-index: 1;
  color: #fff;
  font-size: 20px;

  .map-legend {
    background: #012e55;
    padding: 20px;
    margin-bottom: 10px;

    ul {
      list-style: none;
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      align-items: center;

      li {
        font-size: 12px;
      }

      label {
        margin: 0;
        cursor: pointer;

        &:not(.checked) {
          text-decoration: line-through;
        }
      }

      input {
        display: none;
      }

      span.legend-box {
        border: 1px solid #ccc;
        float: left;
        width: 12px;
        height: 12px;
        margin: 2px;
      }
    }
  }
}

@media screen and (min-width: 768px) {
  .map-legends {
    bottom: 10%;
  }
}

@media screen and (max-width: 991px) {
  .neighborhoods-container {
    top: 50px;
    transform: translateX(100%);
  }

  .neighborhoods-inner {
    width: 100vw;
  }

  .neighborhoods {
    width: 100%;
  }

  .selections-tab {
    position: fixed;
    right: 0;
    left: unset;
    top: 50%;
    background: #012e55;
    background: linear-gradient(
      270deg,
      #013b6e,
      #013b6d,
      #013a6c,
      #013869,
      #013665,
      #013462,
      #01325e,
      #01305a,
      #012f57,
      #012d54,
      #012c53,
      #012c52
    );
    border-top-right-radius: 15px;
    border-bottom-right-radius: 15px;
    letter-spacing: 0.05rem;
    z-index: 1000;
  }

  .map-message {
    width: 100%;
    right: 0;
    bottom: 0;
    padding: 30px;
  }
}

@media screen and (min-width: 992px) {
  .flood-loader {
    position: absolute;
    bottom: 0;
    left: 0;
    height: 30px;
    width: 100%;
  }

  .neighborhoods-container {
    transform: translateX(275px);
  }

  .neighborhoods-inner,
  .neighborhoods {
    width: 275px;
  }

  .selections-tab {
    font-size: 0.95rem;
    height: 140px;
    width: 35px;
    left: -35px;
  }

  .selections-tab svg {
    width: 16px;
  }
}

@media screen and (min-width: 1200px) {
  .map-messages .map-message {
    right: 250px;
    bottom: 30%;
    width: 800px;
  }
  .neighborhoods-container {
    transform: translateX(300px);
  }

  .neighborhoods-inner,
  .neighborhoods {
    width: 300px;
  }

  .selections-tab {
    font-size: 1rem;
    height: 145px;
    width: 40px;
    left: -40px;
  }

  .selections-tab svg {
    width: 18px;
  }
}

@media screen and (min-width: 1400px) {
  .neighborhoods-container {
    transform: translateX(325px);
  }

  .neighborhoods-inner,
  .neighborhoods {
    width: 325px;
  }

  .selections-tab {
    height: 150px;
  }
}

@media screen and (min-width: 1600px) {
  .neighborhoods-container {
    transform: translateX(350px);
  }

  .neighborhoods-inner,
  .neighborhoods {
    width: 350px;
  }
}

/* Right Panel Heights */
.toggle-checkbox-fullwidth {
  height: 50px;
  flex: none;
}

.neighborhoods-footer {
  display: flex;
  align-items: center;
  height: 53px;
  flex: none;
}

.neighborhood-levels {
  flex: none;
}

@media screen and (max-width: 991px) {
  .neighborhood-levels {
    height: 60px;
    font-size: 1.25rem;
  }

  .neighborhoods-content {
    height: calc(100% - 214px);
  }
}

@media screen and (min-width: 992px) {
  .neighborhood-levels {
    height: 40px;
  }

  .neighborhoods-content {
    // 40 + 50 + 53
    height: calc(100% - 143px);
  }
}

@media screen and (min-width: 1200px) {
  .neighborhood-levels {
    height: 45px;
  }

  .neighborhoods-content {
    // 45 + 50 + 53
    height: calc(100% - 148px);
  }
}

@media screen and (min-width: 1400px) {
  .neighborhood-levels {
    height: 50px;
  }

  .neighborhoods-content {
    // 50 + 50 + 53
    height: calc(100% - 153px);
  }
}

@media screen and (min-width: 1600px) {
  .neighborhood-levels {
    height: 55px;
  }

  .neighborhoods-content {
    // 55 + 50 + 53
    height: calc(100% - 158px);
  }
}
</style>
